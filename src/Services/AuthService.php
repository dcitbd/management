<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Session;
use App\Core\RateLimiter;
use App\Core\Logger;
use App\Models\User;

class AuthService
{
    public static function attempt(string $email, string $password, string $ip): array
    {
        $email = strtolower(trim($email));
        $rateKey = "login_" . md5($ip . '_' . $email);

        if (!RateLimiter::check($rateKey, 5, 900)) {
            return [
                'success' => false,
                'message' => 'Too many failed login attempts. Please wait 15 minutes before trying again.'
            ];
        }

        $user = User::findByEmail($email);

        if (!$user) {
            RateLimiter::hit($rateKey, 900);
            Logger::audit('login_failed', 'User', null, null, ['email' => $email, 'reason' => 'user_not_found']);
            return ['success' => false, 'message' => 'Invalid email address or password.'];
        }

        if ((int)$user['is_active'] !== 1) {
            Logger::audit('login_blocked', 'User', (int)$user['id'], null, ['reason' => 'account_disabled']);
            return ['success' => false, 'message' => 'This account has been disabled. Please contact the administrator.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            RateLimiter::hit($rateKey, 900);
            Logger::audit('login_failed', 'User', (int)$user['id'], null, ['email' => $email, 'reason' => 'invalid_password']);
            return ['success' => false, 'message' => 'Invalid email address or password.'];
        }

        // Login success: Clear rate limiter
        RateLimiter::clear($rateKey);

        // Regenerate session id to protect against session fixation attacks
        Session::start();
        Session::regenerate();

        // Store user in session
        Session::set('user_id', (int)$user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role_id', (int)$user['role_id']);
        Session::set('user_role_name', $user['role_name']);
        Session::set('user_role_slug', $user['role_slug']);
        Session::set('theme_preference', $user['theme_preference'] ?? 'dark');
        Session::set('user_permissions', User::getUserPermissions((int)$user['role_id']));

        // Record update and audit
        User::updateLastLogin((int)$user['id'], $ip);
        Logger::audit('login_success', 'User', (int)$user['id']);

        return ['success' => true, 'user' => $user];
    }

    public static function logout(): void
    {
        Session::start();
        $userId = Session::get('user_id');
        if ($userId) {
            Logger::audit('logout', 'User', (int)$userId);
        }
        Session::destroy();
    }
}
