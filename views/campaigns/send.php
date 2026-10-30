<div class="send-campaign-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Dispatch Direct & Bulk Email Campaign</h1>
            <p class="page-subtitle">Enter any recipient email IDs (one or multiple), upload CSV, or select contacts — dispatches immediately on submit</p>
        </div>
        <div class="action-buttons">
            <a href="/campaigns/logs" class="btn btn-secondary"><i class="bi bi-clock-history"></i> View Delivery & Queue Logs</a>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger" style="margin-bottom:22px;padding:14px 20px;background:rgba(239,68,68,0.15);border:1px solid #ef4444;border-radius:12px;color:#f87171;">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= \App\Core\View::e($flash_error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title"><i class="bi bi-send-check-fill text-cyan"></i> Compose & Dispatch Email</h3>
                <span class="card-subtitle">Emails are dispatched immediately to the exact email IDs you provide below</span>
            </div>
            <span class="badge-live-pulse" id="liveEmailCountBadge"><i class="bi bi-envelope-at"></i> 0 Recipient(s) Detected</span>
        </div>
        <div class="card-body">
            <form method="POST" action="/campaigns/enqueue" enctype="multipart/form-data">
                <?= \App\Core\Csrf::field() ?>

                <div class="grid-3" style="margin-bottom:22px;">
                    <div>
                        <label class="form-label">Campaign / Dispatch Title *</label>
                        <input type="text" name="name" required class="input-box" value="Direct Client Outreach — <?= date('M d') ?>" placeholder="e.g., Q4 Enterprise IT Solutions Outreach">
                    </div>
                    <div>
                        <label class="form-label">Load Saved Template</label>
                        <select name="template_id" id="tplSelector" class="input-box" onchange="applySelectedTemplate()">
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
                        <label class="form-label">Sender Display Name</label>
                        <input type="text" name="sender_name" value="Dream Career IT Bangladesh" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Sender Email Address</label>
                        <input type="email" name="sender_email" value="jainal.dcitbd@gmail.com" class="input-box">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label">Email Subject Line *</label>
                        <input type="text" id="campSubject" name="subject" required class="input-box" value="Hello {{name}}, accelerate {{company}} with Dream Career IT Solutions">
                    </div>
                </div>

                <!-- Recipient Import Sources -->
                <div class="grid-2" style="margin-bottom:22px;">
                    <div>
                        <label class="form-label">
                            <i class="bi bi-envelope-plus-fill text-cyan"></i> Enter or Paste Recipient Email IDs * (Comma, space, or line separated)
                        </label>
                        <textarea name="pasted_recipients" id="pastedRecipientsBox" rows="6" class="input-box" style="font-family:var(--font-mono);font-size:13px;" oninput="countDetectedEmails()" placeholder="example@gmail.com&#10;Jainal Abedin, jainal.dcitbd@gmail.com, Dream Career IT&#10;client1@company.com, client2@company.com"></textarea>
                    </div>
                    <div>
                        <label class="form-label">
                            <i class="bi bi-filetype-csv text-purple"></i> Optional: Upload Recipient CSV / TXT File
                        </label>
                        <input type="file" name="csv_file" accept=".csv,.txt" class="input-box" style="margin-bottom:16px;">

                        <label style="display:flex;align-items:center;gap:10px;font-size:13.5px;color:var(--text-primary);cursor:pointer;margin-bottom:16px;padding:12px 14px;background:var(--bg-surface);border-radius:10px;border:1px solid var(--border-color);">
                            <input type="checkbox" name="include_crm_subscribed" value="1">
                            <span>Also include Subscribed CRM Contacts (<strong><?= (int)$activeContactsCount ?></strong> contacts)</span>
                        </label>

                        <label class="form-label">Optional Schedule Time (Leave blank to send immediately)</label>
                        <input type="datetime-local" name="scheduled_for" class="input-box">
                    </div>
                </div>

                <div style="margin-bottom:24px;">
                    <label class="form-label">Email HTML Content * (Supports <code>{{name}}</code>, <code>{{email}}</code>, <code>{{company}}</code>, <code>{{unsubscribe_url}}</code>)</label>
                    <textarea id="campHtml" name="body_html" rows="10" required class="input-box" style="font-family:var(--font-mono);font-size:12.5px;"><div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:28px;background:#0f172a;color:#f8fafc;border-radius:14px;">
  <h2 style="color:#00f2fe;margin-top:0;">Dream Career IT Bangladesh</h2>
  <p>Dear <strong>{{name}}</strong>,</p>
  <p>We help organizations like <strong>{{company}}</strong> build secure web applications, ERP systems, and high-converting digital marketing campaigns.</p>
  <p style="margin:22px 0;"><a href="https://dreamcartbd.com" style="background:#00f2fe;color:#0b1120;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:bold;">Visit Our Shop: dreamcartbd.com</a></p>
  <p>Direct Hotline: 01581703822, 01818273838</p>
  <hr style="border-color:#1e293b;margin:20px 0;">
  <p style="font-size:11px;color:#94a3b8;">If you prefer not to receive future updates, <a href="{{unsubscribe_url}}" style="color:#38bdf8;">unsubscribe here</a>.</p>
</div></textarea>
                </div>

                <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-check-fill"></i> Send & Dispatch Immediately to Provided Emails</button>
                    <button type="button" class="btn btn-secondary" onclick="openGmailComposeBridge()"><i class="bi bi-google text-cyan"></i> Send via Gmail Web Compose</button>
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
function countDetectedEmails() {
    const raw = document.getElementById('pastedRecipientsBox').value || '';
    const matches = raw.match(/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g) || [];
    const unique = [...new Set(matches.map(e => e.toLowerCase()))];
    const badge = document.getElementById('liveEmailCountBadge');
    if (badge) {
        badge.innerHTML = `<i class="bi bi-envelope-at"></i> ${unique.length} Valid Email(s) Ready`;
    }
}
function openGmailComposeBridge() {
    const raw = document.getElementById('pastedRecipientsBox').value || '';
    const matches = raw.match(/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g) || [];
    const unique = [...new Set(matches.map(e => e.toLowerCase()))];
    if (unique.length === 0) {
        alert('Please enter at least one valid email address first.');
        return;
    }
    const subj = document.getElementById('campSubject').value.replace(/\{\{name\}\}/g, 'Valued Client').replace(/\{\{company\}\}/g, 'Your Organization');
    const tmp = document.createElement('div');
    tmp.innerHTML = document.getElementById('campHtml').value.replace(/<br\s*\/?>/gi, '\n').replace(/<\/p>/gi, '\n\n');
    const plain = (tmp.textContent || tmp.innerText || '').trim().replace(/\{\{name\}\}/g, 'Valued Client').replace(/\{\{company\}\}/g, 'Your Organization');
    const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(unique.join(','))}&su=${encodeURIComponent(subj)}&body=${encodeURIComponent(plain)}`;
    window.open(gmailUrl, '_blank', 'noopener');
}
</script>
