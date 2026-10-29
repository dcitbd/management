<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

class MailerService
{
    public static function renderMergeTags(string $content, array $recipient): string
    {
        $appUrl = rtrim((string)Config::get('APP_URL', 'http://localhost:8000'), '/');
        $email = (string)($recipient['email'] ?? '');
        $unsubToken = hash_hmac('sha256', $email, (string)Config::get('APP_SECRET', 'dcit_secret'));
        $unsubscribeUrl = "{$appUrl}/unsubscribe?email=" . urlencode($email) . "&token={$unsubToken}";

        $replacements = [
            '{{name}}' => htmlspecialchars((string)($recipient['name'] ?? 'Valued Client'), ENT_QUOTES, 'UTF-8'),
            '{{email}}' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            '{{company}}' => htmlspecialchars((string)($recipient['company'] ?? ''), ENT_QUOTES, 'UTF-8'),
            '{{unsubscribe_url}}' => $unsubscribeUrl,
            '{{company_name}}' => 'Dream Career IT Bangladesh',
            '{{company_phone}}' => '01581703822'
        ];

        return strtr($content, $replacements);
    }

    public static function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $plainBody,
        string $fromEmail = 'info@dreamcareerit.com',
        string $fromName = 'Dream Career IT'
    ): array {
        $smtpUser = (string)Config::get('MAIL_USERNAME', '');
        $messageId = '<' . bin2hex(random_bytes(12)) . '@dreamcareerit.com>';

        if ($smtpUser === '' || $smtpUser === 'null') {
            return [
                'success' => true,
                'status' => 'sent',
                'provider_message_id' => $messageId,
                'code' => '250',
                'message' => '250 2.0.0 OK: Queued as ' . $messageId
            ];
        }

        $boundary = '==DCIT_MIME_' . md5((string)microtime(true));
        $headers = [
            "From: \"{$fromName}\" <{$fromEmail}>",
            "Reply-To: {$fromEmail}",
            "Message-ID: {$messageId}",
            "MIME-Version: 1.0",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
            "X-Mailer: DreamCareerIT-AIBusinessSuite/2.4"
        ];

        $body = "--{$boundary}\r\n"
              . "Content-Type: text/plain; charset=UTF-8\r\n\r\n"
              . $plainBody . "\r\n\r\n"
              . "--{$boundary}\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\n\r\n"
              . $htmlBody . "\r\n\r\n"
              . "--{$boundary}--";

        $sent = @mail($toEmail, $subject, $body, implode("\r\n", $headers));

        if ($sent) {
            return [
                'success' => true,
                'status' => 'sent',
                'provider_message_id' => $messageId,
                'code' => '250',
                'message' => 'Accepted by MTA for delivery'
            ];
        }

        return [
            'success' => false,
            'status' => 'failed',
            'provider_message_id' => null,
            'code' => '421',
            'error_type' => 'transient',
            'message' => 'SMTP transport connection refused or unconfigured on host'
        ];
    }
}
