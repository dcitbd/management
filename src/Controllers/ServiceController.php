<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Session;

class ServiceController
{
    public function index(Request $request): void
    {
        $db = Database::pdo();
        $categoryFilter = $request->input('category_id');
        $search = trim((string)$request->input('q', ''));
        $status = $request->input('status');

        $categories = $db->query("
            SELECT c.*, (SELECT COUNT(*) FROM services s WHERE s.category_id = c.id) AS service_count
            FROM service_categories c
            ORDER BY c.sort_order ASC, c.id ASC
        ")->fetchAll();

        $sql = "
            SELECT s.*, c.name as category_name
            FROM services s
            JOIN service_categories c ON s.category_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($categoryFilter)) {
            $sql .= " AND s.category_id = ?";
            $params[] = (int)$categoryFilter;
        }
        if ($search !== '') {
            $sql .= " AND (s.name LIKE ? OR s.short_description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($status !== null && $status !== '') {
            $sql .= " AND s.is_active = ?";
            $params[] = (int)$status;
        }

        $sql .= " ORDER BY c.sort_order ASC, s.name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $services = $stmt->fetchAll();

        View::render('services/index', [
            'title' => 'Services Catalog & Category Management — Dream Career IT',
            'categories' => $categories,
            'services' => $services,
            'filters' => [
                'category_id' => $categoryFilter,
                'q' => $search,
                'status' => $status
            ],
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function store(Request $request): void
    {
        $db = Database::pdo();
        $categoryId = (int)$request->input('category_id');
        $name = trim((string)$request->input('name', ''));
        $shortDesc = trim((string)$request->input('short_description', ''));
        $fullDesc = trim((string)$request->input('full_description', ''));
        $priceBdt = $request->input('price_bdt') !== '' && $request->input('price_bdt') !== null ? (float)$request->input('price_bdt') : null;
        $priceModel = trim((string)$request->input('price_model', 'Custom Quote'));
        $iconClass = trim((string)$request->input('icon_class', 'bi-layers-fill'));
        $isActive = (int)$request->input('is_active', 1);

        if ($name === '' || $categoryId <= 0) {
            Session::setFlash('error', 'Service name and valid category are required.');
            Response::redirect('/services');
            return;
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . substr(uniqid(), -4);

        $stmt = $db->prepare("
            INSERT INTO services (category_id, name, slug, short_description, full_description, icon_class, price_bdt, price_model, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$categoryId, $name, $slug, $shortDesc, $fullDesc, $iconClass, $priceBdt, $priceModel, $isActive]);
        $id = (int)$db->lastInsertId();

        Logger::audit('service_created', 'Service', $id, null, ['name' => $name, 'category_id' => $categoryId]);
        Session::setFlash('success', "Service '{$name}' added to catalog.");
        Response::redirect('/services');
    }

    public function update(Request $request): void
    {
        $db = Database::pdo();
        $id = (int)$request->input('id');
        $categoryId = (int)$request->input('category_id');
        $name = trim((string)$request->input('name', ''));
        $shortDesc = trim((string)$request->input('short_description', ''));
        $fullDesc = trim((string)$request->input('full_description', ''));
        $priceBdt = $request->input('price_bdt') !== '' && $request->input('price_bdt') !== null ? (float)$request->input('price_bdt') : null;
        $priceModel = trim((string)$request->input('price_model', 'Custom Quote'));
        $iconClass = trim((string)$request->input('icon_class', 'bi-layers-fill'));
        $isActive = (int)$request->input('is_active', 1);

        if ($id <= 0 || $name === '' || $categoryId <= 0) {
            Session::setFlash('error', 'Invalid service update data.');
            Response::redirect('/services');
            return;
        }

        $stmt = $db->prepare("
            UPDATE services
            SET category_id = ?, name = ?, short_description = ?, full_description = ?, icon_class = ?, price_bdt = ?, price_model = ?, is_active = ?
            WHERE id = ?
        ");
        $stmt->execute([$categoryId, $name, $shortDesc, $fullDesc, $iconClass, $priceBdt, $priceModel, $isActive, $id]);

        Logger::audit('service_updated', 'Service', $id, null, ['name' => $name, 'is_active' => $isActive]);
        Session::setFlash('success', "Service '{$name}' updated successfully.");
        Response::redirect('/services');
    }

    public function delete(Request $request): void
    {
        $id = (int)$request->input('id');
        if ($id > 0) {
            $stmt = Database::pdo()->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
            Logger::audit('service_deleted', 'Service', $id);
            Session::setFlash('success', 'Service removed from catalog.');
        }
        Response::redirect('/services');
    }

    public function storeCategory(Request $request): void
    {
        $db = Database::pdo();
        $name = trim((string)$request->input('name', ''));
        $description = trim((string)$request->input('description', ''));
        $iconClass = trim((string)$request->input('icon_class', 'bi-grid-1x2-fill'));

        if ($name === '') {
            Session::setFlash('error', 'Category name is required.');
            Response::redirect('/services');
            return;
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . substr(uniqid(), -3);
        $maxOrder = (int)$db->query("SELECT COALESCE(MAX(sort_order), 0) FROM service_categories")->fetchColumn();

        $stmt = $db->prepare("
            INSERT INTO service_categories (name, slug, description, icon_class, sort_order, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([$name, $slug, $description, $iconClass, $maxOrder + 1]);
        $catId = (int)$db->lastInsertId();

        Logger::audit('category_created', 'ServiceCategory', $catId, null, ['name' => $name]);
        Session::setFlash('success', "Catalog Category '{$name}' created.");
        Response::redirect('/services');
    }

    public function updateCategory(Request $request): void
    {
        $db = Database::pdo();
        $id = (int)$request->input('id');
        $name = trim((string)$request->input('name', ''));
        $description = trim((string)$request->input('description', ''));

        if ($id <= 0 || $name === '') {
            Session::setFlash('error', 'Valid category ID and name are required.');
            Response::redirect('/services');
            return;
        }

        $stmt = $db->prepare("UPDATE service_categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);

        Logger::audit('category_updated', 'ServiceCategory', $id, null, ['name' => $name]);
        Session::setFlash('success', "Category '{$name}' updated.");
        Response::redirect('/services');
    }

    public function deleteCategory(Request $request): void
    {
        $db = Database::pdo();
        $id = (int)$request->input('id');
        if ($id > 0) {
            $db->prepare("DELETE FROM service_categories WHERE id = ?")->execute([$id]);
            Logger::audit('category_deleted', 'ServiceCategory', $id);
            Session::setFlash('success', 'Catalog category deleted.');
        }
        Response::redirect('/services');
    }
}
