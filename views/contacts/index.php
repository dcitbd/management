<div class="contacts-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Contacts & Client CRM</h1>
            <p class="page-subtitle">Manage leads, customers, email opt-in consent records, and CSV exports</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="/contacts/export" class="btn btn-secondary"><i class="bi bi-download"></i> Export CSV</a>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('new-contact-card').style.display='block'">
                <i class="bi bi-person-plus-fill"></i> Add Contact
            </button>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <!-- New Contact Form -->
    <div class="card" id="new-contact-card" style="display:none;margin-bottom:20px;">
        <div class="card-header">
            <h3 class="card-title">Add Verified Contact</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('new-contact-card').style.display='none'">Close</button>
        </div>
        <div class="card-body" style="padding:20px;">
            <form method="POST" action="/contacts/store">
                <?= \App\Core\Csrf::field() ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:14px;">
                    <input type="text" name="first_name" placeholder="First Name" class="global-search-input" style="padding-left:12px;">
                    <input type="text" name="last_name" placeholder="Last Name" class="global-search-input" style="padding-left:12px;">
                    <input type="email" name="email" required placeholder="Email Address *" class="global-search-input" style="padding-left:12px;">
                    <input type="text" name="phone" placeholder="Phone Number" class="global-search-input" style="padding-left:12px;">
                    <input type="text" name="company" placeholder="Company / Organization" class="global-search-input" style="padding-left:12px;">
                    <select name="lead_status" class="global-search-input" style="padding-left:12px;">
                        <option value="lead">Lead</option>
                        <option value="prospect">Prospect</option>
                        <option value="customer">Customer</option>
                        <option value="partner">Partner</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Contact & Record Opt-in</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">CRM Directory (<?= (int)$total ?> Total Contacts)</h3>
            <form method="GET" action="/contacts" style="display:flex;gap:8px;">
                <input type="text" name="q" value="<?= \App\Core\View::e($filters['q']) ?>" placeholder="Search name, email, company..." class="global-search-input" style="width:240px;height:32px;padding-left:10px;">
                <button type="submit" class="btn btn-sm btn-secondary">Search</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Consent</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contacts)): ?>
                            <tr><td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No contacts found. Click "Add Contact" to add your first client.</td></tr>
                        <?php else: ?>
                            <?php foreach ($contacts as $c): ?>
                                <tr>
                                    <td><strong><?= \App\Core\View::e(trim($c['first_name'] . ' ' . $c['last_name']) ?: 'N/A') ?></strong></td>
                                    <td><?= \App\Core\View::e($c['email']) ?></td>
                                    <td><?= \App\Core\View::e($c['phone'] ?? '-') ?></td>
                                    <td><?= \App\Core\View::e($c['company'] ?? '-') ?></td>
                                    <td><span class="badge-action"><?= strtoupper($c['lead_status']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $c['is_subscribed'] ? 'badge-status-completed' : 'badge-status-failed' ?>">
                                            <?= $c['is_subscribed'] ? 'OPTED-IN' : 'UNSUBSCRIBED' ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
