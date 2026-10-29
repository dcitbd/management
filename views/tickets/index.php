<div class="tickets-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Customer Support Tickets</h1>
            <p class="page-subtitle">SLA issue tracking, priority assignment, and resolution history</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-ticket-box').style.display='block'">
            <i class="bi bi-plus-lg"></i> Open Support Ticket
        </button>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <div class="card" id="new-ticket-box" style="display:none;margin-bottom:20px;">
        <div class="card-header">
            <h3 class="card-title">Create Support Ticket</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('new-ticket-box').style.display='none'">Close</button>
        </div>
        <div class="card-body" style="padding:20px;">
            <form method="POST" action="/tickets/store">
                <?= \App\Core\Csrf::field() ?>
                <div style="display:grid;grid-template-columns:2fr 1fr;gap:14px;margin-bottom:14px;">
                    <input type="text" name="subject" required placeholder="Ticket Subject *" class="global-search-input" style="padding-left:12px;">
                    <select name="priority" class="global-search-input" style="padding-left:12px;">
                        <option value="medium">Priority: Medium</option>
                        <option value="high">Priority: High</option>
                        <option value="urgent">Priority: Urgent</option>
                    </select>
                </div>
                <textarea name="description" rows="3" required placeholder="Detailed issue description..." class="global-search-input" style="height:auto;padding:10px;margin-bottom:12px;"></textarea>
                <button type="submit" class="btn btn-primary">Submit Ticket</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Helpdesk Queue (<?= count($tickets) ?>)</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tickets)): ?>
                            <tr><td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No open support tickets.</td></tr>
                        <?php else: ?>
                            <?php foreach ($tickets as $t): ?>
                                <tr>
                                    <td style="font-family:monospace;color:#00f2fe;"><?= \App\Core\View::e($t['ticket_number']) ?></td>
                                    <td><strong><?= \App\Core\View::e($t['subject']) ?></strong></td>
                                    <td><span class="badge-action"><?= strtoupper($t['priority']) ?></span></td>
                                    <td><span class="badge badge-status-<?= $t['status'] === 'resolved' ? 'completed' : 'queued' ?>"><?= strtoupper($t['status']) ?></span></td>
                                    <td><?= \App\Core\View::e($t['assigned_name'] ?? 'Unassigned') ?></td>
                                    <td><?= date('M d, H:i', strtotime($t['created_at'])) ?></td>
                                    <td>
                                        <form method="POST" action="/tickets/status" style="display:flex;gap:4px;">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="ticket_id" value="<?= (int)$t['id'] ?>">
                                            <select name="status" class="global-search-input" style="height:28px;width:110px;padding-left:6px;font-size:11px;">
                                                <option value="open" <?= $t['status']==='open'?'selected':'' ?>>Open</option>
                                                <option value="pending" <?= $t['status']==='pending'?'selected':'' ?>>Pending</option>
                                                <option value="resolved" <?= $t['status']==='resolved'?'selected':'' ?>>Resolved</option>
                                                <option value="closed" <?= $t['status']==='closed'?'selected':'' ?>>Closed</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-secondary">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
