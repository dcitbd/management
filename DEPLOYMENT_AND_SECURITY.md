# Dream Career IT — AI Business Suite
## Complete Installation, Deployment, PWA, Desktop & Security Hardening Guide

### 1. System Requirements
- **PHP:** 8.2 or higher (`pdo_mysql`, `mbstring`, `openssl`, `json`, `curl` enabled)
- **Database:** MySQL 8.0+ or MariaDB 10.5+
- **Web Server:** Apache 2.4 (`mod_rewrite` enabled) or Nginx, with HTTPS (SSL/TLS) certificate
- **Optional Desktop Wrapper:** Node.js 18+ and Electron 28+ (for Windows `.exe` / macOS `.dmg` builds)

---

### 2. Step-by-Step Installation (cPanel / Shared Hosting / VPS)

1. **Extract Archive:**
   Upload and extract `Dream_Career_IT_AI_Business_Suite_FullStack.zip` into your server directory.
2. **Configure Environment Variables:**
   Copy `.env.example` to `.env` and update your MySQL credentials, `APP_URL`, and `APP_SECRET`:
   ```bash
   cp .env.example .env
   ```
3. **Run Database Migration, Seeder & Initial Admin Setup:**
   Run the interactive CLI installer (creates all 30 normalized tables, seeds all 10 service categories & 28 services without duplicates, and prompts you to set up the first Administrator account):
   ```bash
   php database/setup.php
   ```
   *(If you are on shared hosting without SSH access, import `database/migrations/001_create_initial_schema.sql` via phpMyAdmin and run the seeder once).*
4. **Point Document Root:**
   Set your domain's web root (DocumentRoot) to the `/public` directory so `.env`, `/src`, and `/database` are never publicly accessible via HTTP.
5. **Configure Background Queue Worker (Cron / Supervisor):**
   For bulk email campaign processing respecting provider rate limits:
   - **Cron Job (Shared Hosting / cPanel — every minute):**
     ```cron
     * * * * * /usr/bin/php /path/to/dreamcareerit-suite/src/Services/EmailQueueWorker.php --once >> /dev/null 2>&1
     ```
   - **Supervisor / Daemon (VPS):**
     ```bash
     php src/Services/EmailQueueWorker.php
     ```

---

### 3. Progressive Web App (PWA) Installation
- **Android (Chrome / Edge):** Open the application over HTTPS, tap the browser menu or prompt banner, and select **Install App** / **Add to Home Screen**.
- **iPhone / iPad (Safari):** Open the application in Safari, tap the **Share** button (`↑`), and tap **Add to Home Screen**.
- **Security Guarantee:** `public/sw.js` strictly caches only static CSS/JS/SVG assets and never caches private dashboard pages, authentication responses, or confidential messages.

---

### 4. Native Desktop Application Wrapper (Windows & macOS)
To run or package the native desktop application with frameless window controls (Minimize, Maximize/Restore, Full Screen, Close):
```bash
cd desktop
npm install
APP_URL="https://your-domain.com" npm start
```

---

### 5. Production Security Checklist
- [x] **No Default Credentials:** Administrator is created interactively during `php database/setup.php` with `password_hash(..., PASSWORD_BCRYPT, ['cost' => 12])`.
- [x] **SQL Injection Defense:** 100% PDO prepared statements with `ATTR_EMULATE_PREPARES => false`.
- [x] **CSRF Protection:** `CsrfMiddleware` validates cryptographic tokens on all `POST`, `PUT`, and `DELETE` requests.
- [x] **XSS & Template Sanitization:** `View::e()` escapes all dynamic output; `TemplateController::sanitizeTemplateHtml()` strips `<script>`, `<?php`, and inline event handlers.
- [x] **Login Rate Limiting & Session Hardening:** 5 failed attempts trigger a 15-minute lockout (`RateLimiter`); `Session::regenerate()` prevents session fixation.
- [x] **Email Compliance:** Built-in `suppression_list`, `email_consents` ledger, and signed `{{unsubscribe_url}}` links.
