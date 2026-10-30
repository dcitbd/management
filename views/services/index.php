<div class="services-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Service Catalog & Category Management</h1>
            <p class="page-subtitle">Add, edit, organize, or remove service categories and catalog offerings with 3D interactive cards</p>
        </div>
        <div class="action-buttons">
            <button type="button" class="btn btn-secondary" onclick="toggleCategoryManager()">
                <i class="bi bi-folder-plus"></i> Manage Categories (<?= count($categories) ?>)
            </button>
            <button type="button" class="btn btn-primary" onclick="openServiceCreateForm()">
                <i class="bi bi-plus-lg"></i> Add New Service
            </button>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($flash_error)): ?>
        <div class="alert alert-danger" style="margin-bottom:22px;padding:14px 20px;background:rgba(239,68,68,0.15);border:1px solid #ef4444;border-radius:12px;color:#f87171;">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= \App\Core\View::e($flash_error) ?>
        </div>
    <?php endif; ?>

    <!-- Category CRUD Manager Panel -->
    <div class="card" id="category-manager-card" style="display:none;">
        <div class="card-header">
            <div>
                <h3 class="card-title"><i class="bi bi-collection-fill text-cyan"></i> Catalog Categories Manager (Add / Edit / Delete)</h3>
                <span class="card-subtitle">Manage all service categories in your catalog</span>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleCategoryManager()">Close</button>
        </div>
        <div class="card-body">
            <form method="POST" action="/services/category/store" id="catForm" style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:22px;">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="id" id="catEditId" value="">
                <input type="text" name="name" id="catEditName" required class="input-box" style="flex:1;min-width:220px;" placeholder="Category Name (e.g., Cloud & DevOps) *">
                <input type="text" name="description" id="catEditDesc" class="input-box" style="flex:2;min-width:260px;" placeholder="Category short description...">
                <button type="submit" class="btn btn-primary" id="catSubmitBtn"><i class="bi bi-plus-circle"></i> Save Category</button>
                <button type="button" class="btn btn-outline" id="catCancelEditBtn" style="display:none;" onclick="resetCatForm()">Cancel Edit</button>
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Services</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><strong><?= \App\Core\View::e($cat['name']) ?></strong></td>
                                <td class="text-muted"><?= \App\Core\View::e($cat['description'] ?? '-') ?></td>
                                <td><span class="badge-action"><?= (int)($cat['service_count'] ?? 0) ?> items</span></td>
                                <td style="display:flex;gap:8px;">
                                    <button type="button" class="btn btn-sm btn-secondary"
                                            onclick='editCategory(<?= (int)$cat["id"] ?>, <?= json_encode($cat["name"]) ?>, <?= json_encode($cat["description"] ?? "") ?>)'>
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form method="POST" action="/services/category/delete" style="display:inline;" onsubmit="return confirm('Delete this category and its services?');">
                                        <?= \App\Core\Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger-soft"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create / Edit Service Form Card -->
    <div class="card" id="service-form-card" style="display:none;">
        <div class="card-header">
            <h3 class="card-title" id="service-form-title"><i class="bi bi-plus-circle text-cyan"></i> Add New Service to Catalog</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('service-form-card').style.display='none'">Close</button>
        </div>
        <div class="card-body">
            <form method="POST" action="/services/store" id="serviceForm">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="id" id="svcEditId" value="">
                <div class="grid-3" style="margin-bottom:20px;">
                    <div>
                        <label class="form-label">Service Name *</label>
                        <input type="text" name="name" id="svcEditName" required class="input-box" placeholder="e.g., Custom ERP Development">
                    </div>
                    <div>
                        <label class="form-label">Category *</label>
                        <select name="category_id" id="svcEditCat" required class="input-box">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"><?= \App\Core\View::e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Base Price (BDT)</label>
                        <input type="number" step="0.01" name="price_bdt" id="svcEditPriceBdt" class="input-box" placeholder="25000">
                    </div>
                    <div>
                        <label class="form-label">Pricing Model / Label</label>
                        <input type="text" name="price_model" id="svcEditPriceModel" value="Custom Quote" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Icon Class (Bootstrap Icon)</label>
                        <input type="text" name="icon_class" id="svcEditIcon" value="bi-layers-fill" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Visibility Status</label>
                        <select name="is_active" id="svcEditActive" class="input-box">
                            <option value="1">Active (Visible in Catalog)</option>
                            <option value="0">Inactive / Draft</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:20px;">
                    <label class="form-label">Short Summary *</label>
                    <input type="text" name="short_description" id="svcEditShort" required class="input-box" placeholder="Concise description of service deliverables">
                </div>
                <div style="margin-bottom:22px;">
                    <label class="form-label">Full Technical Scope & Deliverables</label>
                    <textarea name="full_description" id="svcEditFull" rows="3" class="input-box"></textarea>
                </div>
                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn btn-primary" id="svcSubmitBtn"><i class="bi bi-check2-circle"></i> Save Service</button>
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('service-form-card').style.display='none'">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter & View Switcher Bar -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:20px 24px;">
            <form method="GET" action="/services" style="display:flex;flex-wrap:wrap;gap:14px;align-items:center;justify-content:space-between;">
                <div style="display:flex;flex-wrap:wrap;gap:12px;flex:1;">
                    <input type="text" name="q" value="<?= \App\Core\View::e($filters['q']) ?>" placeholder="Search service name or description..." class="input-box" style="max-width:300px;">
                    <select name="category_id" class="input-box" style="max-width:250px;">
                        <option value="">All Categories (<?= count($categories) ?>)</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" <?= (string)$filters['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>>
                                <?= \App\Core\View::e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="/services" class="btn btn-outline">Reset</a>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" class="btn btn-sm btn-secondary" id="btnViewCards" onclick="switchCatalogView('cards')"><i class="bi bi-grid-3x3-gap-fill"></i> 3D Cards</button>
                    <button type="button" class="btn btn-sm btn-outline" id="btnViewTable" onclick="switchCatalogView('table')"><i class="bi bi-table"></i> Table</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3D Cards Grid View -->
    <div id="catalog-cards-view" class="catalog-3d-grid">
        <?php foreach ($services as $svc): ?>
            <div class="card-3d">
                <div>
                    <div class="card-3d-top" style="margin-bottom:16px;">
                        <div class="icon-orb-3d">
                            <i class="bi <?= \App\Core\View::e($svc['icon_class'] ?: 'bi-layers-fill') ?>"></i>
                        </div>
                        <span class="badge <?= $svc['is_active'] ? 'badge-status-completed' : 'badge-status-draft' ?>">
                            <?= $svc['is_active'] ? 'ACTIVE' : 'INACTIVE' ?>
                        </span>
                    </div>
                    <div style="margin-bottom:8px;">
                        <span class="badge-action"><?= \App\Core\View::e($svc['category_name']) ?></span>
                    </div>
                    <h3 class="card-3d-title"><?= \App\Core\View::e($svc['name']) ?></h3>
                    <p class="card-3d-desc"><?= \App\Core\View::e($svc['short_description']) ?></p>
                </div>
                <div class="card-3d-meta">
                    <span class="price-tag-3d">
                        <?= $svc['price_bdt'] ? '৳' . number_format((float)$svc['price_bdt']) : \App\Core\View::e($svc['price_model']) ?>
                    </span>
                    <div class="card-3d-actions">
                        <button type="button" class="btn btn-sm btn-secondary"
                                onclick='openServiceEditForm(<?= json_encode($svc) ?>)' title="Edit Service">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <form method="POST" action="/services/delete" style="display:inline;" onsubmit="return confirm('Delete this service from catalog?');">
                            <?= \App\Core\Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= (int)$svc['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger-soft" title="Delete Service"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Table View -->
    <div class="card" id="catalog-table-view" style="display:none;">
        <div class="card-header">
            <h3 class="card-title">Services Catalog Table (<?= count($services) ?> Services)</h3>
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
                                <td style="max-width:340px;" class="text-muted"><?= \App\Core\View::e($svc['short_description']) ?></td>
                                <td>
                                    <?= $svc['price_bdt'] ? '৳' . number_format((float)$svc['price_bdt']) : \App\Core\View::e($svc['price_model']) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $svc['is_active'] ? 'badge-status-completed' : 'badge-status-draft' ?>">
                                        <?= $svc['is_active'] ? 'ACTIVE' : 'INACTIVE' ?>
                                    </span>
                                </td>
                                <td style="display:flex;gap:8px;">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick='openServiceEditForm(<?= json_encode($svc) ?>)'>
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form method="POST" action="/services/delete" style="display:inline;" onsubmit="return confirm('Delete this service?');">
                                        <?= \App\Core\Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$svc['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger-soft"><i class="bi bi-trash"></i></button>
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

<script>
function toggleCategoryManager() {
    const el = document.getElementById('category-manager-card');
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
function editCategory(id, name, desc) {
    document.getElementById('catForm').action = '/services/category/update';
    document.getElementById('catEditId').value = id;
    document.getElementById('catEditName').value = name;
    document.getElementById('catEditDesc').value = desc;
    document.getElementById('catSubmitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Update Category';
    document.getElementById('catCancelEditBtn').style.display = 'inline-flex';
}
function resetCatForm() {
    document.getElementById('catForm').action = '/services/category/store';
    document.getElementById('catEditId').value = '';
    document.getElementById('catEditName').value = '';
    document.getElementById('catEditDesc').value = '';
    document.getElementById('catSubmitBtn').innerHTML = '<i class="bi bi-plus-circle"></i> Save Category';
    document.getElementById('catCancelEditBtn').style.display = 'none';
}
function openServiceCreateForm() {
    const card = document.getElementById('service-form-card');
    card.style.display = 'block';
    document.getElementById('serviceForm').action = '/services/store';
    document.getElementById('service-form-title').innerHTML = '<i class="bi bi-plus-circle text-cyan"></i> Add New Service to Catalog';
    document.getElementById('svcEditId').value = '';
    document.getElementById('svcEditName').value = '';
    document.getElementById('svcEditPriceBdt').value = '';
    document.getElementById('svcEditPriceModel').value = 'Custom Quote';
    document.getElementById('svcEditShort').value = '';
    document.getElementById('svcEditFull').value = '';
    document.getElementById('svcEditActive').value = '1';
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function openServiceEditForm(svc) {
    const card = document.getElementById('service-form-card');
    card.style.display = 'block';
    document.getElementById('serviceForm').action = '/services/update';
    document.getElementById('service-form-title').innerHTML = '<i class="bi bi-pencil-square text-cyan"></i> Edit Service: ' + svc.name;
    document.getElementById('svcEditId').value = svc.id;
    document.getElementById('svcEditName').value = svc.name || '';
    document.getElementById('svcEditCat').value = svc.category_id;
    document.getElementById('svcEditPriceBdt').value = svc.price_bdt || '';
    document.getElementById('svcEditPriceModel').value = svc.price_model || 'Custom Quote';
    document.getElementById('svcEditIcon').value = svc.icon_class || 'bi-layers-fill';
    document.getElementById('svcEditShort').value = svc.short_description || '';
    document.getElementById('svcEditFull').value = svc.full_description || '';
    document.getElementById('svcEditActive').value = svc.is_active ? '1' : '0';
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function switchCatalogView(mode) {
    document.getElementById('catalog-cards-view').style.display = mode === 'cards' ? 'grid' : 'none';
    document.getElementById('catalog-table-view').style.display = mode === 'table' ? 'block' : 'none';
    document.getElementById('btnViewCards').className = mode === 'cards' ? 'btn btn-sm btn-secondary' : 'btn btn-sm btn-outline';
    document.getElementById('btnViewTable').className = mode === 'table' ? 'btn btn-sm btn-secondary' : 'btn btn-sm btn-outline';
}
</script>
