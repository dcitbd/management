<?php
declare(strict_types=1);

namespace App\Core;

class Config
{
    private static array $settings = [];

    public static function load(string $basePath): void
    {
        $envFile = rtrim($basePath, '/\\') . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    $value = trim($value, '"\'');
                    self::$settings[$key] = $value;
                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                }
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$settings)) {
            return self::$settings[$key];
        }
        $val = getenv($key);
        return $val !== false ? $val : $default;
    }
}
