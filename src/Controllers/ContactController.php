<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;

class ContactController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();
        $q = trim((string)$request->input('q', ''));
        $status = trim((string)$request->input('lead_status', ''));
        $subscribed = $request->input('is_subscribed');
        $page = max(1, (int)$request->input('page', 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $where = ["1=1"];
        $params = [];

        if ($q !== '') {
            $where[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR company LIKE ?)";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }
        if ($status !== '') {
            $where[] = "lead_status = ?";
            $params[] = $status;
        }
        if ($subscribed !== null && $subscribed !== '') {
            $where[] = "is_subscribed = ?";
            $params[] = (int)$subscribed;
        }

        $whereSql = implode(' AND ', $where);

        $countStmt = $db->prepare("SELECT COUNT(*) FROM contacts WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $db->prepare("SELECT * FROM contacts WHERE {$whereSql} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}");
        $stmt->execute($params);
        $contacts = $stmt->fetchAll();

        $lists = $db->query("SELECT * FROM contact_lists ORDER BY name ASC")->fetchAll();

        View::render('contacts/index', [
            'title' => 'Contacts & CRM Management — Dream Career IT',
            'contacts' => $contacts,
            'lists' => $lists,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'filters' => ['q' => $q, 'lead_status' => $status, 'is_subscribed' => $subscribed],
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function store(Request $request): void
    {
        $db = Database::pdo();
        $email = strtolower(trim((string)$request->input('email', '')));
        $firstName = trim((string)$request->input('first_name', ''));
        $lastName = trim((string)$request->input('last_name', ''));
        $phone = trim((string)$request->input('phone', ''));
        $company = trim((string)$request->input('company', ''));
        $leadStatus = trim((string)$request->input('lead_status', 'lead'));
        $consentSource = trim((string)$request->input('consent_source', 'CRM Manual Opt-in'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please enter a valid email address.');
            Response::redirect('/contacts');
            return;
        }

        $stmt = $db->prepare("
            INSERT INTO contacts (first_name, last_name, email, phone, company, source, lead_status, is_subscribed)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
                first_name = COALESCE(NULLIF(VALUES(first_name), ''), first_name),
                last_name = COALESCE(NULLIF(VALUES(last_name), ''), last_name),
                phone = COALESCE(NULLIF(VALUES(phone), ''), phone),
                company = COALESCE(NULLIF(VALUES(company), ''), company),
                lead_status = VALUES(lead_status)
        ");
        $stmt->execute([$firstName, $lastName, $email, $phone, $company, $consentSource, $leadStatus]);

        $contactId = (int)$db->lastInsertId();
        if ($contactId > 0) {
            $db->prepare("INSERT INTO email_consents (contact_id, consent_source, ip_address, consent_type, status) VALUES (?, ?, ?, 'opt_in', 'granted')")
               ->execute([$contactId, $consentSource, $request->getIp()]);
        }

        Logger::audit('contact_saved', 'Contact', $contactId ?: null, null, ['email' => $email]);
        Session::setFlash('success', "Contact {$email} saved with consent audit record.");
        Response::redirect('/contacts');
    }

    public function exportCsv(Request $request): void
    {
        $db = Database::pdo();
        $rows = $db->query("SELECT first_name, last_name, email, phone, company, lead_status, is_subscribed, created_at FROM contacts ORDER BY id DESC")->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dreamcareerit_contacts_' . date('Ymd_His') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Lead Status', 'Subscribed', 'Created At']);
        foreach ($rows as $r) {
            fputcsv($out, $r);
        }
        fclose($out);
        exit;
    }
}
