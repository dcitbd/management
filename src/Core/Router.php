<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middleware);
    }

    public function put(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $pattern, $handler, $middleware);
    }

    public function delete(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $pattern, $handler, $middleware);
    }

    private function addRoute(string $method, string $pattern, array|callable $handler, array $middleware): void
    {
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Execute middleware
                foreach ($route['middleware'] as $mwClass) {
                    $mw = new $mwClass();
                    if (!$mw->handle($request)) {
                        return;
                    }
                }

                // Execute handler
                if (is_callable($route['handler'])) {
                    call_user_func($route['handler'], $request, $params);
                    return;
                }

                if (is_array($route['handler'])) {
                    [$controllerClass, $actionMethod] = $route['handler'];
                    $controller = new $controllerClass();
                    $controller->$actionMethod($request, $params);
                    return;
                }
            }
        }

        // 404 Not Found
        Response::setStatus(404);
        View::render('errors/404', ['title' => 'Page Not Found']);
    }
}
