<div class="projects-page">
    <div class="page-title-row">
        <div>
            <h1 class="page-title">Projects & Tasks Management</h1>
            <p class="page-subtitle">Create, edit, and manage client deliverables, budgets, and deadlines</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openProjectCreate()">
            <i class="bi bi-plus-lg"></i> New Project
        </button>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success" style="margin-bottom:22px;padding:14px 20px;background:rgba(16,185,129,0.15);border:1px solid #10b981;border-radius:12px;color:#34d399;">
            <i class="bi bi-check-circle-fill"></i> <?= \App\Core\View::e($flash_success) ?>
        </div>
    <?php endif; ?>

    <div class="card" id="project-form-box" style="display:none;">
        <div class="card-header">
            <h3 class="card-title" id="project-form-title">Create Client Project</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('project-form-box').style.display='none'">Close</button>
        </div>
        <div class="card-body">
            <form method="POST" action="/projects/store" id="projectForm">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="id" id="projEditId" value="">
                <div class="grid-3" style="margin-bottom:18px;">
                    <div>
                        <label class="form-label">Project Title *</label>
                        <input type="text" name="title" id="projEditTitle" required placeholder="Project Title *" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Related Service</label>
                        <select name="service_id" id="projEditService" class="input-box">
                            <option value="">-- Custom / Select Service --</option>
                            <?php foreach ($services as $s): ?>
                                <option value="<?= (int)$s['id'] ?>"><?= \App\Core\View::e($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Priority</label>
                        <select name="priority" id="projEditPriority" class="input-box">
                            <option value="medium">Medium Priority</option>
                            <option value="high">High Priority</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" id="projEditStatus" class="input-box">
                            <option value="planning">Planning</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Budget (BDT)</label>
                        <input type="number" name="budget_bdt" id="projEditBudget" placeholder="Budget (BDT)" class="input-box">
                    </div>
                    <div>
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" id="projEditDue" class="input-box">
                    </div>
                </div>
                <div style="margin-bottom:18px;">
                    <label class="form-label">Project Scope & Milestones</label>
                    <textarea name="description" id="projEditDesc" rows="2" placeholder="Project scope and milestones..." class="input-box"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save Project</button>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($projects)): ?>
                            <tr><td colspan="7" style="text-align:center;padding:28px;" class="text-muted">No projects yet. Click "New Project" to create one.</td></tr>
                        <?php else: ?>
                            <?php foreach ($projects as $p): ?>
                                <tr>
                                    <td><strong><?= \App\Core\View::e($p['title']) ?></strong></td>
                                    <td><?= \App\Core\View::e($p['service_name'] ?? 'Custom') ?></td>
                                    <td><span class="badge badge-status-processing"><?= strtoupper($p['status']) ?></span></td>
                                    <td><span class="badge-action"><?= strtoupper($p['priority']) ?></span></td>
                                    <td><?= $p['budget_bdt'] ? '৳' . number_format((float)$p['budget_bdt']) : '-' ?></td>
                                    <td><?= \App\Core\View::e($p['due_date'] ?? 'TBD') ?></td>
                                    <td style="display:flex;gap:6px;">
                                        <button type="button" class="btn btn-sm btn-secondary" onclick='openProjectEdit(<?= json_encode($p) ?>)'><i class="bi bi-pencil-square"></i> Edit</button>
                                        <form method="POST" action="/projects/delete" style="display:inline;" onsubmit="return confirm('Delete this project?');">
                                            <?= \App\Core\Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger-soft"><i class="bi bi-trash"></i></button>
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
function openProjectCreate() {
    const box = document.getElementById('project-form-box');
    box.style.display = 'block';
    document.getElementById('projectForm').action = '/projects/store';
    document.getElementById('project-form-title').textContent = 'Create Client Project';
    document.getElementById('projEditId').value = '';
    document.getElementById('projEditTitle').value = '';
    document.getElementById('projEditBudget').value = '';
    document.getElementById('projEditDue').value = '';
    document.getElementById('projEditDesc').value = '';
    box.scrollIntoView({ behavior: 'smooth' });
}
function openProjectEdit(p) {
    const box = document.getElementById('project-form-box');
    box.style.display = 'block';
    document.getElementById('projectForm').action = '/projects/update';
    document.getElementById('project-form-title').textContent = 'Edit Project: ' + p.title;
    document.getElementById('projEditId').value = p.id;
    document.getElementById('projEditTitle').value = p.title || '';
    document.getElementById('projEditService').value = p.service_id || '';
    document.getElementById('projEditPriority').value = p.priority || 'medium';
    document.getElementById('projEditStatus').value = p.status || 'in_progress';
    document.getElementById('projEditBudget').value = p.budget_bdt || '';
    document.getElementById('projEditDue').value = p.due_date || '';
    document.getElementById('projEditDesc').value = p.description || '';
    box.scrollIntoView({ behavior: 'smooth' });
}
</script>
