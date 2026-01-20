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
            if ($route['method'] === $requestMethod) {
                $params = $this->matchRoute($route['path'], $uri);
                if ($params !== false) {
                    $this->executeRoute($route, $params);
                    return;
                }
            }
        }

        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1><p>URI: ' . htmlspecialchars($uri) . '</p>';
    }

    private function matchRoute(string $routePath, string $uri)
    {
        // Extract parameter names from route path
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routePath, $paramNames);
        
        // Create pattern for matching
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        // Match the URI against the pattern
        if (preg_match($pattern, $uri, $matches) === 1) {
            // Remove the full match
            array_shift($matches);
            
            // Create associative array of parameters
            $params = [];
            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index] ?? null;
            }
            
            return $params;
        }
        
        return false;
    }

    private function executeRoute(array $route, array $params = []): void
    {
        $handler = $route['handler'];
        $middlewares = $route['middlewares'];

        $next = function() use ($handler, $params) {
            if (is_callable($handler)) {
                // Pass parameters to the handler
                return call_user_func_array($handler, $params);
            } elseif (is_array($handler)) {
                [$controller, $method] = $handler;
                return call_user_func_array([$controller, $method], $params);
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