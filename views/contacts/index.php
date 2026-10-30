<div class="contacts-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Contacts & Client CRM</h1>
            <p class="page-subtitle">Add, edit, toggle opt-in consent, or delete client records and export CSV lists</p>
        </div>
        <div class="action-buttons">
            <a href="/contacts/export" class="btn btn-secondary"><i class="bi bi-download"></i> Export CSV</a>
            <button type="button" class="btn btn-primary" onclick="openContactCreate()">
                <i class="bi bi-person-plus-fill"></i> Add Contact
            </button>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <!-- Create / Edit Contact Card -->
    <div class="card" id="contact-form-card" style="display:none;">
        <div class="card-header">
            <h3 class="card-title" id="contact-form-title">Add Verified Contact</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('contact-form-card').style.display='none'">Close</button>
        </div>
        <div class="card-body">
            <form method="POST" action="/contacts/store" id="contactForm">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="id" id="ctEditId" value="">
                <div class="grid-3" style="margin-bottom:20px;">
                    <div>
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" id="ctEditFirst" placeholder="First Name" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="ctEditLast" placeholder="Last Name" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" id="ctEditEmail" required placeholder="Email Address *" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="ctEditPhone" placeholder="Phone Number" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Company / Organization</label>
                        <input type="text" name="company" id="ctEditCompany" placeholder="Company / Organization" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Lead Status</label>
                        <select name="lead_status" id="ctEditStatus" class="input-box">
                            <option value="lead">Lead</option>
                            <option value="prospect">Prospect</option>
                            <option value="customer">Customer</option>
                            <option value="partner">Partner</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="is_subscribed" id="ctEditSub" value="1">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save Contact</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">CRM Directory (<?= (int)$total ?> Total Contacts)</h3>
            <form method="GET" action="/contacts" style="display:flex;gap:10px;">
                <input type="text" name="q" value="<?= \App\Core\View::e($filters['q']) ?>" placeholder="Search name, email, company..." class="input-box" style="width:260px;">
                <button type="submit" class="btn btn-secondary">Search</button>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contacts)): ?>
                            <tr><td colspan="7" style="text-align:center;padding:28px;" class="text-muted">No contacts found. Click "Add Contact" to add your first client.</td></tr>
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
                                    <td style="display:flex;gap:6px;">
                                        <button type="button" class="btn btn-sm btn-secondary" onclick='openContactEdit(<?= json_encode($c) ?>)' title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form method="POST" action="/contacts/toggle-consent" style="display:inline;">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline" title="Toggle Consent">
                                                <?= $c['is_subscribed'] ? 'Unsub' : 'Opt-in' ?>
                                            </button>
                                        </form>
                                        <form method="POST" action="/contacts/delete" style="display:inline;" onsubmit="return confirm('Delete this contact?');">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger-soft" title="Delete"><i class="bi bi-trash"></i></button>
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
<script>
function openContactCreate() {
    const card = document.getElementById('contact-form-card');
    card.style.display = 'block';
    document.getElementById('contactForm').action = '/contacts/store';
    document.getElementById('contact-form-title').textContent = 'Add Verified Contact';
    document.getElementById('ctEditId').value = '';
    document.getElementById('ctEditFirst').value = '';
    document.getElementById('ctEditLast').value = '';
    document.getElementById('ctEditEmail').value = '';
    document.getElementById('ctEditPhone').value = '';
    document.getElementById('ctEditCompany').value = '';
    document.getElementById('ctEditStatus').value = 'lead';
    card.scrollIntoView({ behavior: 'smooth' });
}
function openContactEdit(c) {
    const card = document.getElementById('contact-form-card');
    card.style.display = 'block';
    document.getElementById('contactForm').action = '/contacts/update';
    document.getElementById('contact-form-title').textContent = 'Edit Contact: ' + c.email;
    document.getElementById('ctEditId').value = c.id;
    document.getElementById('ctEditFirst').value = c.first_name || '';
    document.getElementById('ctEditLast').value = c.last_name || '';
    document.getElementById('ctEditEmail').value = c.email || '';
    document.getElementById('ctEditPhone').value = c.phone || '';
    document.getElementById('ctEditCompany').value = c.company || '';
    document.getElementById('ctEditStatus').value = c.lead_status || 'lead';
    document.getElementById('ctEditSub').value = c.is_subscribed ? '1' : '0';
    card.scrollIntoView({ behavior: 'smooth' });
}
</script>
