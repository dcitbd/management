<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles = ['admin'])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(Request $request): bool
    {
        Session::start();
        $userRole = Session::get('user_role_slug');

        if (!in_array($userRole, $this->allowedRoles, true)) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'error' => 'Forbidden: Insufficient privileges.'], 403);
            }
            Response::setStatus(403);
            View::render('errors/403', ['title' => 'Access Denied']);
            return false;
        }
        return true;
    }
}
