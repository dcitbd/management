<div class="projects-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Projects & Tasks Management</h1>
            <p class="page-subtitle">Track client deliverables, deadlines, priorities, and assigned staff</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('new-project-box').style.display='block'">
            <i class="bi bi-plus-lg"></i> New Project
        </button>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:8px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <div class="card" id="new-project-box" style="display:none;margin-bottom:20px;">
        <div class="card-header">
            <h3 class="card-title">Create Client Project</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('new-project-box').style.display='none'">Close</button>
        </div>
        <div class="card-body" style="padding:20px;">
            <form method="POST" action="/projects/store">
                <?= \App\Core\Csrf::field() ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:14px;">
                    <input type="text" name="title" required placeholder="Project Title *" class="global-search-input" style="padding-left:12px;">
                    <select name="service_id" class="global-search-input" style="padding-left:12px;">
                        <option value="">-- Related Service --</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?= (int)$s['id'] ?>"><?= \App\Core\View::e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="priority" class="global-search-input" style="padding-left:12px;">
                        <option value="medium">Medium Priority</option>
                        <option value="high">High Priority</option>
                        <option value="urgent">Urgent</option>
                    </select>
                    <input type="number" name="budget_bdt" placeholder="Budget (BDT)" class="global-search-input" style="padding-left:12px;">
                    <input type="date" name="due_date" class="global-search-input" style="padding-left:12px;">
                </div>
                <textarea name="description" rows="2" placeholder="Project scope and milestones..." class="global-search-input" style="height:auto;padding:10px;margin-bottom:12px;"></textarea>
                <button type="submit" class="btn btn-primary">Save Project</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Active Projects (<?= count($projects) ?>)</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Project Title</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Budget</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($projects)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:24px;color:#94a3b8;">No projects yet. Click "New Project" to create one.</td></tr>
                        <?php else: ?>
                            <?php foreach ($projects as $p): ?>
                                <tr>
                                    <td><strong><?= \App\Core\View::e($p['title']) ?></strong></td>
                                    <td><?= \App\Core\View::e($p['service_name'] ?? 'Custom') ?></td>
                                    <td><span class="badge badge-status-processing"><?= strtoupper($p['status']) ?></span></td>
                                    <td><span class="badge-action"><?= strtoupper($p['priority']) ?></span></td>
                                    <td><?= $p['budget_bdt'] ? '৳' . number_format((float)$p['budget_bdt']) : '-' ?></td>
                                    <td><?= \App\Core\View::e($p['due_date'] ?? 'TBD') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
