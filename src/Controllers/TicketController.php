<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;

class TicketController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();
        $tickets = $db->query("
            SELECT t.*, c.first_name, c.last_name, c.email as contact_email, u.name as assigned_name
            FROM support_tickets t
            LEFT JOIN contacts c ON t.contact_id = c.id
            LEFT JOIN users u ON t.assigned_user_id = u.id
            ORDER BY t.created_at DESC
        ")->fetchAll();

        $contacts = $db->query("SELECT id, first_name, last_name, email FROM contacts ORDER BY first_name ASC LIMIT 100")->fetchAll();
        $users = $db->query("SELECT id, name FROM users WHERE is_active = 1")->fetchAll();

        View::render('tickets/index', [
            'title' => 'Customer Support Helpdesk & Tickets — Dream Career IT',
            'tickets' => $tickets,
            'contacts' => $contacts,
            'users' => $users,
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function store(Request $request): void
    {
        $db = Database::pdo();
        $subject = trim((string)$request->input('subject', ''));
        $description = trim((string)$request->input('description', ''));
        $priority = trim((string)$request->input('priority', 'medium'));
        $contactId = (int)$request->input('contact_id', 0) ?: null;
        $assignedId = (int)$request->input('assigned_user_id', 0) ?: Session::get('user_id');

        if ($subject === '' || $description === '') {
            Session::setFlash('error', 'Ticket Subject and Description are required.');
            Response::redirect('/tickets');
            return;
        }

        $ticketNum = 'DCIT-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $stmt = $db->prepare("
            INSERT INTO support_tickets (ticket_number, contact_id, assigned_user_id, subject, description, status, priority, channel)
            VALUES (?, ?, ?, ?, ?, 'open', ?, 'portal')
        ");
        $stmt->execute([$ticketNum, $contactId, $assignedId, $subject, $description, $priority]);
        $tid = (int)$db->lastInsertId();

        Logger::audit('ticket_created', 'SupportTicket', $tid, null, ['ticket_number' => $ticketNum]);
        Session::setFlash('success', "Support Ticket {$ticketNum} opened.");
        Response::redirect('/tickets');
    }

    public function updateStatus(Request $request): void
    {
        $id = (int)$request->input('ticket_id', 0);
        $status = trim((string)$request->input('status', 'open'));
        if ($id > 0 && in_array($status, ['open', 'pending', 'resolved', 'closed'], true)) {
            Database::pdo()->prepare("UPDATE support_tickets SET status = ? WHERE id = ?")->execute([$status, $id]);
            Logger::audit('ticket_status_updated', 'SupportTicket', $id, null, ['status' => $status]);
            Session::setFlash('success', 'Ticket status updated.');
        }
        Response::redirect('/tickets');
    }
}
