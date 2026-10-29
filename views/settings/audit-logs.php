<div class="audit-logs-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Security & Activity Audit Logs</h1>
            <p class="page-subtitle">Immutable ledger of authentication attempts, role changes, campaign dispatches, and administrative actions</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Recent Security Telemetry (Last 150 Events)</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Metadata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:24px;color:#94a3b8;">No audit events recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $l): ?>
                                <tr>
                                    <td style="font-family:monospace;font-size:12px;"><?= date('Y-m-d H:i:s', strtotime($l['created_at'])) ?></td>
                                    <td><span class="badge-action"><?= \App\Core\View::e($l['action']) ?></span></td>
                                    <td><?= \App\Core\View::e($l['entity_type']) ?> <?= $l['entity_id'] ? '#' . (int)$l['entity_id'] : '' ?></td>
                                    <td><?= \App\Core\View::e($l['user_name'] ?? 'System / Guest') ?></td>
                                    <td style="font-family:monospace;"><?= \App\Core\View::e($l['ip_address'] ?? '-') ?></td>
                                    <td style="font-family:monospace;font-size:11px;color:#94a3b8;max-width:280px;overflow:hidden;text-overflow:ellipsis;"><?= \App\Core\View::e($l['new_values'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
