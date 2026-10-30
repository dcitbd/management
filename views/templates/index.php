<div class="templates-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Email Templates Library & Studio</h1>
            <p class="page-subtitle">Select any template to launch the Bulk Auto-Sender, or Add, Edit, Duplicate, and Delete templates</p>
        </div>
        <div class="action-buttons">
            <a href="/campaigns/send" class="btn btn-secondary">
                <i class="bi bi-rocket-takeoff-fill text-cyan"></i> Open Bulk Auto-Sender
            </a>
            <a href="/templates/create" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create New Template
            </a>
        </div>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <!-- Search & Filter Bar -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:18px 24px;">
            <form method="GET" action="/templates" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                <input type="text" name="q" value="<?= \App\Core\View::e($q) ?>" placeholder="Search templates by name or subject..." class="input-box" style="max-width:320px;">
                <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i> Search</button>
                <a href="/templates" class="btn btn-outline">Reset</a>
            </form>
        </div>
    </div>

    <!-- 3D Template Cards Grid -->
    <div class="catalog-3d-grid">
        <?php if (empty($templates)): ?>
            <div class="card" style="grid-column:1/-1;">
                <div class="card-body" style="text-align:center;padding:40px;">
                    <p class="text-muted" style="margin-bottom:16px;">No templates found. Click below to create your first HTML email template.</p>
                    <a href="/templates/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Create New Template</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($templates as $tpl): ?>
                <div class="card-3d">
                    <div>
                        <div class="card-3d-top" style="margin-bottom:14px;">
                            <div class="icon-orb-3d">
                                <i class="bi bi-envelope-paper-heart-fill"></i>
                            </div>
                            <span class="badge-action"><?= \App\Core\View::e($tpl['category']) ?></span>
                        </div>
                        <h3 class="card-3d-title"><?= \App\Core\View::e($tpl['name']) ?></h3>
                        <p class="card-3d-desc" style="margin-bottom:10px;">
                            <strong>Subject:</strong> <?= \App\Core\View::e($tpl['subject']) ?>
                        </p>
                        <div style="font-size:12px;" class="text-muted">
                            <i class="bi bi-person-check"></i> <?= \App\Core\View::e($tpl['sender_name'] . ' <' . $tpl['sender_email'] . '>') ?>
                        </div>
                    </div>
                    <div class="card-3d-meta">
                        <a href="/campaigns/send?template_id=<?= (int)$tpl['id'] ?>" class="btn btn-sm btn-primary" title="Select & Auto-Send Bulk Mail">
                            <i class="bi bi-rocket-takeoff-fill"></i> Select & Auto-Send
                        </a>
                        <div class="card-3d-actions">
                            <a href="/templates/edit/<?= (int)$tpl['id'] ?>" class="btn btn-sm btn-secondary" title="Edit Template">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form method="POST" action="/templates/duplicate" style="display:inline;">
                                <?= \App\Core\Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int)$tpl['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline" title="Duplicate"><i class="bi bi-files"></i></button>
                            </form>
                            <form method="POST" action="/templates/delete" style="display:inline;" onsubmit="return confirm('Delete this template?');">
                                <?= \App\Core\Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int)$tpl['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger-soft" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
