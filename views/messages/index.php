<div class="messages-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Unified Omnichannel Message Center</h1>
            <p class="page-subtitle">Authorized integrations for Facebook Pages, Messenger, Instagram, WhatsApp Business, Viber, Daraz & Bikroy workflows</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-conv-modal').style.display='block'">
            <i class="bi bi-chat-left-text-fill"></i> New Conversation Thread
        </button>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <!-- New Conversation Box -->
    <div class="card" id="new-conv-modal" style="display:none;margin-bottom:20px;">
        <div class="card-header">
            <h3 class="card-title">Log / Start Omnichannel Conversation</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('new-conv-modal').style.display='none'">Cancel</button>
        </div>
        <div class="card-body" style="padding:18px;">
            <form method="POST" action="/messages/create">
                <?= \App\Core\Csrf::field() ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <input type="text" name="title" required placeholder="Client Name / Inquiry Subject *" class="global-search-input" style="padding-left:12px;">
                    <select name="channel" class="global-search-input" style="padding-left:12px;">
                        <option value="facebook">Facebook Page / Messenger (Graph API)</option>
                        <option value="whatsapp">WhatsApp Business Cloud API</option>
                        <option value="instagram">Instagram Direct (Graph API)</option>
                        <option value="viber">Viber Business</option>
                        <option value="daraz">Daraz Seller Center</option>
                        <option value="bikroy">Bikroy.com (Manual Inquiry Log)</option>
                    </select>
                </div>
                <textarea name="message_text" rows="2" placeholder="Initial incoming message or inquiry summary..." class="global-search-input" style="height:auto;padding:10px;margin-bottom:12px;"></textarea>
                <button type="submit" class="btn btn-primary">Create Thread</button>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:320px 1fr;gap:20px;min-height:460px;">
        <!-- Left Thread List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Active Threads (<?= count($conversations) ?>)</h3>
            </div>
            <div class="card-body p-0" style="overflow-y:auto;max-height:440px;">
                <?php if (empty($conversations)): ?>
                    <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">
                        No active conversations. Click "New Conversation Thread" to start.
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="/messages?conversation_id=<?= (int)$conv['id'] ?>" style="display:block;padding:14px 16px;border-bottom:1px solid #1e293b;text-decoration:none;background:<?= ($activeConversation['id'] ?? 0) == $conv['id'] ? 'rgba(0,242,254,0.08)' : 'transparent' ?>;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                <strong style="color:#f8fafc;font-size:13px;"><?= \App\Core\View::e($conv['title']) ?></strong>
                                <span class="badge-action"><?= strtoupper($conv['channel']) ?></span>
                            </div>
                            <div style="font-size:11px;color:#64748b;">Status: <?= strtoupper($conv['status']) ?></div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Conversation History & Composer -->
        <div class="card" style="display:flex;flex-direction:column;">
            <?php if (!$activeConversation): ?>
                <div style="padding:60px;text-align:center;color:#94a3b8;">
                    <i class="bi bi-chat-square-dots" style="font-size:36px;display:block;margin-bottom:12px;color:#38bdf8;"></i>
                    Select a conversation thread on the left or create a new thread.
                </div>
            <?php else: ?>
                <div class="card-header">
                    <div>
                        <h3 class="card-title"><?= \App\Core\View::e($activeConversation['title']) ?></h3>
                        <span class="card-subtitle">Channel: <?= strtoupper($activeConversation['channel']) ?> &bull; Status: <?= strtoupper($activeConversation['status']) ?></span>
                    </div>
                    <?php if ($activeConversation['channel'] === 'bikroy'): ?>
                        <span class="badge badge-status-draft" title="Bikroy does not provide a public messaging API">MANUAL WORKFLOW MODE</span>
                    <?php endif; ?>
                </div>
                <div class="card-body" style="flex:1;padding:20px;overflow-y:auto;max-height:280px;display:flex;flex-direction:column;gap:12px;">
                    <?php foreach ($messages as $m): ?>
                        <div style="align-self:<?= $m['sender_type'] === 'agent' ? 'flex-end' : 'flex-start' ?>;background:<?= $m['sender_type'] === 'agent' ? 'rgba(0,242,254,0.12)' : '#1e293b' ?>;border:1px solid rgba(56,189,248,0.25);padding:10px 14px;border-radius:10px;max-width:75%;">
                            <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;">
                                <strong><?= \App\Core\View::e($m['sender_id'] ?? $m['sender_type']) ?></strong> &bull; <?= date('H:i', strtotime($m['created_at'])) ?>
                            </div>
                            <div style="font-size:13px;color:#f8fafc;"><?= nl2br(\App\Core\View::e($m['message_text'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="padding:16px;border-top:1px solid #1e293b;">
                    <form method="POST" action="/messages/reply">
                        <?= \App\Core\Csrf::field() ?>
                        <input type="hidden" name="conversation_id" value="<?= (int)$activeConversation['id'] ?>">
                        <textarea name="message_text" rows="2" required placeholder="Type your reply..." class="global-search-input" style="height:auto;padding:10px;margin-bottom:10px;"></textarea>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <input type="text" name="internal_notes" value="<?= \App\Core\View::e($activeConversation['internal_notes'] ?? '') ?>" placeholder="Internal team note (private)..." class="global-search-input" style="width:65%;height:34px;padding-left:10px;">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Send Reply</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
