<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Logger;
use App\Core\Session;
use App\Services\AiAssistantService;

class AiController
{
    public function index(Request $request): void
    {
        View::render('ai/index', [
            'title' => 'AI Business Assistant — Dream Career IT',
            'result' => Session::getFlash('ai_result'),
            'provider' => Session::getFlash('ai_provider'),
            'lastPrompt' => Session::getFlash('ai_prompt'),
            'user' => ['theme' => Session::get('theme_preference', 'dark')]
        ]);
    }

    public function generate(Request $request): void
    {
        $taskType = trim((string)$request->input('task_type', 'email_draft'));
        $prompt = trim((string)$request->input('prompt', ''));
        $language = trim((string)$request->input('language', 'English'));

        if ($prompt === '') {
            Session::setFlash('ai_result', 'Please enter a topic or instruction for the AI Assistant.');
            Response::redirect('/ai-assistant');
            return;
        }

        $res = AiAssistantService::generate($taskType, $prompt, $language);
        Logger::audit('ai_assistant_used', 'AI', null, null, ['task' => $taskType, 'language' => $language]);

        if ($request->isAjax()) {
            Response::json($res);
        }

        Session::setFlash('ai_result', $res['content']);
        Session::setFlash('ai_provider', $res['provider']);
        Session::setFlash('ai_prompt', $prompt);
        Response::redirect('/ai-assistant');
    }
}
