<?php
declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const TOKEN_KEY = '_csrf_token';

    public static function generateToken(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set(self::TOKEN_KEY, $token);
        return $token;
    }

    public static function getToken(): string
    {
        Session::start();
        if (!Session::has(self::TOKEN_KEY)) {
            return self::generateToken();
        }
        return (string)Session::get(self::TOKEN_KEY);
    }

    public static function validate(?string $token): bool
    {
        Session::start();
        $sessionToken = Session::get(self::TOKEN_KEY);
        if (!$sessionToken || !$token) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
    }
}
