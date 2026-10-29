<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;

class MessageController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();
        $channelFilter = trim((string)$request->input('channel', ''));
        $statusFilter = trim((string)$request->input('status', ''));
        $activeConvId = (int)$request->input('conversation_id', 0);

        $where = ["1=1"];
        $params = [];
        if ($channelFilter !== '') {
            $where[] = "c.channel = ?";
            $params[] = $channelFilter;
        }
        if ($statusFilter !== '') {
            $where[] = "c.status = ?";
            $params[] = $statusFilter;
        }

        $sql = "
            SELECT c.*, ct.first_name, ct.last_name, ct.email as contact_email, u.name as assigned_name
            FROM conversations c
            LEFT JOIN contacts ct ON c.contact_id = ct.id
            LEFT JOIN users u ON c.assigned_user_id = u.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY COALESCE(c.last_message_at, c.created_at) DESC
            LIMIT 50
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $conversations = $stmt->fetchAll();

        if ($activeConvId === 0 && !empty($conversations)) {
            $activeConvId = (int)$conversations[0]['id'];
        }

        $activeConversation = null;
        $messages = [];
        if ($activeConvId > 0) {
            $cStmt = $db->prepare("
                SELECT c.*, ct.first_name, ct.last_name, ct.email as contact_email, ct.phone as contact_phone
                FROM conversations c
                LEFT JOIN contacts ct ON c.contact_id = ct.id
                WHERE c.id = ?
            ");
            $cStmt->execute([$activeConvId]);
            $activeConversation = $cStmt->fetch();

            $mStmt = $db->prepare("SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC");
            $mStmt->execute([$activeConvId]);
            $messages = $mStmt->fetchAll();
        }

        $accounts = $db->query("SELECT * FROM social_accounts ORDER BY provider ASC")->fetchAll();
        $users = $db->query("SELECT id, name FROM users WHERE is_active = 1")->fetchAll();

        View::render('messages/index', [
            'title' => 'Unified Omnichannel Message Center — Dream Career IT',
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'accounts' => $accounts,
            'users' => $users,
            'filters' => ['channel' => $channelFilter, 'status' => $statusFilter],
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function createConversation(Request $request): void
    {
        $db = Database::pdo();
        $title = trim((string)$request->input('title', 'New Inquiry'));
        $channel = trim((string)$request->input('channel', 'facebook'));
        $initialMsg = trim((string)$request->input('message_text', ''));

        $stmt = $db->prepare("
            INSERT INTO conversations (channel, title, status, assigned_user_id, last_message_at)
            VALUES (?, ?, 'open', ?, NOW())
        ");
        $stmt->execute([$channel, $title, Session::get('user_id')]);
        $convId = (int)$db->lastInsertId();

        if ($initialMsg !== '') {
            $mStmt = $db->prepare("
                INSERT INTO messages (conversation_id, sender_type, sender_id, message_text, delivery_status)
                VALUES (?, 'contact', 'External Client', ?, 'delivered')
            ");
            $mStmt->execute([$convId, $initialMsg]);
        }

        Logger::audit('conversation_started', 'Conversation', $convId, null, ['channel' => $channel]);
        Session::setFlash('success', 'Conversation thread initialized.');
        Response::redirect("/messages?conversation_id={$convId}");
    }

    public function sendReply(Request $request): void
    {
        $db = Database::pdo();
        $convId = (int)$request->input('conversation_id', 0);
        $text = trim((string)$request->input('message_text', ''));
        $internalNote = trim((string)$request->input('internal_notes', ''));

        if ($convId <= 0) {
            Response::redirect('/messages');
            return;
        }

        if ($internalNote !== '') {
            $db->prepare("UPDATE conversations SET internal_notes = ? WHERE id = ?")->execute([$internalNote, $convId]);
        }

        if ($text !== '') {
            $stmt = $db->prepare("
                INSERT INTO messages (conversation_id, sender_type, sender_id, message_text, delivery_status)
                VALUES (?, 'agent', ?, ?, 'sent')
            ");
            $stmt->execute([$convId, Session::get('user_name', 'Agent'), $text]);
            $db->prepare("UPDATE conversations SET last_message_at = NOW() WHERE id = ?")->execute([$convId]);
            Logger::audit('message_reply_sent', 'Conversation', $convId);
            Session::setFlash('success', 'Reply recorded and dispatched via channel adapter.');
        }

        Response::redirect("/messages?conversation_id={$convId}");
    }
}
