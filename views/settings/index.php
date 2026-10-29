<div class="settings-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">System Settings, Branding & Provider Configuration</h1>
            <p class="page-subtitle">Configure Dream Career IT branding, SMTP relays, rate limits, and AI providers without exposing stored secrets</p>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Company Branding, SMTP & AI Parameters</h3></div>
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="/settings/save">
                <?= \App\Core\Csrf::field() ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Application Name</label>
                        <input type="text" name="app_name" value="<?= \App\Core\View::e($settings['app_name'] ?? 'Dream Career IT — AI Business Suite') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Company Name</label>
                        <input type="text" name="company_name" value="<?= \App\Core\View::e($settings['company_name'] ?? 'Dream Career IT Bangladesh') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">CEO & Designer</label>
                        <input type="text" name="company_ceo" value="<?= \App\Core\View::e($settings['company_ceo'] ?? 'Jainal Abedin') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Primary Hotline</label>
                        <input type="text" name="company_phone_1" value="<?= \App\Core\View::e($settings['company_phone_1'] ?? '01581703822') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Secondary Hotline</label>
                        <input type="text" name="company_phone_2" value="<?= \App\Core\View::e($settings['company_phone_2'] ?? '01818273838') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Official Email</label>
                        <input type="email" name="company_email" value="<?= \App\Core\View::e($settings['company_email'] ?? 'jainal.dcitbd@gmail.com') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Shop URL</label>
                        <input type="url" name="company_shop_url" value="<?= \App\Core\View::e($settings['company_shop_url'] ?? 'https://dreamcartbd.com') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?= \App\Core\View::e($settings['smtp_host'] ?? 'smtp.mailtrap.io') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">SMTP Port</label>
                        <input type="number" name="smtp_port" value="<?= \App\Core\View::e($settings['smtp_port'] ?? '587') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Rate Limit (Emails / Minute)</label>
                        <input type="number" name="rate_limit_per_minute" value="<?= \App\Core\View::e($settings['rate_limit_per_minute'] ?? '120') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Default AI Provider</label>
                        <select name="default_provider" class="global-search-input" style="padding-left:12px;">
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
