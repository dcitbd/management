<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $template, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        $viewsDir = __DIR__ . '/../../views/';
        
        $templatePath = $viewsDir . $template . '.php';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("View template not found: {$templatePath}");
        }

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        if ($layout) {
            $layoutPath = $viewsDir . 'layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                require $layoutPath;
                return;
            }
        }

        echo $content;
    }

    public static function e(mixed $string): string
    {
        return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
