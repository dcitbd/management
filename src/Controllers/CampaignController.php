<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;
use App\Services\EmailQueueWorker;
use App\Controllers\TemplateController;

class CampaignController
{
    public function showSend(Request $request): void
    {
        $db = Database::pdo();
        $templates = $db->query("SELECT id, name, category, subject, sender_name, sender_email, body_html FROM email_templates WHERE is_archived = 0 ORDER BY name ASC")->fetchAll();
        $lists = $db->query("SELECT * FROM contact_lists ORDER BY name ASC")->fetchAll();
        $activeContactsCount = (int)$db->query("SELECT COUNT(*) FROM contacts WHERE is_subscribed = 1")->fetchColumn();

        View::render('campaigns/send', [
            'title' => 'Dispatch Bulk Email Campaign — Dream Career IT',
            'templates' => $templates,
            'lists' => $lists,
            'activeContactsCount' => $activeContactsCount,
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function enqueueCampaign(Request $request): void
    {
        $db = Database::pdo();
        $name = trim((string)$request->input('name', ''));
        $subject = trim((string)$request->input('subject', ''));
        $senderName = trim((string)$request->input('sender_name', 'Dream Career IT Bangladesh'));
        $senderEmail = trim((string)$request->input('sender_email', 'info@dreamcareerit.com'));
        $templateId = (int)$request->input('template_id', 0) ?: null;
        $rawHtml = (string)$request->input('body_html', '');
        $pastedRecipients = (string)$request->input('pasted_recipients', '');
        $includeAllSubscribed = (int)$request->input('include_crm_subscribed', 0) === 1;
        $scheduledFor = trim((string)$request->input('scheduled_for', '')) ?: null;

        if ($name === '' || $subject === '' || trim($rawHtml) === '') {
            Session::setFlash('error', 'Campaign Name, Subject, and HTML Content are required.');
            Response::redirect('/campaigns/send');
            return;
        }

        $safeHtml = TemplateController::sanitizeTemplateHtml($rawHtml);
        $plainText = trim(strip_tags($safeHtml));

        $rawCandidates = [];

        if (trim($pastedRecipients) !== '') {
            $lines = preg_split('/\r\n|\r|\n/', trim($pastedRecipients));
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $parts = preg_split('/[\t,;]+/', $line);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if (filter_var($part, FILTER_VALIDATE_EMAIL)) {
                        $rawCandidates[] = [
                            'email' => strtolower($part),
                            'name' => $parts[0] !== $part ? trim($parts[0]) : '',
                            'company' => $parts[2] ?? ''
                        ];
                    }
                }
            }
        }

        if (!empty($_FILES['csv_file']['tmp_name']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            $fh = fopen($_FILES['csv_file']['tmp_name'], 'r');
            if ($fh !== false) {
                while (($row = fgetcsv($fh)) !== false) {
                    foreach ($row as $cell) {
                        $cell = trim((string)$cell);
                        if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                            $rawCandidates[] = [
                                'email' => strtolower($cell),
                                'name' => isset($row[0]) && $row[0] !== $cell ? trim((string)$row[0]) : '',
                                'company' => isset($row[2]) ? trim((string)$row[2]) : ''
                            ];
                            break;
                        }
                    }
                }
                fclose($fh);
            }
        }

        if ($includeAllSubscribed) {
            $crmRows = $db->query("SELECT first_name, last_name, email, company FROM contacts WHERE is_subscribed = 1")->fetchAll();
            foreach ($crmRows as $cr) {
                $rawCandidates[] = [
                    'email' => strtolower(trim($cr['email'])),
                    'name' => trim($cr['first_name'] . ' ' . $cr['last_name']),
                    'company' => (string)$cr['company']
                ];
            }
        }

        if (empty($rawCandidates)) {
            Session::setFlash('error', 'No valid email addresses were provided via paste, CSV upload, or CRM selection.');
            Response::redirect('/campaigns/send');
            return;
        }

        $suppressedMap = [];
        $supRows = $db->query("SELECT email FROM suppression_list")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($supRows as $se) {
            $suppressedMap[strtolower($se)] = true;
        }
        $unsubRows = $db->query("SELECT email FROM contacts WHERE is_subscribed = 0")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($unsubRows as $ue) {
            $suppressedMap[strtolower($ue)] = true;
        }

        $uniqueValid = [];
        $suppressedRecipients = [];
        foreach ($rawCandidates as $cand) {
            $em = $cand['email'];
            if (isset($uniqueValid[$em]) || isset($suppressedRecipients[$em])) {
                continue;
            }
            if (isset($suppressedMap[$em])) {
                $suppressedRecipients[$em] = $cand;
            } else {
                $uniqueValid[$em] = $cand;
            }
        }

        $totalValid = count($uniqueValid);
        $totalSuppressed = count($suppressedRecipients);

        $db->beginTransaction();
        try {
            $initialStatus = $scheduledFor ? 'scheduled' : 'queued';
            $campStmt = $db->prepare("
                INSERT INTO campaigns (template_id, name, subject, sender_name, sender_email, body_html, body_plain, status, total_recipients, queued_count, scheduled_for, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $campStmt->execute([
                $templateId, $name, $subject, $senderName, $senderEmail,
                $safeHtml, $plainText, $initialStatus,
                $totalValid + $totalSuppressed, $totalValid, $scheduledFor, Session::get('user_id')
            ]);
            $campaignId = (int)$db->lastInsertId();

            $recStmt = $db->prepare("
                INSERT INTO campaign_recipients (campaign_id, email, name, company, status, error_message)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $queueStmt = $db->prepare("
                INSERT INTO email_queue (campaign_id, recipient_id, to_email, to_name, subject, body_html, body_plain, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'queued')
            ");

            foreach ($uniqueValid as $em => $info) {
                $recStmt->execute([$campaignId, $em, $info['name'], $info['company'], 'queued', null]);
                $recId = (int)$db->lastInsertId();
                $queueStmt->execute([$campaignId, $recId, $em, $info['name'], $subject, $safeHtml, $plainText]);
            }

            foreach ($suppressedRecipients as $em => $info) {
                $recStmt->execute([$campaignId, $em, $info['name'], $info['company'], 'suppressed', 'Skipped: Present on Suppression/Unsubscribe List']);
            }

            $db->commit();
            Logger::audit('campaign_queued', 'Campaign', $campaignId, null, [
                'valid_queued' => $totalValid,
                'suppressed_skipped' => $totalSuppressed
            ]);

            Session::setFlash(
                'success',
                "Campaign '{$name}' created! Queued {$totalValid} unique verified recipients ({$totalSuppressed} suppressed addresses automatically filtered)."
            );
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::setFlash('error', 'Failed to enqueue campaign: ' . $e->getMessage());
            Response::redirect('/campaigns/send');
            return;
        }

        Response::redirect('/campaigns/logs');
    }

    public function logs(Request $request): void
    {
        $db = Database::pdo();
        $tab = trim((string)$request->input('tab', 'campaigns'));

        $campaigns = $db->query("SELECT * FROM campaigns ORDER BY created_at DESC LIMIT 50")->fetchAll();
        $queueItems = $db->query("SELECT * FROM email_queue ORDER BY id DESC LIMIT 100")->fetchAll();
        $deliveryLogs = $db->query("SELECT * FROM email_logs ORDER BY id DESC LIMIT 100")->fetchAll();
        $suppressed = $db->query("SELECT * FROM suppression_list ORDER BY id DESC LIMIT 100")->fetchAll();

        View::render('campaigns/logs', [
            'title' => 'Campaign Queue, Delivery & Bounce Logs — Dream Career IT',
            'tab' => $tab,
            'campaigns' => $campaigns,
            'queueItems' => $queueItems,
            'deliveryLogs' => $deliveryLogs,
            'suppressed' => $suppressed,
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function processQueueNow(Request $request): void
    {
        $worker = new EmailQueueWorker();
        $res = $worker->processBatch();
        Session::setFlash(
            'success',
            "Worker Batch Executed: Processed {$res['processed']} | Sent: {$res['sent']} | Failed: {$res['failed']} | Suppressed: {$res['suppressed']}"
        );
        Response::redirect('/campaigns/logs?tab=queue');
    }

    public function pauseOrCancel(Request $request): void
    {
        $id = (int)$request->input('campaign_id', 0);
        $action = (string)$request->input('action', 'pause');
        $db = Database::pdo();

        if ($id > 0 && in_array($action, ['paused', 'cancelled', 'queued'], true)) {
            $db->prepare("UPDATE campaigns SET status = ? WHERE id = ?")->execute([$action, $id]);
            Logger::audit("campaign_{$action}", 'Campaign', $id);
            Session::setFlash('success', "Campaign #{$id} status updated to " . strtoupper($action) . ".");
        }
        Response::redirect('/campaigns/logs');
    }
}
