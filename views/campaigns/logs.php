<div class="campaign-logs-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Campaign Queue, Delivery & Bounce Logs</h1>
            <p class="page-subtitle">Real-time tracking of Sent, Verified Delivered, Queued, Failed, and Suppressed email dispatches</p>
        </div>
        <div class="action-buttons">
            <form method="POST" action="/campaigns/process-queue" style="display:inline;">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="btn btn-secondary"><i class="bi bi-play-circle-fill text-cyan"></i> Process Remaining Queue</button>
            </form>
            <a href="/campaigns/send" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Send New Email</a>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <!-- Campaigns History Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Campaign Execution History</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Campaign Name</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Queued</th>
                            <th>Sent (MTA)</th>
                            <th>Delivered</th>
                            <th>Failed / Suppressed</th>
                            <th>Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($campaigns)): ?>
                            <tr><td colspan="9" style="text-align:center;padding:28px;" class="text-muted">No campaigns recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($campaigns as $c): ?>
                                <tr>
                                    <td>#<?= (int)$c['id'] ?></td>
                                    <td><strong><?= \App\Core\View::e($c['name']) ?></strong></td>
                                    <td><span class="badge badge-status-<?= $c['status'] ?>"><?= strtoupper($c['status']) ?></span></td>
                                    <td><?= (int)$c['total_recipients'] ?></td>
                                    <td><?= (int)$c['queued_count'] ?></td>
                                    <td class="text-cyan"><?= (int)$c['sent_count'] ?></td>
                                    <td class="text-success"><?= (int)$c['delivered_count'] ?></td>
                                    <td class="text-danger"><?= (int)$c['failed_count'] ?></td>
                                    <td style="display:flex;gap:6px;">
                                        <form method="POST" action="/campaigns/control" style="display:inline;">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="campaign_id" value="<?= (int)$c['id'] ?>">
                                            <?php if ($c['status'] === 'queued' || $c['status'] === 'processing'): ?>
                                                <input type="hidden" name="action" value="paused">
                                                <button type="submit" class="btn btn-sm btn-outline">Pause</button>
                                            <?php elseif ($c['status'] === 'paused'): ?>
                                                <input type="hidden" name="action" value="queued">
                                                <button type="submit" class="btn btn-sm btn-secondary">Resume</button>
                                            <?php endif; ?>
                                        </form>
                                        <form method="POST" action="/campaigns/control" style="display:inline;" onsubmit="return confirm('Delete this campaign record?');">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="campaign_id" value="<?= (int)$c['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
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

    <!-- SMTP Delivery & Provider Event Logs -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Granular Email Delivery & Recipient Logs</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Recipient Email</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Provider Message ID</th>
                            <th>SMTP / Transport Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deliveryLogs)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:28px;" class="text-muted">No delivery logs yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($deliveryLogs as $log): ?>
                                <tr>
                                    <td><?= date('M d, H:i:s', strtotime($log['created_at'])) ?></td>
                                    <td><strong><?= \App\Core\View::e($log['recipient_email']) ?></strong></td>
                                    <td><?= \App\Core\View::e($log['subject']) ?></td>
                                    <td><span class="badge badge-status-<?= $log['status'] === 'sent' || $log['status'] === 'delivered' ? 'completed' : 'failed' ?>"><?= strtoupper($log['status']) ?></span></td>
                                    <td style="font-family:var(--font-mono);font-size:11.5px;"><?= \App\Core\View::e($log['provider_message_id'] ?? 'N/A') ?></td>
                                    <td class="text-muted" style="font-size:12.5px;"><?= \App\Core\View::e($log['response_message']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
