<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;

class SettingController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();
        $rows = $db->query("SELECT setting_key, setting_value, is_encrypted FROM settings")->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            // Never expose raw encrypted secrets in full
            $settings[$r['setting_key']] = $r['is_encrypted'] ? '••••••••••••' : $r['setting_value'];
        }

        $socialAccounts = $db->query("SELECT * FROM social_accounts ORDER BY id ASC")->fetchAll();

        View::render('settings/index', [
            'title' => 'System Settings, SMTP, AI & Integrations — Dream Career IT',
            'settings' => $settings,
            'socialAccounts' => $socialAccounts,
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function save(Request $request): void
    {
        $db = Database::pdo();
        $allowedKeys = [
            'app_name', 'company_name', 'company_ceo', 'company_phone_1', 'company_phone_2',
            'company_email', 'company_shop_url', 'smtp_host', 'smtp_port', 'smtp_encryption',
            'rate_limit_per_minute', 'default_provider', 'max_tokens'
        ];

        $stmt = $db->prepare("
            INSERT INTO settings (setting_group, setting_key, setting_value)
            VALUES ('general', ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        foreach ($allowedKeys as $key) {
            $val = $request->input($key);
            if ($val !== null) {
                $stmt->execute([$key, trim((string)$val)]);
            }
        }

        Logger::audit('settings_updated', 'Settings');
        Session::setFlash('success', 'System and provider settings updated safely.');
        Response::redirect('/settings');
    }

    public function users(Request $request): void
    {
        $db = Database::pdo();
        $users = $db->query("
            SELECT u.*, r.name as role_name, r.slug as role_slug
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.id ASC
        ")->fetchAll();
        $roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();

        View::render('settings/users', [
            'title' => 'User Management & RBAC — Dream Career IT',
            'users' => $users,
            'roles' => $roles,
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function storeUser(Request $request): void
    {
        $db = Database::pdo();
        $name = trim((string)$request->input('name', ''));
        $email = strtolower(trim((string)$request->input('email', '')));
        $phone = trim((string)$request->input('phone', ''));
        $roleId = (int)$request->input('role_id', 3);
        $password = (string)$request->input('password', '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            Session::setFlash('error', 'Name, valid Email, and Password (min 8 characters) are required.');
            Response::redirect('/users');
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        try {
            $stmt = $db->prepare("
                INSERT INTO users (role_id, name, email, phone, password_hash, is_active)
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$roleId, $name, $email, $phone, $hash]);
            Logger::audit('user_created', 'User', (int)$db->lastInsertId(), null, ['email' => $email, 'role_id' => $roleId]);
            Session::setFlash('success', "User account for {$name} created.");
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Could not create user (email may already exist).');
        }
        Response::redirect('/users');
    }

    public function auditLogs(Request $request): void
    {
        $db = Database::pdo();
        $logs = $db->query("
            SELECT a.*, u.name as user_name, u.email as user_email
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.id DESC
            LIMIT 150
        ")->fetchAll();

        View::render('settings/audit-logs', [
            'title' => 'Security & Activity Audit Logs — Dream Career IT',
            'logs' => $logs,
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }
}
