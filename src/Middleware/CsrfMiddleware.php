<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;

class CsrfMiddleware
{
    public function handle(Request $request): bool
    {
        $method = $request->getMethod();
        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            $token = $request->input('_csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Csrf::validate($token)) {
                if ($request->isAjax()) {
                    Response::json(['success' => false, 'error' => 'CSRF token mismatch or expired. Please refresh the page.'], 403);
                }
                Response::setStatus(403);
                die('CSRF token validation failed. Please reload the page and try again.');
            }
        }
        return true;
    }
}
