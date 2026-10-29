<?php
declare(strict_types=1);

/**
 * Dream Career IT — AI Business Suite
 * Front Controller & Routing Gateway (All 6 Phases Integrated)
 *
 * Designer & Director: Jainal Abedin (CEO, Dream Career IT Bangladesh)
 * Contact: 01581703822, 01818273838
 * Email: jainal.dcitbd@gmail.com
 * Shop: https://dreamcartbd.com
 */

// Zero-dependency PSR-4 Autoloader for App\ namespace
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Config;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ServiceController;
use App\Controllers\TemplateController;
use App\Controllers\ContactController;
use App\Controllers\CampaignController;
use App\Controllers\MessageController;
use App\Controllers\AiController;
use App\Controllers\ProjectController;
use App\Controllers\TicketController;
use App\Controllers\SettingController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;

// Load environment variables & initialize hardened session
Config::load(__DIR__ . '/..');
Session::start();

$request = new Request();
$router = new Router();

// ----------------------------------------------------------
// 1. Guest / Public Authentication Routes
// ----------------------------------------------------------
$router->get('/', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class, CsrfMiddleware::class]);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], [GuestMiddleware::class]);
$router->post('/forgot-password', [AuthController::class, 'processForgotPassword'], [GuestMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------------------------
// 2. Protected Executive Dashboard & Account Routes
// ----------------------------------------------------------
$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
$router->get('/profile', [SettingController::class, 'users'], [AuthMiddleware::class]);

// ----------------------------------------------------------
// 3. Service Catalog & Management Routes (10 Categories, 28+ Services)
// ----------------------------------------------------------
$router->get('/services', [ServiceController::class, 'index'], [AuthMiddleware::class]);
$router->post('/services/store', [ServiceController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/services/update', [ServiceController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/services/delete', [ServiceController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------------------------
// 4. Email Template Builder & Sanitized HTML Editor Routes
// ----------------------------------------------------------
$router->get('/templates', [TemplateController::class, 'index'], [AuthMiddleware::class]);
$router->get('/templates/create', [TemplateController::class, 'create'], [AuthMiddleware::class]);
$router->get('/templates/edit/{id}', [TemplateController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/templates/save', [TemplateController::class, 'save'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/templates/duplicate', [TemplateController::class, 'duplicate'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/templates/delete', [TemplateController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------------------------
// 5. Bulk Email Campaigns, CSV/Paste Import & Queue Worker Routes
// ----------------------------------------------------------
$router->get('/campaigns/send', [CampaignController::class, 'showSend'], [AuthMiddleware::class]);
$router->post('/campaigns/enqueue', [CampaignController::class, 'enqueueCampaign'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/campaigns/logs', [CampaignController::class, 'logs'], [AuthMiddleware::class]);
$router->post('/campaigns/process-queue', [CampaignController::class, 'processQueueNow'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/campaigns/control', [CampaignController::class, 'pauseOrCancel'], [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------------------------
// 6. Contacts, Client CRM & CSV Export Routes
// ----------------------------------------------------------
$router->get('/contacts', [ContactController::class, 'index'], [AuthMiddleware::class]);
$router->post('/contacts/store', [ContactController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/contacts/export', [ContactController::class, 'exportCsv'], [AuthMiddleware::class]);

// ----------------------------------------------------------
// 7. Unified Omnichannel Message Center Routes
// ----------------------------------------------------------
$router->get('/messages', [MessageController::class, 'index'], [AuthMiddleware::class]);
$router->post('/messages/create', [MessageController::class, 'createConversation'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/messages/reply', [MessageController::class, 'sendReply'], [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------------------------
// 8. AI Business Assistant (Bangla & English) Routes
// ----------------------------------------------------------
$router->get('/ai-assistant', [AiController::class, 'index'], [AuthMiddleware::class]);
$router->post('/ai-assistant/generate', [AiController::class, 'generate'], [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------------------------
// 9. Projects, Tasks & Customer Support Helpdesk Routes
// ----------------------------------------------------------
$router->get('/projects', [ProjectController::class, 'index'], [AuthMiddleware::class]);
$router->post('/projects/store', [ProjectController::class, 'storeProject'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/tasks/store', [ProjectController::class, 'storeTask'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->get('/tickets', [TicketController::class, 'index'], [AuthMiddleware::class]);
$router->post('/tickets/store', [TicketController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/tickets/status', [TicketController::class, 'updateStatus'], [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------------------------
// 10. System Settings, Users (RBAC) & Security Audit Logs
// ----------------------------------------------------------
$router->get('/settings', [SettingController::class, 'index'], [AuthMiddleware::class]);
$router->post('/settings/save', [SettingController::class, 'save'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/users', [SettingController::class, 'users'], [AuthMiddleware::class]);
$router->post('/users/store', [SettingController::class, 'storeUser'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/audit-logs', [SettingController::class, 'auditLogs'], [AuthMiddleware::class]);

// Dispatch incoming HTTP request
$router->dispatch($request);
