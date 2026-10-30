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
use App\Services\MailerService;
use App\Controllers\TemplateController;

class CampaignController
{
    public function showSend(Request $request): void
    {
        $db = Database::pdo();
        $templates = $db->query("SELECT id, name, category, subject, sender_name, sender_email, body_html FROM email_templates WHERE is_archived = 0 ORDER BY id ASC")->fetchAll();
        $lists = $db->query("SELECT * FROM contact_lists ORDER BY name ASC")->fetchAll();
        $activeContactsCount = (int)$db->query("SELECT COUNT(*) FROM contacts WHERE is_subscribed = 1")->fetchColumn();
        $selectedTemplateId = (int)$request->input('template_id', 0);

        View::render('campaigns/send', [
            'title' => 'Bulk Email Auto-Sender — Dream Career IT',
            'templates' => $templates,
            'selectedTemplateId' => $selectedTemplateId,
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
        $templateId = (int)$request->input('template_id', 0) ?: null;
        $name = trim((string)$request->input('name', ''));
        $subject = trim((string)$request->input('subject', ''));
        $senderName = trim((string)$request->input('sender_name', 'Dream Career IT Bangladesh'));
        $senderEmail = trim((string)$request->input('sender_email', 'info@dreamcareerit.com'));
        $rawHtml = (string)$request->input('body_html', '');
        $pastedRecipients = (string)$request->input('pasted_recipients', '');
        $includeAllSubscribed = (int)$request->input('include_crm_subscribed', 0) === 1;
        $scheduledFor = trim((string)$request->input('scheduled_for', '')) ?: null;

        // If a template is selected, automatically load its Subject, HTML Body, and Sender details if not overridden
        if ($templateId !== null && $templateId > 0) {
            $tplStmt = $db->prepare("SELECT * FROM email_templates WHERE id = ?");
            $tplStmt->execute([$templateId]);
            $tpl = $tplStmt->fetch();
            if ($tpl) {
                if ($name === '') {
                    $name = $tpl['name'] . ' — Bulk Auto-Send (' . date('M d, H:i') . ')';
                }
                if ($subject === '') {
                    $subject = (string)$tpl['subject'];
                }
                if (trim($rawHtml) === '') {
                    $rawHtml = (string)$tpl['body_html'];
                }
                if ($senderName === '' && !empty($tpl['sender_name'])) {
                    $senderName = (string)$tpl['sender_name'];
                }
                if ($senderEmail === '' && !empty($tpl['sender_email'])) {
                    $senderEmail = (string)$tpl['sender_email'];
                }
            }
        }

        if ($name === '') {
            $name = 'Bulk Auto-Send Campaign — ' . date('M d, H:i');
        }

        if ($subject === '' || trim($rawHtml) === '') {
            Session::setFlash('error', 'Please select a template or provide an Email Subject and HTML Body.');
            Response::redirect('/campaigns/send');
            return;
        }

        $safeHtml = TemplateController::sanitizeTemplateHtml($rawHtml);
        $plainText = trim(strip_tags($safeHtml));

        $rawCandidates = MailerService::extractEmailsFromText($pastedRecipients);

        if (!empty($_FILES['csv_file']['tmp_name']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            $csvContent = (string)file_get_contents($_FILES['csv_file']['tmp_name']);
            $csvExtracted = MailerService::extractEmailsFromText($csvContent);
            foreach ($csvExtracted as $ce) {
                $rawCandidates[] = $ce;
            }
        }

        if ($includeAllSubscribed) {
            $crmRows = $db->query("SELECT first_name, last_name, email, company FROM contacts WHERE is_subscribed = 1")->fetchAll();
            foreach ($crmRows as $cr) {
                $em = strtolower(trim((string)$cr['email']));
                if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
                    $rawCandidates[] = [
                        'email' => $em,
                        'name' => trim($cr['first_name'] . ' ' . $cr['last_name']) ?: 'Valued Client',
                        'company' => (string)$cr['company']
                    ];
                }
            }
        }

        if (empty($rawCandidates)) {
            Session::setFlash('error', 'No valid email addresses were found. Please paste recipient email IDs, upload a CSV, or select CRM contacts.');
            Response::redirect('/campaigns/send');
            return;
        }

        $suppressedMap = [];
        $supRows = $db->query("SELECT email FROM suppression_list")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($supRows as $se) {
            $suppressedMap[strtolower((string)$se)] = true;
        }
        $unsubRows = $db->query("SELECT email FROM contacts WHERE is_subscribed = 0")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($unsubRows as $ue) {
            $suppressedMap[strtolower((string)$ue)] = true;
        }

        $uniqueValid = [];
        $suppressedRecipients = [];
        foreach ($rawCandidates as $cand) {
            $em = strtolower(trim($cand['email']));
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

            // Auto-Sender: Immediately process and send all emails in this campaign
            $sentNow = 0;
            if (!$scheduledFor && $totalValid > 0) {
                $worker = new EmailQueueWorker();
                do {
                    $batchRes = $worker->processBatch($campaignId);
                    $sentNow += (int)($batchRes['sent'] ?? 0);
                } while (($batchRes['processed'] ?? 0) > 0);
            }

            Logger::audit('bulk_auto_send_completed', 'Campaign', $campaignId, null, [
                'valid_queued' => $totalValid,
                'auto_sent_now' => $sentNow,
                'suppressed_skipped' => $totalSuppressed
            ]);

            if ($request->isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => true,
                    'campaign_id' => $campaignId,
                    'campaign_name' => $name,
                    'sent_count' => $sentNow,
                    'total_valid' => $totalValid,
                    'suppressed_count' => $totalSuppressed
                ]);
                return;
            }

            $recipientListPreview = implode(', ', array_slice(array_keys($uniqueValid), 0, 5));
            if (count($uniqueValid) > 5) {
                $recipientListPreview .= ' +' . (count($uniqueValid) - 5) . ' more';
            }

            Session::setFlash(
                'success',
                "Auto-Sender Completed! Campaign '{$name}' automatically sent to {$sentNow}/{$totalValid} recipient(s) ({$recipientListPreview})."
            );
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if ($request->isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                return;
            }
            Session::setFlash('error', 'Auto-Sender failed: ' . $e->getMessage());
            Response::redirect('/campaigns/send');
            return;
        }

        Response::redirect('/campaigns/logs');
    }

    /**
     * Direct JSON API endpoint for the Bulk Auto-Sender Engine
     */
    public function apiSendEmail(Request $request): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $templateId = (int)$request->input('template_id', 0);
        $subject = trim((string)$request->input('subject', ''));
        $rawHtml = (string)$request->input('body_html', '');
        $senderName = trim((string)$request->input('sender_name', 'Dream Career IT Bangladesh'));
        $senderEmail = trim((string)$request->input('sender_email', 'info@dreamcareerit.com'));
        $recipientsRaw = $request->input('recipients', '');

        if ($templateId > 0 && ($subject === '' || trim($rawHtml) === '')) {
            try {
                $db = Database::pdo();
                $stmt = $db->prepare("SELECT * FROM email_templates WHERE id = ?");
                $stmt->execute([$templateId]);
                $tpl = $stmt->fetch();
                if ($tpl) {
                    if ($subject === '') $subject = (string)$tpl['subject'];
                    if (trim($rawHtml) === '') $rawHtml = (string)$tpl['body_html'];
                }
            } catch (\Throwable $e) {}
        }

        if ($subject === '') {
            $subject = 'Important Update from Dream Career IT Bangladesh';
        }

        $smtpOverride = [
            'smtp_host' => $request->input('smtp_host'),
            'smtp_port' => $request->input('smtp_port'),
            'smtp_user' => $request->input('smtp_user'),
            'smtp_pass' => $request->input('smtp_pass'),
            'smtp_encryption' => $request->input('smtp_encryption'),
        ];

        $candidates = [];
        if (is_array($recipientsRaw)) {
            foreach ($recipientsRaw as $item) {
                if (is_string($item) && filter_var(trim($item), FILTER_VALIDATE_EMAIL)) {
                    $candidates[] = ['email' => strtolower(trim($item)), 'name' => 'Valued Client', 'company' => ''];
                } elseif (is_array($item) && !empty($item['email']) && filter_var(trim((string)$item['email']), FILTER_VALIDATE_EMAIL)) {
                    $candidates[] = [
                        'email' => strtolower(trim((string)$item['email'])),
                        'name' => trim((string)($item['name'] ?? 'Valued Client')),
                        'company' => trim((string)($item['company'] ?? ''))
                    ];
                }
            }
        } else {
            $candidates = MailerService::extractEmailsFromText((string)$recipientsRaw);
        }

        if (empty($candidates)) {
            echo json_encode([
                'success' => false,
                'message' => 'No valid recipient email addresses provided.',
                'results' => []
            ]);
            return;
        }

        $safeHtml = TemplateController::sanitizeTemplateHtml($rawHtml);
        $plainText = trim(strip_tags($safeHtml));
        $results = [];
        $sentCount = 0;

        foreach ($candidates as $cand) {
            $renderedHtml = MailerService::renderMergeTags($safeHtml, $cand);
            $renderedPlain = MailerService::renderMergeTags($plainText, $cand);
            $renderedSubject = MailerService::renderMergeTags($subject, $cand);

            $res = MailerService::send(
                $cand['email'],
                $cand['name'],
                $renderedSubject,
                $renderedHtml,
                $renderedPlain,
                $senderEmail,
                $senderName,
                $smtpOverride
            );

            if ($res['success']) {
                $sentCount++;
            }

            $results[] = [
                'email' => $cand['email'],
                'name' => $cand['name'],
                'company' => $cand['company'],
                'status' => $res['success'] ? 'delivered' : 'failed',
                'provider_message_id' => $res['provider_message_id'],
                'message' => $res['message'],
                'transport' => $res['transport'] ?? 'auto_smtp'
            ];
        }

        echo json_encode([
            'success' => $sentCount > 0,
            'sent_count' => $sentCount,
            'total_count' => count($candidates),
            'results' => $results
        ]);
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
            "Auto-Sender Batch Executed: Processed {$res['processed']} | Sent: {$res['sent']} | Failed: {$res['failed']} | Suppressed: {$res['suppressed']}"
        );
        Response::redirect('/campaigns/logs?tab=queue');
    }

    public function pauseOrCancel(Request $request): void
    {
        $id = (int)$request->input('campaign_id', 0);
        $action = (string)$request->input('action', 'pause');
        $db = Database::pdo();

        if ($id > 0 && $action === 'delete') {
            $db->prepare("DELETE FROM campaigns WHERE id = ?")->execute([$id]);
            Logger::audit("campaign_deleted", 'Campaign', $id);
            Session::setFlash('success', "Campaign #{$id} deleted.");
        } elseif ($id > 0 && in_array($action, ['paused', 'cancelled', 'queued'], true)) {
            $db->prepare("UPDATE campaigns SET status = ? WHERE id = ?")->execute([$action, $id]);
            Logger::audit("campaign_{$action}", 'Campaign', $id);
            Session::setFlash('success', "Campaign #{$id} status updated to " . strtoupper($action) . ".");
        }
        Response::redirect('/campaigns/logs');
    }
}
