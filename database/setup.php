<?php
declare(strict_types=1);

/**
 * Dream Career IT — AI Business Suite
 * CLI Setup & Migration Tool
 * 
 * Usage: php database/setup.php [--create-admin]
 */

require_once __DIR__ . '/../src/Core/Config.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../database/seeders/DatabaseSeeder.php';

use App\Core\Config;
use App\Core\Database;
use App\Database\Seeders\DatabaseSeeder;

echo "=========================================================\n";
echo "  Dream Career IT — AI Business Suite Database Setup    \n";
echo "  CEO: Jainal Abedin | Dream Career IT Bangladesh       \n";
echo "=========================================================\n\n";

// Load configuration
Config::load(__DIR__ . '/..');

try {
    $db = Database::getInstance()->getConnection();
    echo "[✓] Successfully connected to database.\n";

    // Run migration
    echo "[i] Executing migration: 001_create_initial_schema.sql...\n";
    $sql = file_get_contents(__DIR__ . '/migrations/001_create_initial_schema.sql');
    $db->exec($sql);
    echo "[✓] Schema tables created successfully.\n";

    // Run Seeder
    echo "[i] Running database seeders (Roles, Services, System Settings)...\n";
    $seeder = new DatabaseSeeder($db);
    $seeder->run();
    echo "[✓] Base catalog and system seed data initialized without duplicates.\n";

    // Admin Creation Prompt
    $adminCount = $db->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'admin'")->fetchColumn();

    if ($adminCount > 0) {
        echo "[i] An Administrator account already exists. Skipping administrator creation.\n";
    } else {
        echo "\n--- Secure Initial Administrator Setup ---\n";
        echo "No default credentials are provided out of the box for security.\n";
        
        $name = "Jainal Abedin";
        $email = "jainal.dcitbd@gmail.com";
        $phone = "01581703822";

        if (php_sapi_name() === 'cli') {
            echo "Enter Admin Full Name [$name]: ";
            $inputName = trim(fgets(STDIN));
            if (!empty($inputName)) $name = $inputName;

            echo "Enter Admin Email [$email]: ";
            $inputEmail = trim(fgets(STDIN));
            if (!empty($inputEmail)) $email = $inputEmail;

            echo "Enter Admin Secure Password (min 8 chars): ";
            // Hide or read password
            $password = trim(fgets(STDIN));
            while (strlen($password) < 8) {
                echo "Password must be at least 8 characters. Please re-enter: ";
                $password = trim(fgets(STDIN));
            }
        } else {
            // Non-interactive fallback
            $password = bin2hex(random_bytes(8)) . '!A1';
            echo "[!] Non-interactive shell detected. Generated random admin password: $password\n";
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $roleId = $db->query("SELECT id FROM roles WHERE slug = 'admin'")->fetchColumn();

        $stmt = $db->prepare("
            INSERT INTO users (role_id, name, email, phone, password_hash, is_active, theme_preference)
            VALUES (?, ?, ?, ?, ?, 1, 'dark')
        ");
        $stmt->execute([$roleId, $name, $email, $phone, $passwordHash]);

        echo "\n[✓] Administrator created successfully!\n";
        echo "    Name:  $name\n";
        echo "    Email: $email\n";
        echo "    Role:  Administrator\n";
    }

    echo "\n[✓] Database initialization complete!\n";

} catch (Throwable $e) {
    echo "\n[X] Setup Failed: " . $e->getMessage() . "\n";
    echo "    In file: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    exit(1);
}
