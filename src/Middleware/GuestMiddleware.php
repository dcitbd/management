<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class GuestMiddleware
{
    public function handle(Request $request): bool
    {
        Session::start();
        if (Session::has('user_id')) {
            Response::redirect('/dashboard');
            return false;
        }
        return true;
    }
}
