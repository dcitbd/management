<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;

class AuthController
{
    public function showLogin(Request $request): void
    {
        View::render('auth/login', [
            'title' => 'Sign In — Dream Career IT AI Business Suite',
            'error' => Session::getFlash('error'),
            'success' => Session::getFlash('success')
        ], 'auth');
    }

    public function login(Request $request): void
    {
        $email = (string)$request->input('email', '');
        $password = (string)$request->input('password', '');
        $ip = $request->getIp();

        if (empty($email) || empty($password)) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'message' => 'Please provide both email and password.'], 422);
            }
            Session::setFlash('error', 'Please provide both email and password.');
            Response::redirect('/login');
            return;
        }

        $result = AuthService::attempt($email, $password, $ip);

        if (!$result['success']) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'message' => $result['message']], 401);
            }
            Session::setFlash('error', $result['message']);
            Response::redirect('/login');
            return;
        }

        if ($request->isAjax()) {
            Response::json(['success' => true, 'redirect' => '/dashboard']);
        }
        Response::redirect('/dashboard');
    }

    public function logout(Request $request): void
    {
        AuthService::logout();
        Response::redirect('/login');
    }

    public function showForgotPassword(Request $request): void
    {
        View::render('auth/forgot-password', [
            'title' => 'Forgot Password — Dream Career IT',
            'error' => Session::getFlash('error'),
            'success' => Session::getFlash('success')
        ], 'auth');
    }

    public function processForgotPassword(Request $request): void
    {
        $email = (string)$request->input('email', '');
        // For security, always show confirmation message without leaking user existence
        Session::setFlash('success', 'If a matching account was found, a password reset link has been dispatched.');
        Response::redirect('/forgot-password');
    }
}
