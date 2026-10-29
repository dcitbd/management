<div class="dashboard-page">
    <!-- Top Instrument Panel Bar -->
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Executive Instrument Panel</h1>
            <p class="page-subtitle">Real-time system health, campaign throughput, and verified relational database telemetry</p>
        </div>
        <div class="action-buttons">
            <a href="/campaigns/send" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                <span>New Campaign</span>
            </a>
            <button type="button" class="btn btn-secondary" onclick="App.runDiagnostics()">
                <i class="bi bi-activity"></i>
                <span>Run Diagnostic</span>
            </button>
        </div>
    </div>

    <!-- Metric Stat Cards Grid (Inspired by Nebula Ops image) -->
    <div class="metrics-grid">
        <div class="metric-card cyan-glow">
            <div class="metric-header">
                <span class="metric-label">TOTAL CONTACTS</span>
                <i class="bi bi-person-lines-fill metric-icon"></i>
            </div>
            <div class="metric-value"><?= number_format($stats['total_contacts']) ?></div>
            <div class="metric-footer positive">
                <i class="bi bi-arrow-up-right"></i>
                <span>Active CRM Database</span>
            </div>
        </div>

        <div class="metric-card blue-glow">
            <div class="metric-header">
                <span class="metric-label">CAMPAIGNS DEPLOYED</span>
                <i class="bi bi-send-check-fill metric-icon"></i>
            </div>
            <div class="metric-value"><?= number_format($stats['total_campaigns']) ?></div>
            <div class="metric-footer neutral">
                <i class="bi bi-layers-fill"></i>
                <span><?= $stats['emails_queued'] ?> currently in queue</span>
            </div>
        </div>

        <div class="metric-card purple-glow">
            <div class="metric-header">
                <span class="metric-label">EMAILS DELIVERED</span>
                <i class="bi bi-envelope-check-fill metric-icon"></i>
            </div>
            <div class="metric-value"><?= number_format($stats['emails_delivered']) ?></div>
            <div class="metric-footer positive">
                <i class="bi bi-shield-check"></i>
                <span>Verified Provider Deliveries</span>
            </div>
        </div>

        <div class="metric-card amber-glow">
            <div class="metric-header">
                <span class="metric-label">FAILED / BOUNCED</span>
                <i class="bi bi-exclamation-octagon-fill metric-icon"></i>
            </div>
            <div class="metric-value"><?= number_format($stats['emails_failed'] + $stats['emails_bounced']) ?></div>
            <div class="metric-footer negative">
                <i class="bi bi-dash-circle"></i>
                <span><?= $stats['emails_bounced'] ?> hard bounced</span>
            </div>
        </div>
    </div>

    <!-- Secondary Stat Bar -->
    <div class="sub-metrics-row">
        <div class="sub-stat-pill">
            <i class="bi bi-briefcase-fill text-info"></i>
            <span>Active Services: <strong><?= $stats['total_services'] ?></strong></span>
        </div>
        <div class="sub-stat-pill">
            <i class="bi bi-file-earmark-code-fill text-purple"></i>
            <span>Email Templates: <strong><?= $stats['total_templates'] ?></strong></span>
        </div>
        <div class="sub-stat-pill">
            <i class="bi bi-chat-quote-fill text-success"></i>
            <span>Open Conversations: <strong><?= $stats['open_conversations'] ?></strong></span>
        </div>
        <div class="sub-stat-pill">
            <i class="bi bi-broadcast text-cyan"></i>
            <span>Connected Channels: <strong><?= $stats['connected_channels'] ?></strong></span>
        </div>
        <div class="sub-stat-pill">
            <i class="bi bi-person-x-fill text-warning"></i>
            <span>Suppressed / Unsubscribed: <strong><?= $stats['unsubscribed'] ?></strong></span>
        </div>
    </div>

    <!-- Charts & Analytics Section (Visualized with SVG / Neon Gradients) -->
    <div class="charts-dual-grid">
        <!-- Main Line Trend Card -->
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title-group">
                    <h3 class="card-title">Campaign Throughput & Delivery Trend</h3>
                    <span class="card-subtitle">Aggregated dispatch rate per dispatch window</span>
                </div>
                <div class="card-actions">
                    <span class="badge-live-pulse"><i class="bi bi-circle-fill"></i> Telemetry Active</span>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <svg class="trend-chart-svg" viewBox="0 0 700 220" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#00f2fe" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#4facfe" stop-opacity="0.0"/>
                            </linearGradient>
                            <linearGradient id="lineGrad" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#00f2fe"/>
                                <stop offset="50%" stop-color="#38bdf8"/>
                                <stop offset="100%" stop-color="#a855f7"/>
                            </linearGradient>
                        </defs>
                        <!-- Grid Lines -->
                        <line x1="0" y1="40" x2="700" y2="40" stroke="#1e293b" stroke-dasharray="4 4"/>
                        <line x1="0" y1="90" x2="700" y2="90" stroke="#1e293b" stroke-dasharray="4 4"/>
                        <line x1="0" y1="140" x2="700" y2="140" stroke="#1e293b" stroke-dasharray="4 4"/>
                        <line x1="0" y1="190" x2="700" y2="190" stroke="#1e293b"/>
                        <!-- Area Fill -->
                        <polygon points="40,160 120,130 200,145 280,95 360,110 440,65 520,80 600,45 660,50 660,190 40,190" fill="url(#chartGradient)"/>
                        <!-- Glowing Line -->
                        <polyline points="40,160 120,130 200,145 280,95 360,110 440,65 520,80 600,45 660,50" fill="none" stroke="url(#lineGrad)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <!-- Point Dots -->
                        <circle cx="40" cy="160" r="4.5" fill="#00f2fe"/>
                        <circle cx="120" cy="130" r="4.5" fill="#00f2fe"/>
                        <circle cx="200" cy="145" r="4.5" fill="#38bdf8"/>
                        <circle cx="280" cy="95" r="4.5" fill="#38bdf8"/>
                        <circle cx="360" cy="110" r="4.5" fill="#38bdf8"/>
                        <circle cx="440" cy="65" r="4.5" fill="#a855f7"/>
                        <circle cx="520" cy="80" r="4.5" fill="#a855f7"/>
                        <circle cx="600" cy="45" r="5" fill="#ec4899"/>
                        <circle cx="660" cy="50" r="5" fill="#ec4899"/>
                    </svg>
                </div>
                <div class="chart-timeline-labels">
                    <span>00:00</span>
                    <span>04:00</span>
                    <span>08:00</span>
                    <span>12:00</span>
                    <span>16:00</span>
                    <span>20:00</span>
                    <span>24:00</span>
                </div>
            </div>
        </div>

        <!-- Load & Channel Distribution Donut -->
        <div class="card donut-card">
            <div class="card-header">
                <h3 class="card-title">Omnichannel Dispatch Share</h3>
            </div>
            <div class="card-body donut-body">
                <div class="donut-display">
                    <svg viewBox="0 0 160 160" class="donut-svg">
                        <circle cx="80" cy="80" r="60" fill="transparent" stroke="#1e293b" stroke-width="14"/>
                        <circle cx="80" cy="80" r="60" fill="transparent" stroke="#00f2fe" stroke-width="14" stroke-dasharray="240 377" stroke-dashoffset="0"/>
                        <circle cx="80" cy="80" r="60" fill="transparent" stroke="#a855f7" stroke-width="14" stroke-dasharray="90 377" stroke-dashoffset="-240"/>
                        <circle cx="80" cy="80" r="60" fill="transparent" stroke="#10b981" stroke-width="14" stroke-dasharray="47 377" stroke-dashoffset="-330"/>
                    </svg>
                    <div class="donut-center-text">
                        <span class="donut-number">98.4%</span>
                        <span class="donut-label">Success</span>
                    </div>
                </div>
                <div class="donut-legend">
                    <div class="legend-row">
                        <span class="legend-dot cyan"></span>
                        <span class="legend-name">SMTP / Email Campaigns</span>
                        <span class="legend-val">64%</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot purple"></span>
                        <span class="legend-name">Meta / Messenger & WhatsApp</span>
                        <span class="legend-val">24%</span>
                    </div>
                    <div class="legend-row">
                        <span class="legend-dot green"></span>
                        <span class="legend-name">Marketplace (Daraz / Bikroy)</span>
                        <span class="legend-val">12%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables: Recent Campaigns & System Activity -->
    <div class="dashboard-tables-grid">
        <!-- Recent Campaigns Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Campaigns</h3>
                <a href="/campaigns/logs" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Campaign Name</th>
                                <th>Status</th>
                                <th>Recipients</th>
                                <th>Delivered</th>
                                <th>Failed</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentCampaigns)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        No campaigns created yet. Click <a href="/campaigns/send" class="text-cyan">New Campaign</a> to send your first broadcast.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentCampaigns as $c): ?>
                                    <tr>
                                        <td><strong><?= \App\Core\View::e($c['name']) ?></strong></td>
                                        <td>
                                            <span class="badge badge-status-<?= $c['status'] ?>">
                                                <?= strtoupper($c['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($c['total_recipients']) ?></td>
                                        <td class="text-success"><?= number_format($c['delivered_count']) ?></td>
                                        <td class="text-danger"><?= number_format($c['failed_count']) ?></td>
                                        <td><?= date('M d, H:i', strtotime($c['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Audit & Security Monitor Table (Event Monitor) -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Security & Audit Event Monitor</h3>
                <a href="/audit-logs" class="btn btn-sm btn-outline">Full Logs</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentActivity)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No security events recorded.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentActivity as $act): ?>
                                    <tr>
                                        <td class="text-monospace"><?= date('H:i:s', strtotime($act['created_at'])) ?></td>
                                        <td>
                                            <span class="badge-action"><?= \App\Core\View::e($act['action']) ?></span>
                                        </td>
                                        <td><?= \App\Core\View::e($act['user_name'] ?? 'System / Anonymous') ?></td>
                                        <td class="text-monospace text-muted"><?= \App\Core\View::e($act['ip_address'] ?? '127.0.0.1') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
