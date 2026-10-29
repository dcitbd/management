<div class="ai-assistant-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title"><i class="bi bi-robot text-cyan"></i> AI Business Assistant (Bangla & English)</h1>
            <p class="page-subtitle">Generate high-converting email drafts, subject lines, customer support replies, and technical documentation</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">AI Prompt Configuration</h3>
            </div>
            <div class="card-body" style="padding:20px;">
                <form method="POST" action="/ai-assistant/generate">
                    <?= \App\Core\Csrf::field() ?>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Select Task Type</label>
                        <select name="task_type" class="global-search-input" style="padding-left:12px;">
                            <option value="email_draft">Generate Marketing Email Draft</option>
                            <option value="subject_lines">Improve & Suggest Email Subject Lines</option>
                            <option value="support_reply">Suggest Customer Support Ticket Reply</option>
                            <option value="service_desc">Write IT Service Description & Documentation</option>
                        </select>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Output Language</label>
                        <select name="language" class="global-search-input" style="padding-left:12px;">
                            <option value="English">English</option>
                            <option value="Bangla">বাংলা (Bangla)</option>
                        </select>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Topic, Client Context, or Instructions *</label>
                        <textarea name="prompt" rows="5" required class="global-search-input" style="height:auto;padding:12px;" placeholder="e.g., Enterprise E-commerce & ERP automation proposal for retail brands in Bangladesh"><?= \App\Core\View::e($lastPrompt ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-magic"></i> Generate AI Content</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Generated Output (Review Before Sending)</h3>
                <?php if (!empty($provider)): ?>
                    <span class="badge-action"><?= \App\Core\View::e($provider) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body" style="padding:20px;">
                <textarea id="aiOutputBox" rows="13" readonly class="global-search-input" style="height:auto;padding:14px;font-family:inherit;line-height:1.6;"><?= \App\Core\View::e($result ?? 'Submit a prompt on the left to generate Bangla or English business copy.') ?></textarea>
                <div style="margin-top:12px;display:flex;gap:10px;">
                    <button type="button" class="btn btn-secondary" onclick="navigator.clipboard.writeText(document.getElementById('aiOutputBox').value);App.toast('success','Copied to clipboard!');">
                        <i class="bi bi-clipboard"></i> Copy Output
                    </button>
                    <a href="/campaigns/send" class="btn btn-outline">Use in Campaign</a>
                </div>
            </div>
        </div>
    </div>
</div>
