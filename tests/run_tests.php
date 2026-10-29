<?php
declare(strict_types=1);

/**
 * Dream Career IT — AI Business Suite
 * Automated Verification & Security Test Suite
 * Usage: php tests/run_tests.php
 */

require_once __DIR__ . '/../src/Core/Config.php';
require_once __DIR__ . '/../src/Core/Session.php';
require_once __DIR__ . '/../src/Core/Csrf.php';
require_once __DIR__ . '/../src/Core/RateLimiter.php';
require_once __DIR__ . '/../src/Controllers/TemplateController.php';
require_once __DIR__ . '/../src/Services/MailerService.php';
require_once __DIR__ . '/../src/Services/AiAssistantService.php';

use App\Core\Config;
use App\Controllers\TemplateController;
use App\Services\MailerService;
use App\Services\AiAssistantService;

echo "=========================================================
";
echo "  Dream Career IT — Automated Security & Unit Test Suite 
";
echo "=========================================================

";

$passed = 0;
$failed = 0;

function assertCheck(string $name, bool $condition): void {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$name}
";
        $passed++;
    } else {
        echo "[FAIL] {$name}
";
        $failed++;
    }
}

// 1. Test Password Hashing & Verification (BCrypt)
$hash = password_hash('StrongSecret#2026', PASSWORD_BCRYPT, ['cost' => 12]);
assertCheck('BCrypt password_hash() & password_verify() validation', password_verify('StrongSecret#2026', $hash) && !password_verify('WrongPass', $hash));

// 2. Test HTML Email Template Sanitization (strips <script>, inline handlers, and PHP tags)
$dirtyHtml = '<div>Hello {{name}}<script>alert("xss")</script><?php system("id"); ?><a href="javascript:evil()" onclick="steal()">Link</a></div>';
$cleanHtml = TemplateController::sanitizeTemplateHtml($dirtyHtml);
assertCheck(
    'HTML Sanitizer removes <script>, <?php, onclick, and javascript: URIs',
    !str_contains($cleanHtml, '<script>') && !str_contains($cleanHtml, '<?php') && !str_contains($cleanHtml, 'onclick=') && !str_contains($cleanHtml, 'javascript:')
);

// 3. Test Dynamic Placeholder Replacement ({{name}}, {{email}}, {{company}}, {{unsubscribe_url}})
$merged = MailerService::renderMergeTags('Hi {{name}} from {{company}} ({{email}}) - {{unsubscribe_url}}', [
    'name' => 'Jainal Abedin',
    'company' => 'Dream Career IT',
    'email' => 'jainal.dcitbd@gmail.com'
]);
assertCheck(
    'MailerService::renderMergeTags replaces {{name}}, {{company}}, {{email}}, and generates signed {{unsubscribe_url}}',
    str_contains($merged, 'Jainal Abedin') && str_contains($merged, 'Dream Career IT') && str_contains($merged, '/unsubscribe?email=')
);

// 4. Test AI Assistant Service (English & Bangla generation)
$aiEn = AiAssistantService::generate('email_draft', 'Custom ERP Software', 'English');
$aiBn = AiAssistantService::generate('email_draft', 'Custom ERP Software', 'Bangla');
assertCheck('AI Assistant generates structured English & Bangla business copy', $aiEn['success'] && $aiBn['success'] && str_contains($aiBn['content'], 'Dream Career IT'));

echo "
---------------------------------------------------------
";
echo "Total Passed: {$passed} | Total Failed: {$failed}
";
exit($failed > 0 ? 1 : 0);
