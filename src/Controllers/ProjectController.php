<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;

class ProjectController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();
        $projects = $db->query("
            SELECT p.*, s.name as service_name, c.first_name, c.last_name, c.company as client_company, u.name as manager_name
            FROM projects p
            LEFT JOIN services s ON p.service_id = s.id
            LEFT JOIN contacts c ON p.client_contact_id = c.id
            LEFT JOIN users u ON p.manager_user_id = u.id
            ORDER BY p.created_at DESC
        ")->fetchAll();

        $tasks = $db->query("
            SELECT t.*, p.title as project_title, u.name as assigned_name
            FROM tasks t
            JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_user_id = u.id
            ORDER BY t.due_date ASC, t.id DESC
        ")->fetchAll();

        $services = $db->query("SELECT id, name FROM services WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        $contacts = $db->query("SELECT id, first_name, last_name, company, email FROM contacts ORDER BY first_name ASC LIMIT 100")->fetchAll();
        $users = $db->query("SELECT id, name FROM users WHERE is_active = 1")->fetchAll();

        View::render('projects/index', [
            'title' => 'Project & Task Operations — Dream Career IT',
            'projects' => $projects,
            'tasks' => $tasks,
            'services' => $services,
            'contacts' => $contacts,
            'users' => $users,
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function storeProject(Request $request): void
    {
        $db = Database::pdo();
        $title = trim((string)$request->input('title', ''));
        $serviceId = (int)$request->input('service_id', 0) ?: null;
        $clientId = (int)$request->input('client_contact_id', 0) ?: null;
        $status = trim((string)$request->input('status', 'planning'));
        $priority = trim((string)$request->input('priority', 'medium'));
        $budget = $request->input('budget_bdt') !== '' ? (float)$request->input('budget_bdt') : null;
        $dueDate = trim((string)$request->input('due_date', '')) ?: null;
        $desc = trim((string)$request->input('description', ''));

        if ($title === '') {
            Session::setFlash('error', 'Project title is required.');
            Response::redirect('/projects');
            return;
        }

        $stmt = $db->prepare("
            INSERT INTO projects (title, description, client_contact_id, service_id, manager_user_id, status, priority, budget_bdt, start_date, due_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)
        ");
        $stmt->execute([$title, $desc, $clientId, $serviceId, Session::get('user_id'), $status, $priority, $budget, $dueDate]);
        $pid = (int)$db->lastInsertId();

        Logger::audit('project_created', 'Project', $pid, null, ['title' => $title]);
        Session::setFlash('success', "Project '{$title}' created.");
        Response::redirect('/projects');
    }

    public function storeTask(Request $request): void
    {
        $db = Database::pdo();
        $projectId = (int)$request->input('project_id', 0);
        $title = trim((string)$request->input('title', ''));
        $assignedId = (int)$request->input('assigned_user_id', 0) ?: null;
        $priority = trim((string)$request->input('priority', 'medium'));
        $dueDate = trim((string)$request->input('due_date', '')) ?: null;

        if ($projectId > 0 && $title !== '') {
            $stmt = $db->prepare("
                INSERT INTO tasks (project_id, assigned_user_id, title, status, priority, due_date)
                VALUES (?, ?, ?, 'todo', ?, ?)
            ");
            $stmt->execute([$projectId, $assignedId, $title, $priority, $dueDate]);
            Logger::audit('task_created', 'Task', (int)$db->lastInsertId(), null, ['title' => $title]);
            Session::setFlash('success', 'Task added to project.');
        }
        Response::redirect('/projects');
    }
}
