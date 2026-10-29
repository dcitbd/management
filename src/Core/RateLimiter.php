<?php
declare(strict_types=1);

namespace App\Core;

class RateLimiter
{
    public static function check(string $key, int $maxAttempts = 5, int $decaySeconds = 900): bool
    {
        Session::start();
        $attempts = Session::get("rate_limit_{$key}", []);
        $now = time();

        // Prune expired attempts
        $attempts = array_filter($attempts, fn($timestamp) => ($now - $timestamp) < $decaySeconds);

        if (count($attempts) >= $maxAttempts) {
            return false;
        }

        return true;
    }

    public static function hit(string $key, int $decaySeconds = 900): void
    {
        Session::start();
        $attempts = Session::get("rate_limit_{$key}", []);
        $now = time();
        $attempts = array_filter($attempts, fn($timestamp) => ($now - $timestamp) < $decaySeconds);
        $attempts[] = $now;
        Session::set("rate_limit_{$key}", $attempts);
    }

    public static function clear(string $key): void
    {
        Session::start();
        Session::remove("rate_limit_{$key}");
    }
}
