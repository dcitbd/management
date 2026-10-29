<div class="users-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">User Management & Role-Based Access Control (RBAC)</h1>
            <p class="page-subtitle">Roles: Administrator, Manager, Staff, and Read-only</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-user-card').style.display='block'">
            <i class="bi bi-person-plus-fill"></i> Create Team User
        </button>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger" style="margin-bottom:16px;padding:12px 16px;background:rgba(239,68,68,0.15);border:1px solid #ef4444;border-radius:8px;color:#f87171;">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= \App\Core\View::e($flash_error) ?>
        </div>
    <?php endif; ?>

    <div class="card" id="new-user-card" style="display:none;margin-bottom:20px;">
        <div class="card-header">
            <h3 class="card-title">Add New Team Member</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('new-user-card').style.display='none'">Close</button>
        </div>
        <div class="card-body" style="padding:20px;">
            <form method="POST" action="/users/store">
                <?= \App\Core\Csrf::field() ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:14px;">
                    <input type="text" name="name" required placeholder="Full Name *" class="global-search-input" style="padding-left:12px;">
                    <input type="email" name="email" required placeholder="Email Address *" class="global-search-input" style="padding-left:12px;">
                    <input type="text" name="phone" placeholder="Phone Number" class="global-search-input" style="padding-left:12px;">
                    <select name="role_id" class="global-search-input" style="padding-left:12px;">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int)$r['id'] ?>"><?= \App\Core\View::e($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="password" name="password" required minlength="8" placeholder="Password (min 8 chars) *" class="global-search-input" style="padding-left:12px;">
                </div>
                <button type="submit" class="btn btn-primary">Create User Account</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Registered Accounts (<?= count($users) ?>)</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><strong><?= \App\Core\View::e($u['name']) ?></strong></td>
                                <td><?= \App\Core\View::e($u['email']) ?></td>
                                <td><span class="badge-action"><?= \App\Core\View::e($u['role_name']) ?></span></td>
                                <td><span class="badge <?= $u['is_active'] ? 'badge-status-completed' : 'badge-status-failed' ?>"><?= $u['is_active'] ? 'ACTIVE' : 'DISABLED' ?></span></td>
                                <td><?= $u['last_login_at'] ? date('M d, H:i', strtotime($u['last_login_at'])) : 'Never' ?></td>
                                <td style="font-family:monospace;"><?= \App\Core\View::e($u['last_login_ip'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
