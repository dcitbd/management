<div class="send-campaign-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Dispatch Bulk Email Campaign</h1>
            <p class="page-subtitle">Upload CSV, paste copied rows from Excel/Google Sheets, deduplicate, filter suppressions, and queue for throttled delivery</p>
        </div>
        <a href="/campaigns/logs" class="btn btn-secondary"><i class="bi bi-clock-history"></i> View Queue & Logs</a>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger" style="margin-bottom:16px;padding:12px 16px;background:rgba(239,68,68,0.15);border:1px solid #ef4444;border-radius:8px;color:#f87171;">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= \App\Core\View::e($flash_error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="/campaigns/enqueue" enctype="multipart/form-data">
                <?= \App\Core\Csrf::field() ?>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:18px;">
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Campaign Name *</label>
                        <input type="text" name="name" required class="global-search-input" style="padding-left:12px;" placeholder="e.g., Q4 Enterprise IT Solutions Outreach">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Load Existing Template</label>
                        <select name="template_id" id="tplSelector" class="global-search-input" style="padding-left:12px;" onchange="applySelectedTemplate()">
                            <option value="">-- Custom / Select Template --</option>
                            <?php foreach ($templates as $t): ?>
                                <option value="<?= (int)$t['id'] ?>"
                                        data-subject="<?= \App\Core\View::e($t['subject']) ?>"
                                        data-html="<?= \App\Core\View::e($t['body_html']) ?>">
                                    <?= \App\Core\View::e($t['name']) ?> (<?= \App\Core\View::e($t['category']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Sender Name</label>
                        <input type="text" name="sender_name" value="Dream Career IT Bangladesh" class="global-search-input" style="padding-left:12px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Sender Email</label>
                        <input type="email" name="sender_email" value="info@dreamcareerit.com" class="global-search-input" style="padding-left:12px;">
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Email Subject *</label>
                    <input type="text" id="campSubject" name="subject" required class="global-search-input" style="padding-left:12px;" placeholder="Hello {{name}}, accelerate {{company}} with Dream Career IT">
                </div>

                <!-- Recipient Import Sources: Paste from Excel/Sheets + CSV Upload + CRM -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:18px;">
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">
                            <i class="bi bi-clipboard-data text-cyan"></i> Paste Recipients from Excel / Google Sheets (Name, Email, Company or one email per line)
                        </label>
                        <textarea name="pasted_recipients" rows="6" class="global-search-input" style="height:auto;padding:12px;font-family:monospace;font-size:12px;" placeholder="Jainal Abedin, jainal.dcitbd@gmail.com, Dream Career IT&#10;client@example.com"></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">
                            <i class="bi bi-filetype-csv text-purple"></i> Upload Recipient CSV File
                        </label>
                        <input type="file" name="csv_file" accept=".csv,.txt" class="global-search-input" style="padding:8px 12px;margin-bottom:14px;">

                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#f8fafc;cursor:pointer;margin-bottom:14px;">
                            <input type="checkbox" name="include_crm_subscribed" value="1">
                            <span>Also include all Subscribed CRM Contacts (<strong><?= (int)$activeContactsCount ?></strong> active contacts)</span>
                        </label>

                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Optional Schedule Time (Leave blank to queue immediately)</label>
                        <input type="datetime-local" name="scheduled_for" class="global-search-input" style="padding-left:12px;">
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Campaign HTML Body * (Supports {{name}}, {{email}}, {{company}}, {{unsubscribe_url}})</label>
                    <textarea id="campHtml" name="body_html" rows="10" required class="global-search-input" style="height:auto;padding:12px;font-family:monospace;font-size:12px;"><div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#0f172a;color:#f8fafc;border-radius:10px;">
  <h2 style="color:#00f2fe;">Dream Career IT Bangladesh</h2>
  <p>Dear {{name}},</p>
  <p>We help organizations like {{company}} build secure web applications, ERP systems, and high-converting digital marketing campaigns.</p>
  <p><a href="https://dreamcartbd.com" style="color:#00f2fe;">Visit Our Shop: dreamcartbd.com</a> | Hotline: 01581703822</p>
  <hr style="border-color:#1e293b;">
  <p style="font-size:11px;color:#94a3b8;">If you prefer not to receive future updates, <a href="{{unsubscribe_url}}" style="color:#38bdf8;">unsubscribe here</a>.</p>
</div></textarea>
                </div>

                <div style="display:flex;gap:12px;align-items:center;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-check-fill"></i> Validate, Deduplicate & Enqueue Campaign</button>
                    <span style="font-size:12px;color:#94a3b8;"><i class="bi bi-shield-check text-success"></i> Suppressed & unsubscribed contacts are automatically filtered out.</span>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function applySelectedTemplate() {
    const sel = document.getElementById('tplSelector');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        document.getElementById('campSubject').value = opt.getAttribute('data-subject') || '';
        document.getElementById('campHtml').value = opt.getAttribute('data-html') || '';
    }
}
</script>
