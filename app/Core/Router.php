<?php

namespace App\Core;

/**
 * Минималистичный роутер: шаблоны вида /catalog/{slug} превращаются в regexp,
 * параметры передаются в метод контроллера по порядку.
 */
class Router
{
    private array $routes = [];
    private array $groupMiddleware = [];
    private string $prefix = '';

    public function get(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function any(array $methods, string $pattern, array|callable $handler, array $middleware = []): void
    {
        foreach ($methods as $method) {
            $this->add($method, $pattern, $handler, $middleware);
        }
    }

    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $previousPrefix     = $this->prefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->prefix          = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->prefix          = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function add(string $method, string $pattern, array|callable $handler, array $middleware): void
    {
        $pattern = $this->prefix . $pattern;
        $pattern = '/' . trim($pattern, '/');
        if ($pattern === '//') {
            $pattern = '/';
        }

        $regex = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#',
            static fn (array $m) => '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')',
            $pattern
        );

        $this->routes[] = [
            'method'     => $method,
            'regex'      => '#^' . $regex . '$#u',
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(Request $request): void
    {
        $path          = $request->path();
        $method        = $request->method();
        $pathMatched   = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }
            $pathMatched = true;
            if ($route['method'] !== $method) {
                continue;
            }

            foreach ($route['middleware'] as $middleware) {
                $result = (new $middleware())->handle($request);
                if ($result === false) {
                    return; // middleware сам сделал redirect / вывел ответ
                }
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[] = $value;
                }
            }

            $handler = $route['handler'];
            if (is_array($handler)) {
                [$class, $action] = $handler;
                $controller = new $class($request);
                $controller->$action(...$params);
            } else {
                $handler($request, ...$params);
            }
            return;
        }

        Response::status($pathMatched ? 405 : 404);
        (new \App\Controllers\ErrorController($request))->notFound();
    }
}
