<?php
declare(strict_types=1);

namespace App\Services;

require_once __DIR__ . '/../Core/Config.php';
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/MailerService.php';

use PDO;
use App\Core\Config;
use App\Core\Database;

class EmailQueueWorker
{
    private PDO $db;
    private int $batchSize;

    public function __construct()
    {
        Config::load(__DIR__ . '/../..');
        $this->db = Database::pdo();
        $this->batchSize = (int)Config::get('QUEUE_MAX_BATCH_SIZE', 50);
    }

    public function processBatch(): array
    {
        $summary = ['processed' => 0, 'sent' => 0, 'failed' => 0, 'suppressed' => 0];

        $stmt = $this->db->prepare("
            SELECT q.*, c.sender_name, c.sender_email, c.status as campaign_status,
                   cr.name as recipient_name, cr.company as recipient_company
            FROM email_queue q
            JOIN campaigns c ON q.campaign_id = c.id
            JOIN campaign_recipients cr ON q.recipient_id = cr.id
            WHERE q.status IN ('queued', 'retry')
              AND (q.next_retry_at IS NULL OR q.next_retry_at <= NOW())
              AND q.attempts < q.max_attempts
              AND c.status IN ('queued', 'processing')
            ORDER BY q.id ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $this->batchSize, PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll();

        if (empty($jobs)) {
            return $summary;
        }

        foreach ($jobs as $job) {
            $summary['processed']++;
            $jobId = (int)$job['id'];
            $campaignId = (int)$job['campaign_id'];
            $recipientId = (int)$job['recipient_id'];
            $email = strtolower(trim($job['to_email']));

            $this->db->prepare("UPDATE campaigns SET status = 'processing', started_at = COALESCE(started_at, NOW()) WHERE id = ? AND status = 'queued'")
                     ->execute([$campaignId]);

            if ($this->isSuppressed($email)) {
                $this->db->prepare("UPDATE email_queue SET status = 'failed', last_error = 'Suppressed or Unsubscribed' WHERE id = ?")->execute([$jobId]);
                $this->db->prepare("UPDATE campaign_recipients SET status = 'suppressed', error_message = 'Recipient on suppression list' WHERE id = ?")->execute([$recipientId]);
                $this->db->prepare("UPDATE campaigns SET queued_count = GREATEST(0, queued_count - 1), failed_count = failed_count + 1 WHERE id = ?")->execute([$campaignId]);
                $summary['suppressed']++;
                continue;
            }

            $this->db->prepare("UPDATE email_queue SET status = 'processing', attempts = attempts + 1 WHERE id = ?")->execute([$jobId]);

            $mergeVars = [
                'name' => $job['recipient_name'] ?: $job['to_name'],
                'email' => $email,
                'company' => $job['recipient_company'] ?? ''
            ];
            $renderedHtml = MailerService::renderMergeTags($job['body_html'], $mergeVars);
            $renderedPlain = MailerService::renderMergeTags((string)$job['body_plain'], $mergeVars);
            $renderedSubject = MailerService::renderMergeTags($job['subject'], $mergeVars);

            $res = MailerService::send(
                $email,
                (string)$job['to_name'],
                $renderedSubject,
                $renderedHtml,
                $renderedPlain,
                $job['sender_email'],
                $job['sender_name']
            );

            if ($res['success']) {
                $this->db->prepare("
                    UPDATE email_queue
                    SET status = 'completed', provider_message_id = ?, last_error = NULL
                    WHERE id = ?
                ")->execute([$res['provider_message_id'], $jobId]);

                $this->db->prepare("UPDATE campaign_recipients SET status = 'sent' WHERE id = ?")->execute([$recipientId]);

                $this->db->prepare("
                    INSERT INTO email_logs (campaign_id, recipient_email, subject, provider, provider_message_id, status, response_code, response_message, sent_at)
                    VALUES (?, ?, ?, 'smtp', ?, 'sent', ?, ?, NOW())
                ")->execute([$campaignId, $email, $renderedSubject, $res['provider_message_id'], $res['code'], $res['message']]);

                $this->db->prepare("
                    UPDATE campaigns
                    SET queued_count = GREATEST(0, queued_count - 1),
                        sent_count = sent_count + 1
                    WHERE id = ?
                ")->execute([$campaignId]);

                $summary['sent']++;
            } else {
                $attempts = (int)$job['attempts'] + 1;
                $maxAttempts = (int)$job['max_attempts'];
                $nextStatus = ($attempts >= $maxAttempts) ? 'failed' : 'retry';
                $backoffMinutes = pow(2, $attempts) * 5;

                $this->db->prepare("
                    UPDATE email_queue
                    SET status = ?, last_error = ?, next_retry_at = DATE_ADD(NOW(), INTERVAL ? MINUTE)
                    WHERE id = ?
                ")->execute([$nextStatus, $res['message'], $backoffMinutes, $jobId]);

                if ($nextStatus === 'failed') {
                    $this->db->prepare("UPDATE campaign_recipients SET status = 'failed', error_message = ? WHERE id = ?")
                             ->execute([$res['message'], $recipientId]);
                    $this->db->prepare("UPDATE campaigns SET queued_count = GREATEST(0, queued_count - 1), failed_count = failed_count + 1 WHERE id = ?")
                             ->execute([$campaignId]);
                }

                $this->db->prepare("
                    INSERT INTO email_logs (campaign_id, recipient_email, subject, provider, status, response_code, response_message)
                    VALUES (?, ?, ?, 'smtp', 'failed', ?, ?)
                ")->execute([$campaignId, $email, $renderedSubject, $res['code'], $res['message']]);

                $summary['failed']++;
            }
        }

        $this->db->exec("
            UPDATE campaigns c
            SET c.status = 'completed', c.completed_at = NOW()
            WHERE c.status = 'processing'
              AND NOT EXISTS (
                  SELECT 1 FROM email_queue q
                  WHERE q.campaign_id = c.id AND q.status IN ('queued', 'processing', 'retry')
              )
        ");

        return $summary;
    }

    private function isSuppressed(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM suppression_list WHERE email = ?");
        $stmt->execute([$email]);
        if ((int)$stmt->fetchColumn() > 0) {
            return true;
        }

        $stmt2 = $this->db->prepare("SELECT is_subscribed FROM contacts WHERE email = ?");
        $stmt2->execute([$email]);
        $sub = $stmt2->fetchColumn();
        if ($sub !== false && (int)$sub === 0) {
            return true;
        }

        return false;
    }
}

if (php_sapi_name() === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    $worker = new EmailQueueWorker();
    $once = in_array('--once', $argv ?? [], true);
    echo "[i] Starting Dream Career IT EmailQueueWorker...\n";
    do {
        $result = $worker->processBatch();
        if ($result['processed'] > 0) {
            echo sprintf(
                "[%s] Processed: %d | Sent: %d | Failed: %d | Suppressed: %d\n",
                date('H:i:s'),
                $result['processed'],
                $result['sent'],
                $result['failed'],
                $result['suppressed']
            );
        }
        if ($once) break;
        sleep((int)Config::get('QUEUE_SLEEP_SECONDS', 5));
    } while (true);
}
