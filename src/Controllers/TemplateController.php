<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;

class TemplateController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();
        $q = trim((string)$request->input('q', ''));
        $category = trim((string)$request->input('category', ''));

        $sql = "SELECT * FROM email_templates WHERE is_archived = 0";
        $params = [];

        if ($q !== '') {
            $sql .= " AND (name LIKE ? OR subject LIKE ?)";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }
        if ($category !== '') {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY updated_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $templates = $stmt->fetchAll();

        View::render('templates/index', [
            'title' => 'Email Template Builder & Library — Dream Career IT',
            'templates' => $templates,
            'q' => $q,
            'category' => $category,
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function create(Request $request): void
    {
        View::render('templates/editor', [
            'title' => 'Create Email Template — Dream Career IT',
            'template' => null,
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function edit(Request $request, array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $stmt = Database::pdo()->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch();

        if (!$template) {
            Session::setFlash('error', 'Template not found.');
            Response::redirect('/templates');
            return;
        }

        View::render('templates/editor', [
            'title' => 'Edit Email Template — ' . $template['name'],
            'template' => $template,
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public static function sanitizeTemplateHtml(string $html): string
    {
        $clean = preg_replace('/<\?(?:php|=)?.*?\?>/is', '', $html);
        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $clean);
        $clean = preg_replace('/\bon[a-z]+\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+)/i', '', $clean);
        $clean = preg_replace('/href\s*=\s*["']\s*javascript:[^"']*["']/i', 'href="#"', $clean);
        return $clean;
    }

    public function save(Request $request): void
    {
        $db = Database::pdo();
        $id = (int)$request->input('id', 0);
        $name = trim((string)$request->input('name', ''));
        $category = trim((string)$request->input('category', 'Marketing'));
        $subject = trim((string)$request->input('subject', ''));
        $senderName = trim((string)$request->input('sender_name', 'Dream Career IT Bangladesh'));
        $senderEmail = trim((string)$request->input('sender_email', 'info@dreamcareerit.com'));
        $rawHtml = (string)$request->input('body_html', '');
        $plainText = trim((string)$request->input('body_plain', ''));

        if ($name === '' || $subject === '' || trim($rawHtml) === '') {
            Session::setFlash('error', 'Template Name, Subject, and HTML Body are required.');
            Response::redirect($id > 0 ? "/templates/edit/{$id}" : '/templates/create');
            return;
        }

        $safeHtml = self::sanitizeTemplateHtml($rawHtml);
        if ($plainText === '') {
            $plainText = trim(strip_tags($safeHtml));
        }

        if ($id > 0) {
            $stmt = $db->prepare("
                UPDATE email_templates
                SET category = ?, name = ?, subject = ?, body_html = ?, body_plain = ?, sender_name = ?, sender_email = ?
                WHERE id = ?
            ");
            $stmt->execute([$category, $name, $subject, $safeHtml, $plainText, $senderName, $senderEmail, $id]);
            Logger::audit('template_updated', 'EmailTemplate', $id, null, ['name' => $name]);
            Session::setFlash('success', "Template '{$name}' updated safely.");
        } else {
            $stmt = $db->prepare("
                INSERT INTO email_templates (category, name, subject, body_html, body_plain, sender_name, sender_email, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$category, $name, $subject, $safeHtml, $plainText, $senderName, $senderEmail, Session::get('user_id')]);
            $newId = (int)$db->lastInsertId();
            Logger::audit('template_created', 'EmailTemplate', $newId, null, ['name' => $name]);
            Session::setFlash('success', "Template '{$name}' created and sanitized.");
        }

        Response::redirect('/templates');
    }

    public function duplicate(Request $request): void
    {
        $id = (int)$request->input('id', 0);
        $db = Database::pdo();
        $stmt = $db->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $tpl = $stmt->fetch();

        if ($tpl) {
            $copyName = $tpl['name'] . ' (Copy)';
            $ins = $db->prepare("
                INSERT INTO email_templates (category, name, subject, body_html, body_plain, sender_name, sender_email, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $ins->execute([
                $tpl['category'], $copyName, $tpl['subject'], $tpl['body_html'],
                $tpl['body_plain'], $tpl['sender_name'], $tpl['sender_email'], Session::get('user_id')
            ]);
            Session::setFlash('success', "Duplicated template as '{$copyName}'.");
        }
        Response::redirect('/templates');
    }

    public function delete(Request $request): void
    {
        $id = (int)$request->input('id', 0);
        if ($id > 0) {
            $stmt = Database::pdo()->prepare("UPDATE email_templates SET is_archived = 1 WHERE id = ?");
            $stmt->execute([$id]);
            Logger::audit('template_archived', 'EmailTemplate', $id);
            Session::setFlash('success', 'Template moved to archive.');
        }
        Response::redirect('/templates');
    }
}
