<?php
namespace App\Config;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, $handler, array $middlewares): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(string $requestMethod, string $requestUri): void
    {
        $uri = strtok($requestUri, '?');
        $uri = str_replace('/Talent_Hub/public', '', $uri);
        
        if (empty($uri) || $uri === '/') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchRoute($route['path'], $uri)) {
                $this->executeRoute($route);
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1><p>URI: ' . htmlspecialchars($uri) . '</p>';
    }

    private function matchRoute(string $routePath, string $uri): bool
    {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri) === 1;
    }

    private function executeRoute(array $route): void
    {
        $handler = $route['handler'];
        $middlewares = $route['middlewares'];

        $next = function() use ($handler) {
            if (is_callable($handler)) {
                return $handler();
            } elseif (is_array($handler)) {
                [$controller, $method] = $handler;
                return $controller->$method();
            }
        };

        foreach (array_reverse($middlewares) as $middleware) {
            $next = function() use ($middleware, $next) {
                return $middleware->handle(null, $next);
            };
        }

        $next();
    }

    public function group(array $attributes, callable $callback): void
    {
        $prefix = $attributes['prefix'] ?? '';
        $middlewares = $attributes['middlewares'] ?? [];

        $beforeCount = count($this->routes);

        $callback($this);

        for ($i = $beforeCount; $i < count($this->routes); $i++) {
            $this->routes[$i]['path'] = $prefix . $this->routes[$i]['path'];
            $this->routes[$i]['middlewares'] = array_merge(
                $middlewares,
                $this->routes[$i]['middlewares']
            );
        }
    }
}