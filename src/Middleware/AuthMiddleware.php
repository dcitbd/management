<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        Session::start();
        if (!Session::has('user_id')) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'error' => 'Unauthenticated'], 401);
            }
            Response::redirect('/login');
            return false;
        }
        return true;
    }
}
