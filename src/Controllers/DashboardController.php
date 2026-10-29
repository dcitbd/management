<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Session;

class DashboardController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();

        // Real Database Analytics Queries
        $stats = [
            'total_contacts' => (int)$db->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
            'total_campaigns' => (int)$db->query("SELECT COUNT(*) FROM campaigns")->fetchColumn(),
            'emails_queued' => (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE status = 'queued'")->fetchColumn(),
            'emails_sent' => (int)$db->query("SELECT COUNT(*) FROM email_logs WHERE status IN ('sent', 'delivered', 'accepted')")->fetchColumn(),
            'emails_delivered' => (int)$db->query("SELECT COUNT(*) FROM email_logs WHERE status = 'delivered'")->fetchColumn(),
            'emails_failed' => (int)$db->query("SELECT COUNT(*) FROM email_logs WHERE status = 'failed'")->fetchColumn(),
            'emails_bounced' => (int)$db->query("SELECT COUNT(*) FROM email_logs WHERE status = 'bounced'")->fetchColumn(),
            'unsubscribed' => (int)$db->query("SELECT COUNT(*) FROM contacts WHERE is_subscribed = 0")->fetchColumn(),
            'total_templates' => (int)$db->query("SELECT COUNT(*) FROM email_templates WHERE is_archived = 0")->fetchColumn(),
            'total_services' => (int)$db->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn(),
            'connected_channels' => (int)$db->query("SELECT COUNT(*) FROM social_accounts WHERE is_connected = 1")->fetchColumn(),
            'open_conversations' => (int)$db->query("SELECT COUNT(*) FROM conversations WHERE status = 'open'")->fetchColumn(),
        ];

        // Recent campaign activity
        $recentCampaigns = $db->query("
            SELECT id, name, status, total_recipients, sent_count, delivered_count, failed_count, created_at
            FROM campaigns
            ORDER BY created_at DESC
            LIMIT 5
        ")->fetchAll();

        // Recent messages
        $recentMessages = $db->query("
            SELECT m.*, c.channel, c.title as conversation_title
            FROM messages m
            JOIN conversations c ON m.conversation_id = c.id
            ORDER BY m.created_at DESC
            LIMIT 5
        ")->fetchAll();

        // Recent audit/login logs
        $recentActivity = $db->query("
            SELECT a.*, u.name as user_name, u.email as user_email
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT 6
        ")->fetchAll();

        View::render('dashboard/index', [
            'title' => 'Executive Instrument Panel — Dream Career IT',
            'stats' => $stats,
            'recentCampaigns' => $recentCampaigns,
            'recentMessages' => $recentMessages,
            'recentActivity' => $recentActivity,
            'user' => [
                'name' => Session::get('user_name'),
                'email' => Session::get('user_email'),
                'role' => Session::get('user_role_name'),
                'theme' => Session::get('theme_preference', 'dark')
            ]
        ], 'main');
    }
}
