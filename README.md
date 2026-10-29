# Dream Career IT — AI Business Suite (v2.4 Enterprise)
**Full-Stack Web Application + PWA + Desktop Application (Electron/Tauri)**

---

### 1. Ownership & Corporate Identity
* **Application Name:** Dream Career IT — AI Business Suite
* **Designer & Director:** Jainal Abedin
* **Position:** CEO, Dream Career IT Bangladesh
* **Official Contacts:** `01581703822`, `01818273838`
* **Direct Email:** `jainal.dcitbd@gmail.com`
* **E-Commerce Shop:** [https://dreamcartbd.com](https://dreamcartbd.com)

---

### 2. Architectural Blueprint & Technical Stack
The system is engineered as an enterprise-grade, modular MVC full-stack application built for high throughput, strict security, and zero external framework lock-in.

* **Backend Engine:** PHP 8.2+ with PSR-4 compliance, strict typing (`declare(strict_types=1);`), custom lightweight MVC router, and PDO prepared statements.
* **Database Layer:** MySQL 8.0+ / MariaDB 10.5+ (InnoDB, `utf8mb4_unicode_ci`), normalized 3NF relational schema with foreign key cascades and composite indexes.
* **Frontend & UI/UX:** HTML5 semantic structure, CSS3 responsive grid with Nebula Ops dark gradient theme and optional light toggle, Vanilla ES6+ JavaScript.
* **Security Subsystem:** BCrypt password hashing (`PASSWORD_BCRYPT`, cost 12), cryptographically secure session management (`session_regenerate_id`), CSRF tokens for all state-changing verbs, IP/account-based login rate limiting (5 attempts / 15-minute decay), and RBAC (`Admin`, `Manager`, `Staff`, `Read-only`).
* **PWA Engine:** Web App Manifest (`manifest.json`), service worker (`sw.js`) with cache isolation preventing sensitive data leaks.
* **Desktop Wrapper:** Electron multi-process architecture with sandboxed preload context bridge, frameless native title bar controls (minimize, maximize/restore, fullscreen, close).

---

### 3. Database Entity Relationships Overview
1. **Security & Governance:** `roles` 1:N `users` 1:N `audit_logs`, `roles` N:M `permissions` via `role_permissions`.
2. **Catalog & Services:** `service_categories` 1:N `services`.
3. **CRM & Suppression:** `contacts` N:M `contact_lists` via `contact_list_members`, `contacts` 1:N `email_consents`, `suppression_list` (unique email hash).
4. **Campaign & Queue Processing:** `campaigns` 1:N `campaign_recipients` 1:1 `email_queue` 1:N `email_logs`.
5. **Omnichannel Messaging:** `social_accounts` 1:N `conversations` 1:N `messages`, `webhook_events`.
6. **Operations:** `projects` 1:N `tasks`, `support_tickets` 1:N `ticket_comments`.

---

### 4. Phase 1 Implementation File Directory
```
dreamcareerit-suite/
├── .env.example                               # Environment secrets template
├── composer.json                              # PSR-4 Autoloading definition
├── database/
│   ├── migrations/
│   │   └── 001_create_initial_schema.sql     # 30+ relational MySQL tables
│   ├── seeders/
│   │   └── DatabaseSeeder.php                # Repeatable catalog & RBAC seeder
│   └── setup.php                             # Interactive CLI installer & admin generator
├── public/
│   ├── index.php                             # Front Controller & route gateway
│   ├── manifest.json                         # PWA Manifest
│   ├── sw.js                                 # PWA Service Worker
│   └── assets/
│       ├── css/
│       │   ├── app.css                       # Nebula Ops dark theme & layout
│       │   └── auth.css                      # Authentication UI styles
│       ├── js/
│       │   └── app.js                        # Client controllers, toasts, desktop bridge
│       └── images/
│           ├── logo.svg                      # Original vector branding logo
│           └── favicon.svg                   # Original application emblem
├── src/
│   ├── Core/                                 # Zero-dependency MVC Framework Core
│   │   ├── Config.php
│   │   ├── Database.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Session.php
│   │   ├── Csrf.php
│   │   ├── RateLimiter.php
│   │   ├── Router.php
│   │   ├── View.php
│   │   └── Logger.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── CsrfMiddleware.php
│   ├── Models/
│   │   └── User.php
│   ├── Services/
│   │   └── AuthService.php
│   └── Controllers/
│       ├── AuthController.php
│       └── DashboardController.php
├── views/
│   ├── layouts/
│   │   ├── main.php                          # Desktop application shell
│   │   └── auth.php                          # Standalone login shell
│   ├── partials/
│   │   ├── titlebar.php                      # Desktop window control titlebar
│   │   ├── sidebar.php                       # Collapsible navigation
│   │   └── header.php                        # Global search & profile pill
│   ├── auth/
│   │   ├── login.php                         # Login form with CSRF & toggle PW
│   │   └── forgot-password.php               # Secure password reset request
│   ├── dashboard/
│   │   └── index.php                         # Instrument panel telemetry & tables
│   └── errors/
│       ├── 403.php
│       ├── 404.php
│       └── 500.php
└── desktop/
    ├── package.json
    ├── main.js                               # Electron main process
    └── preload.js                            # Isolated contextBridge
```
