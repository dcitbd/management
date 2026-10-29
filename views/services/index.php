<div class="services-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Service Catalog & Management</h1>
            <p class="page-subtitle">Manage all 10 Dream Career IT Bangladesh categories and 28+ specialized digital services</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-service-card').style.display='block'">
            <i class="bi bi-plus-lg"></i>
            <span>Add New Service</span>
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

    <!-- Create Service Form Card (Hidden by default) -->
    <div class="card" id="new-service-card" style="display:none;margin-bottom:24px;">
        <div class="card-header">
            <h3 class="card-title">Create New IT Service</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('new-service-card').style.display='none'">Cancel</button>
        </div>
        <div class="card-body" style="padding:20px;">
            <form method="POST" action="/services/store">
                <?= \App\Core\Csrf::field() ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Service Name *</label>
                        <input type="text" name="name" required class="global-search-input" style="padding-left:12px;" placeholder="e.g., Custom ERP Development">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Category *</label>
                        <select name="category_id" required class="global-search-input" style="padding-left:12px;">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"><?= \App\Core\View::e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Base Price (BDT)</label>
                        <input type="number" step="0.01" name="price_bdt" class="global-search-input" style="padding-left:12px;" placeholder="25000">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Pricing Model</label>
                        <input type="text" name="price_model" value="Custom Quote" class="global-search-input" style="padding-left:12px;">
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Short Summary</label>
                    <input type="text" name="short_description" class="global-search-input" style="padding-left:12px;" placeholder="Concise description of deliverables">
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;color:#94a3b8;margin-bottom:6px;">Full Technical Scope</label>
                    <textarea name="full_description" rows="3" class="global-search-input" style="height:auto;padding:10px;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save Service</button>
            </form>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:16px;">
            <form method="GET" action="/services" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
                <input type="text" name="q" value="<?= \App\Core\View::e($filters['q']) ?>" placeholder="Search service name or description..." class="global-search-input" style="width:280px;padding-left:14px;">
                <select name="category_id" class="global-search-input" style="width:240px;padding-left:12px;">
                    <option value="">All 10 Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= (string)$filters['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>>
                            <?= \App\Core\View::e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="/services" class="btn btn-outline">Reset</a>
            </form>
        </div>
    </div>

    <!-- Services Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Active Services Catalog (<?= count($services) ?> Services)</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Summary</th>
                            <th>Pricing</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                            <tr>
                                <td>
                                    <i class="bi <?= \App\Core\View::e($svc['icon_class']) ?> text-cyan" style="margin-right:8px;"></i>
                                    <strong><?= \App\Core\View::e($svc['name']) ?></strong>
                                </td>
                                <td><span class="badge-action"><?= \App\Core\View::e($svc['category_name']) ?></span></td>
                                <td style="max-width:340px;color:#94a3b8;"><?= \App\Core\View::e($svc['short_description']) ?></td>
                                <td>
                                    <?= $svc['price_bdt'] ? '৳' . number_format((float)$svc['price_bdt']) : \App\Core\View::e($svc['price_model']) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $svc['is_active'] ? 'badge-status-completed' : 'badge-status-draft' ?>">
                                        <?= $svc['is_active'] ? 'ACTIVE' : 'INACTIVE' ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="/services/delete" style="display:inline;" onsubmit="return confirm('Delete this service?');">
                                        <?= \App\Core\Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$svc['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color:#ef4444;"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
