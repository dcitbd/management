<div class="settings-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">System Settings, SMTP Relay & AI Configuration</h1>
            <p class="page-subtitle">Configure Dream Career IT branding, authenticated SMTP credentials for live email delivery, and AI providers</p>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="bi bi-sliders text-cyan"></i> Company Branding, Live SMTP Transport & AI Parameters</h3></div>
        <div class="card-body">
            <form method="POST" action="/settings/save">
                <?= \App\Core\Csrf::field() ?>
                <div class="grid-3" style="margin-bottom:24px;">
                    <div>
                        <label class="form-label">Application Name</label>
                        <input type="text" name="app_name" value="<?= \App\Core\View::e($settings['app_name'] ?? 'Dream Career IT — AI Business Suite') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" value="<?= \App\Core\View::e($settings['company_name'] ?? 'Dream Career IT Bangladesh') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">CEO & Designer</label>
                        <input type="text" name="company_ceo" value="<?= \App\Core\View::e($settings['company_ceo'] ?? 'Jainal Abedin') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Primary Hotline</label>
                        <input type="text" name="company_phone_1" value="<?= \App\Core\View::e($settings['company_phone_1'] ?? '01581703822') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Secondary Hotline</label>
                        <input type="text" name="company_phone_2" value="<?= \App\Core\View::e($settings['company_phone_2'] ?? '01818273838') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Official Sender Email</label>
                        <input type="email" name="company_email" value="<?= \App\Core\View::e($settings['company_email'] ?? 'jainal.dcitbd@gmail.com') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Shop URL</label>
                        <input type="url" name="company_shop_url" value="<?= \App\Core\View::e($settings['company_shop_url'] ?? 'https://dreamcartbd.com') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">SMTP Host (e.g., smtp.gmail.com)</label>
                        <input type="text" name="smtp_host" value="<?= \App\Core\View::e($settings['smtp_host'] ?? 'smtp.gmail.com') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">SMTP Port (587 TLS / 465 SSL)</label>
                        <input type="number" name="smtp_port" value="<?= \App\Core\View::e($settings['smtp_port'] ?? '587') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">SMTP Username / Email</label>
                        <input type="text" name="smtp_user" value="<?= \App\Core\View::e($settings['smtp_user'] ?? 'jainal.dcitbd@gmail.com') ?>" class="input-box" placeholder="your-email@gmail.com">
                    </div>
                    <div>
                        <label class="form-label">SMTP Password / Gmail App Password</label>
                        <input type="password" name="smtp_pass" value="<?= \App\Core\View::e($settings['smtp_pass'] ?? '') ?>" class="input-box" placeholder="Enter 16-digit Gmail App Password or SMTP Password">
                    </div>
                    <div>
                        <label class="form-label">SMTP Encryption</label>
                        <select name="smtp_encryption" class="input-box">
                            <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>STARTTLS (Port 587)</option>
                            <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL/TLS (Port 465)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Rate Limit (Emails / Minute)</label>
                        <input type="number" name="rate_limit_per_minute" value="<?= \App\Core\View::e($settings['rate_limit_per_minute'] ?? '120') ?>" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Default AI Provider</label>
                        <select name="default_provider" class="input-box">
                            <option value="gemini" <?= ($settings['default_provider'] ?? '') === 'gemini' ? 'selected' : '' ?>>Google Gemini API</option>
                            <option value="openai" <?= ($settings['default_provider'] ?? '') === 'openai' ? 'selected' : '' ?>>OpenAI GPT-4o</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save Configuration</button>
            </form>
        </div>
    </div>
</div>
