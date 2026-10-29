<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Core\Database;

class User
{
    public static function findById(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("
            SELECT u.*, r.name as role_name, r.slug as role_slug 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare("
            SELECT u.*, r.name as role_name, r.slug as role_slug 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = ?
        ");
        $stmt->execute([strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function updateLastLogin(int $userId, string $ip): void
    {
        $stmt = Database::pdo()->prepare("
            UPDATE users 
            SET last_login_at = NOW(), last_login_ip = ?
            WHERE id = ?
        ");
        $stmt->execute([$ip, $userId]);
    }

    public static function getUserPermissions(int $roleId): array
    {
        $stmt = Database::pdo()->prepare("
            SELECT p.slug
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = ?
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}
