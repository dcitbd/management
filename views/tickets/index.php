<div class="tickets-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Customer Support Tickets</h1>
            <p class="page-subtitle">SLA issue tracking, priority assignment, status updates, and ticket management</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-ticket-box').style.display='block'">
            <i class="bi bi-plus-lg"></i> Open Support Ticket
        </button>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <div class="card" id="new-ticket-box" style="display:none;">
        <div class="card-header">
            <h3 class="card-title">Create Support Ticket</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('new-ticket-box').style.display='none'">Close</button>
        </div>
        <div class="card-body">
            <form method="POST" action="/tickets/store">
                <?= \App\Core\Csrf::field() ?>
                <div class="grid-2" style="margin-bottom:16px;">
                    <div>
                        <label class="form-label">Ticket Subject *</label>
                        <input type="text" name="subject" required placeholder="Ticket Subject *" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Priority</label>
                        <select name="priority" class="input-box">
                            <option value="medium">Priority: Medium</option>
                            <option value="high">Priority: High</option>
                            <option value="urgent">Priority: Urgent</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:18px;">
                    <label class="form-label">Detailed Issue Description *</label>
                    <textarea name="description" rows="3" required placeholder="Detailed issue description..." class="input-box"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Submit Ticket</button>
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
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tickets)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:28px;" class="text-muted">No open support tickets.</td></tr>
                        <?php else: ?>
                            <?php foreach ($tickets as $t): ?>
                                <tr>
                                    <td style="font-family:var(--font-mono);color:#00f2fe;"><?= \App\Core\View::e($t['ticket_number']) ?></td>
                                    <td><strong><?= \App\Core\View::e($t['subject']) ?></strong></td>
                                    <td><span class="badge-action"><?= strtoupper($t['priority']) ?></span></td>
                                    <td><span class="badge badge-status-<?= $t['status'] === 'resolved' ? 'completed' : 'queued' ?>"><?= strtoupper($t['status']) ?></span></td>
                                    <td><?= date('M d, H:i', strtotime($t['created_at'])) ?></td>
                                    <td style="display:flex;gap:8px;align-items:center;">
                                        <form method="POST" action="/tickets/status" style="display:flex;gap:6px;align-items:center;">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="ticket_id" value="<?= (int)$t['id'] ?>">
                                            <select name="status" class="input-box" style="min-height:34px;padding:4px 10px;width:120px;font-size:12px;">
                                                <option value="open" <?= $t['status']==='open'?'selected':'' ?>>Open</option>
                                                <option value="pending" <?= $t['status']==='pending'?'selected':'' ?>>Pending</option>
                                                <option value="resolved" <?= $t['status']==='resolved'?'selected':'' ?>>Resolved</option>
                                                <option value="closed" <?= $t['status']==='closed'?'selected':'' ?>>Closed</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-secondary">Update</button>
                                        </form>
                                        <form method="POST" action="/tickets/delete" style="display:inline;" onsubmit="return confirm('Delete this ticket?');">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="ticket_id" value="<?= (int)$t['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger-soft"><i class="bi bi-trash"></i></button>
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
