<div class="templates-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Email Templates Library</h1>
            <p class="page-subtitle">Responsive HTML email templates with safe placeholder merging ({{name}}, {{email}}, {{company}})</p>
        </div>
        <a href="/templates/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            <span>Create New Template</span>
        </a>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Saved Templates (<?= count($templates) ?>)</h3>
            <form method="GET" action="/templates" style="display:flex;gap:8px;">
                <input type="text" name="q" value="<?= \App\Core\View::e($q) ?>" placeholder="Search templates..." class="global-search-input" style="width:220px;height:32px;padding-left:10px;">
                <button type="submit" class="btn btn-sm btn-secondary">Search</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>Category</th>
                            <th>Email Subject</th>
                            <th>Sender Identity</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($templates)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:30px;color:#94a3b8;">
                                    No templates created yet. Click <a href="/templates/create" style="color:#00f2fe;">Create New Template</a> to build your first responsive HTML email.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($templates as $tpl): ?>
                                <tr>
                                    <td><strong><?= \App\Core\View::e($tpl['name']) ?></strong></td>
                                    <td><span class="badge-action"><?= \App\Core\View::e($tpl['category']) ?></span></td>
                                    <td><?= \App\Core\View::e($tpl['subject']) ?></td>
                                    <td><?= \App\Core\View::e($tpl['sender_name'] . ' <' . $tpl['sender_email'] . '>') ?></td>
                                    <td><?= date('M d, Y', strtotime($tpl['updated_at'])) ?></td>
                                    <td style="display:flex;gap:6px;">
                                        <a href="/templates/edit/<?= (int)$tpl['id'] ?>" class="btn btn-sm btn-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="/templates/duplicate" style="display:inline;">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$tpl['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline" title="Duplicate"><i class="bi bi-files"></i></button>
                                        </form>
                                        <form method="POST" action="/templates/delete" style="display:inline;" onsubmit="return confirm('Archive this template?');">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$tpl['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline" style="color:#ef4444;" title="Archive"><i class="bi bi-archive"></i></button>
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
