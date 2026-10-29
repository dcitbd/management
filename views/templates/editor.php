<div class="template-editor-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title"><?= $template ? 'Edit Template: ' . \App\Core\View::e($template['name']) : 'Create Responsive Email Template' ?></h1>
            <p class="page-subtitle">Supported placeholders: <code>{{name}}</code>, <code>{{email}}</code>, <code>{{company}}</code>, <code>{{unsubscribe_url}}</code></p>
        </div>
        <a href="/templates" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Library</a>
    </div>

    <div class="card">
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="/templates/save">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="id" value="<?= (int)($template['id'] ?? 0) ?>">

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:18px;">
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Template Name *</label>
                        <input type="text" name="name" required value="<?= \App\Core\View::e($template['name'] ?? '') ?>" class="global-search-input" style="padding-left:12px;" placeholder="e.g., Enterprise Corporate Newsletter">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Category</label>
                        <select name="category" class="global-search-input" style="padding-left:12px;">
                            <?php foreach (['Marketing', 'Newsletter', 'Transactional', 'Onboarding', 'Follow-Up'] as $cat): ?>
                                <option value="<?= $cat ?>" <?= ($template['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Sender Name</label>
                        <input type="text" name="sender_name" value="<?= \App\Core\View::e($template['sender_name'] ?? 'Dream Career IT Bangladesh') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Sender Email</label>
                        <input type="email" name="sender_email" value="<?= \App\Core\View::e($template['sender_email'] ?? 'info@dreamcareerit.com') ?>" class="global-search-input" style="padding-left:12px;">
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Email Subject Line * (Supports {{name}} & {{company}})</label>
                    <input type="text" name="subject" required value="<?= \App\Core\View::e($template['subject'] ?? 'Transforming {{company}} with Dream Career IT Digital Solutions') ?>" class="global-search-input" style="padding-left:12px;">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:18px;">
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">HTML Email Body * (Sanitized automatically on save)</label>
                        <textarea id="tpl-html-input" name="body_html" rows="16" required class="global-search-input" style="height:auto;padding:12px;font-family:monospace;font-size:12px;" oninput="updatePreview()"><?= \App\Core\View::e($template['body_html'] ?? "<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#0f172a;color:#f8fafc;padding:30px;border-radius:12px;">
  <h2 style="color:#00f2fe;margin-top:0;">Dream Career IT Bangladesh</h2>
  <p>Hello <strong>{{name}}</strong>,</p>
  <p>We are reaching out to <strong>{{company}}</strong> with our latest full-stack software, SEO, and business automation solutions tailored to accelerate your growth.</p>
  <p style="margin:24px 0;"><a href="https://dreamcartbd.com" style="background:#00f2fe;color:#0b1120;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold;">Explore Our Solutions</a></p>
  <hr style="border:none;border-top:1px solid #1e293b;margin:24px 0;">
  <p style="font-size:12px;color:#94a3b8;">Jainal Abedin, CEO | Dream Career IT Bangladesh | 01581703822<br><a href="{{unsubscribe_url}}" style="color:#38bdf8;">Unsubscribe</a></p>
</div>") ?></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Live Safe Preview (Sandboxed Iframe)</label>
                        <iframe id="tpl-preview-frame" sandbox="allow-same-origin" style="width:100%;height:355px;background:#ffffff;border:1px solid #1e293b;border-radius:8px;"></iframe>
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Plain-Text Fallback (Auto-generated if left blank)</label>
                    <textarea name="body_plain" rows="4" class="global-search-input" style="height:auto;padding:12px;"><?= \App\Core\View::e($template['body_plain'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save & Sanitize Template</button>
            </form>
        </div>
    </div>
</div>
<script>
function updatePreview() {
    const raw = document.getElementById('tpl-html-input').value;
    const frame = document.getElementById('tpl-preview-frame');
    if (frame) {
        const previewHtml = raw
            .replaceAll('{{name}}', 'Jainal Abedin')
            .replaceAll('{{company}}', 'Dream Career IT')
            .replaceAll('{{email}}', 'jainal.dcitbd@gmail.com')
            .replaceAll('{{unsubscribe_url}}', '#unsubscribe');
        frame.srcdoc = previewHtml;
    }
}
document.addEventListener('DOMContentLoaded', updatePreview);
</script>
