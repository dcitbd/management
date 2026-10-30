<div class="send-campaign-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title"><i class="bi bi-rocket-takeoff-fill text-cyan"></i> Automated Bulk Email Sender</h1>
            <p class="page-subtitle">Select any template, paste or upload bulk recipient email IDs, and click Submit — the Auto-Sender dispatches every email automatically</p>
        </div>
        <div class="action-buttons">
            <a href="/templates" class="btn btn-outline"><i class="bi bi-layout-text-window-reverse"></i> Manage Templates</a>
            <a href="/campaigns/logs" class="btn btn-secondary"><i class="bi bi-clock-history"></i> View Delivery Logs</a>
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

    <!-- Live Auto-Sender Progress Monitor Card (Appears during bulk sending) -->
    <div class="card" id="phpAutoSenderMonitor" style="display:none;border-color:var(--accent-cyan);box-shadow:var(--shadow-3d-hover);">
        <div class="card-header">
            <div>
                <h3 class="card-title"><i class="bi bi-cpu-fill text-cyan"></i> Bulk Auto-Sender Engine Running...</h3>
                <span class="card-subtitle" id="phpAutoSenderSub">Automatically dispatching emails to recipients...</span>
            </div>
            <span class="badge-live-pulse" id="phpAutoSenderStatusBadge"><i class="bi bi-lightning-charge-fill"></i> AUTO-SENDING</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:20px;">
                <div style="padding:14px 18px;background:var(--bg-surface);border-radius:12px;border:1px solid var(--border-color);">
                    <div style="font-size:11px;" class="text-muted">TOTAL RECIPIENTS</div>
                    <div style="font-size:24px;font-weight:800;" id="phpAsTotal">0</div>
                </div>
                <div style="padding:14px 18px;background:var(--bg-surface);border-radius:12px;border:1px solid rgba(16,185,129,0.3);">
                    <div style="font-size:11px;color:#34d399;">AUTO-SENT / DELIVERED</div>
                    <div style="font-size:24px;font-weight:800;color:#34d399;" id="phpAsSent">0</div>
                </div>
                <div style="padding:14px 18px;background:var(--bg-surface);border-radius:12px;border:1px solid rgba(56,189,248,0.3);">
                    <div style="font-size:11px;color:#38bdf8;">REMAINING</div>
                    <div style="font-size:24px;font-weight:800;color:#38bdf8;" id="phpAsRemaining">0</div>
                </div>
                <div style="padding:14px 18px;background:var(--bg-surface);border-radius:12px;border:1px solid rgba(239,68,68,0.3);">
                    <div style="font-size:11px;color:#f87171;">FAILED / SKIPPED</div>
                    <div style="font-size:24px;font-weight:800;color:#f87171;" id="phpAsFailed">0</div>
                </div>
            </div>
            <div style="width:100%;height:12px;background:var(--bg-primary);border-radius:999px;overflow:hidden;margin-bottom:16px;border:1px solid var(--border-color);">
                <div id="phpAsProgressBar" style="width:0%;height:100%;background:linear-gradient(90deg,#00f2fe,#38bdf8,#10b981);transition:width 0.25s ease;"></div>
            </div>
            <div id="phpAsLogFeed" style="max-height:190px;overflow-y:auto;background:#040812;border:1px solid var(--border-color);border-radius:12px;padding:14px;font-family:var(--font-mono);font-size:12px;line-height:1.7;"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title"><i class="bi bi-send-check-fill text-cyan"></i> 1-Click Template Selection & Bulk Auto-Sender</h3>
                <span class="card-subtitle">Select your template below, enter bulk recipient emails, and click Submit to auto-send</span>
            </div>
            <span class="badge-live-pulse" id="liveEmailCountBadge"><i class="bi bi-envelope-at"></i> 0 Recipient(s) Ready</span>
        </div>
        <div class="card-body">
            <form method="POST" action="/campaigns/enqueue" enctype="multipart/form-data" id="phpBulkSendForm" onsubmit="return runPhpLiveAutoSender(event)">
                <?= \App\Core\Csrf::field() ?>

                <!-- Step 1: Select Template (Quick 3D Selector Cards + Dropdown) -->
                <div style="margin-bottom:24px;padding:18px 20px;background:rgba(0,242,254,0.04);border:1px solid var(--border-strong);border-radius:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:14px;">
                        <label class="form-label" style="margin-bottom:0;font-size:14px;color:var(--accent-cyan);">
                            <i class="bi bi-1-circle-fill"></i> Select Email Template (Auto-fills Subject & HTML Body) *
                        </label>
                        <span style="font-size:12px;" class="text-muted">Selecting any template loads it immediately for bulk sending</span>
                    </div>
                    <select name="template_id" id="tplSelector" class="input-box" onchange="applySelectedTemplate()" style="margin-bottom:14px;">
                        <?php foreach ($templates as $idx => $t): ?>
                            <option value="<?= (int)$t['id'] ?>"
                                    <?= ($selectedTemplateId === (int)$t['id'] || ($selectedTemplateId === 0 && $idx === 0)) ? 'selected' : '' ?>
                                    data-name="<?= \App\Core\View::e($t['name']) ?>"
                                    data-subject="<?= \App\Core\View::e($t['subject']) ?>"
                                    data-sender-name="<?= \App\Core\View::e($t['sender_name'] ?? 'Dream Career IT Bangladesh') ?>"
                                    data-sender-email="<?= \App\Core\View::e($t['sender_email'] ?? 'info@dreamcareerit.com') ?>"
                                    data-html="<?= \App\Core\View::e($t['body_html']) ?>">
                                <?= \App\Core\View::e($t['name']) ?> — [<?= \App\Core\View::e($t['category']) ?>] (Subject: <?= \App\Core\View::e($t['subject']) ?>)
                            </option>
                        <?php endforeach; ?>
                        <option value="">-- Custom Message (Write Below) --</option>
                    </select>
                </div>

                <div class="grid-3" style="margin-bottom:22px;">
                    <div>
                        <label class="form-label">Campaign / Batch Title *</label>
                        <input type="text" id="campName" name="name" required class="input-box" value="Bulk Auto-Send — <?= date('M d, H:i') ?>">
                    </div>
                    <div>
                        <label class="form-label">Sender Display Name</label>
                        <input type="text" id="campSenderName" name="sender_name" value="Dream Career IT Bangladesh" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Sender Email Address</label>
                        <input type="email" id="campSenderEmail" name="sender_email" value="info@dreamcareerit.com" class="input-box">
                    </div>
                </div>

                <!-- Step 2: Bulk Recipients -->
                <div class="grid-2" style="margin-bottom:22px;">
                    <div>
                        <label class="form-label">
                            <i class="bi bi-2-circle-fill text-cyan"></i> Paste Bulk Recipient Email IDs * (Comma, space, or line separated / Excel paste)
                        </label>
                        <textarea name="pasted_recipients" id="pastedRecipientsBox" rows="7" class="input-box" style="font-family:var(--font-mono);font-size:13px;" oninput="countDetectedEmails()" placeholder="client1@company.com&#10;client2@business.org&#10;Rahim Uddin, rahim@corp.com, Chattogram Trade&#10;karim@enterprise.net, info@partner.com"></textarea>
                    </div>
                    <div>
                        <label class="form-label">
                            <i class="bi bi-filetype-csv text-purple"></i> Or Upload Bulk CSV / TXT Email List
                        </label>
                        <input type="file" name="csv_file" id="phpCsvFile" accept=".csv,.txt" class="input-box" style="margin-bottom:14px;" onchange="loadCsvIntoBox(event)">

                        <label style="display:flex;align-items:center;gap:10px;font-size:13.5px;color:var(--text-primary);cursor:pointer;margin-bottom:14px;padding:12px 14px;background:var(--bg-surface);border-radius:10px;border:1px solid var(--border-color);">
                            <input type="checkbox" name="include_crm_subscribed" id="phpIncludeCrm" value="1">
                            <span>Also include Subscribed CRM Contacts (<strong><?= (int)$activeContactsCount ?></strong> active contacts)</span>
                        </label>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <label class="form-label">Auto-Sender Speed / Delay</label>
                                <select id="phpAutoSpeed" class="input-box">
                                    <option value="50">Ultra Fast (20 mails/sec)</option>
                                    <option value="250" selected>Smart Throttle (4 mails/sec)</option>
                                    <option value="800">Safe SMTP Mode (1.2/sec)</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Schedule (Blank = Auto-Send Now)</label>
                                <input type="datetime-local" name="scheduled_for" id="phpScheduleFor" class="input-box">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Template Subject & Body Preview/Edit -->
                <div style="margin-bottom:18px;">
                    <label class="form-label"><i class="bi bi-3-circle-fill text-cyan"></i> Selected Template Subject * (Supports <code>{{name}}</code>, <code>{{company}}</code>)</label>
                    <input type="text" id="campSubject" name="subject" required class="input-box" value="Hello {{name}}, accelerate {{company}} with Dream Career IT Solutions">
                </div>

                <div style="margin-bottom:24px;">
                    <label class="form-label">Selected Template HTML Body * (Supports <code>{{name}}</code>, <code>{{email}}</code>, <code>{{company}}</code>, <code>{{unsubscribe_url}}</code>)</label>
                    <textarea id="campHtml" name="body_html" rows="9" required class="input-box" style="font-family:var(--font-mono);font-size:12.5px;"></textarea>
                </div>

                <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary" id="phpBulkSubmitBtn" style="padding:12px 26px;font-size:14.5px;">
                        <i class="bi bi-send-check-fill"></i> Submit & Start Bulk Auto-Sender Now
                    </button>
                    <span class="text-muted" style="font-size:12.5px;">
                        <i class="bi bi-cpu text-cyan"></i> Automatically merges placeholders and sends to every recipient email ID without manual popups.
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function applySelectedTemplate() {
    const sel = document.getElementById('tplSelector');
    if (!sel) return;
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        document.getElementById('campSubject').value = opt.getAttribute('data-subject') || '';
        document.getElementById('campHtml').value = opt.getAttribute('data-html') || '';
        const sName = opt.getAttribute('data-sender-name');
        const sEmail = opt.getAttribute('data-sender-email');
        if (sName) document.getElementById('campSenderName').value = sName;
        if (sEmail) document.getElementById('campSenderEmail').value = sEmail;
    }
}
function loadCsvIntoBox(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(evt) {
        const box = document.getElementById('pastedRecipientsBox');
        box.value = (box.value ? box.value + "\n" : "") + evt.target.result;
        countDetectedEmails();
    };
    reader.readAsText(file);
}
function extractBulkEmailsJs(raw) {
    const matches = (raw || '').match(/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g) || [];
    return [...new Set(matches.map(e => e.toLowerCase()))];
}
function countDetectedEmails() {
    const raw = document.getElementById('pastedRecipientsBox').value || '';
    const unique = extractBulkEmailsJs(raw);
    const badge = document.getElementById('liveEmailCountBadge');
    if (badge) {
        badge.innerHTML = `<i class="bi bi-envelope-at"></i> ${unique.length} Valid Email(s) Ready`;
    }
}
async function runPhpLiveAutoSender(e) {
    const sched = document.getElementById('phpScheduleFor').value;
    const includeCrm = document.getElementById('phpIncludeCrm').checked;
    const raw = document.getElementById('pastedRecipientsBox').value || '';
    const emails = extractBulkEmailsJs(raw);

    // If scheduled for later or only using server-side CRM contacts, let standard PHP POST handle it
    if (sched || (emails.length === 0 && includeCrm)) {
        return true;
    }

    if (emails.length === 0) {
        e.preventDefault();
        alert('অনুগ্রহ করে অন্তত একটি সঠিক ইমেইল আইডি দিন (Please enter or upload at least one valid email ID).');
        return false;
    }

    e.preventDefault();
    const monitor = document.getElementById('phpAutoSenderMonitor');
    const logFeed = document.getElementById('phpAsLogFeed');
    const progBar = document.getElementById('phpAsProgressBar');
    const btn = document.getElementById('phpBulkSubmitBtn');
    const delayMs = parseInt(document.getElementById('phpAutoSpeed').value || '250', 10);

    monitor.style.display = 'block';
    monitor.scrollIntoView({ behavior: 'smooth', block: 'start' });
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Auto-Sender Dispatching...';
    logFeed.innerHTML = '';

    const total = emails.length;
    let sent = 0;
    let failed = 0;
    document.getElementById('phpAsTotal').textContent = total;
    document.getElementById('phpAsSent').textContent = '0';
    document.getElementById('phpAsRemaining').textContent = total;
    document.getElementById('phpAsFailed').textContent = '0';

    const subject = document.getElementById('campSubject').value;
    const bodyHtml = document.getElementById('campHtml').value;
    const senderName = document.getElementById('campSenderName').value;
    const senderEmail = document.getElementById('campSenderEmail').value;
    const tplId = document.getElementById('tplSelector').value;

    for (let i = 0; i < total; i++) {
        const em = emails[i];
        document.getElementById('phpAutoSenderSub').textContent = `Auto-sending (${i + 1} of ${total}) to ${em}...`;
        try {
            const resp = await fetch('/api/send-email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    template_id: tplId,
                    subject: subject,
                    body_html: bodyHtml,
                    sender_name: senderName,
                    sender_email: senderEmail,
                    recipients: [em]
                })
            });
            const data = await resp.json();
            if (data && data.success) {
                sent++;
                const r = data.results && data.results[0] ? data.results[0] : {};
                logFeed.innerHTML = `<div style="color:#34d399;">[✓ ${new Date().toLocaleTimeString()}] AUTO-SENT -> <strong>${em}</strong> (${r.provider_message_id || '250 OK'})</div>` + logFeed.innerHTML;
            } else {
                failed++;
                logFeed.innerHTML = `<div style="color:#f87171;">[✗ ${new Date().toLocaleTimeString()}] FAILED -> ${em}</div>` + logFeed.innerHTML;
            }
        } catch (err) {
            // Fallback to submitting the full form to backend queue worker
            document.getElementById('phpBulkSendForm').onsubmit = null;
            document.getElementById('phpBulkSendForm').submit();
            return false;
        }

        const rem = total - (i + 1);
        const pct = Math.round(((i + 1) / total) * 100);
        document.getElementById('phpAsSent').textContent = sent;
        document.getElementById('phpAsRemaining').textContent = rem;
        document.getElementById('phpAsFailed').textContent = failed;
        progBar.style.width = pct + '%';

        if (i < total - 1 && delayMs > 0) {
            await new Promise(r => setTimeout(r, delayMs));
        }
    }

    document.getElementById('phpAutoSenderSub').textContent = `Completed! Automatically dispatched ${sent} of ${total} emails.`;
    document.getElementById('phpAutoSenderStatusBadge').innerHTML = '<i class="bi bi-check-circle-fill"></i> COMPLETED';
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-send-check-fill"></i> Submit & Start Bulk Auto-Sender Now';
    return false;
}
document.addEventListener('DOMContentLoaded', function() {
    applySelectedTemplate();
    countDetectedEmails();
});
</script>
