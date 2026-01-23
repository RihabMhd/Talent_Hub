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
        // n9adew uri w n7aydou query parameters ila kanin
        $uri = strtok($requestUri, '?');
        // n cleanew base path dial project
        $uri = str_replace('/Talent_Hub/public', '', $uri);
        
        // ila kan uri khawi nrja3ouh l home page
        if (empty($uri) || $uri === '/') {
            $uri = '/';
        }

        // ndouro 3la les routes kamlin bach nlqaw match
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $params = $this->matchRoute($route['path'], $uri);
                if ($params !== false) {
                    // l9ina route li t match, ghadi n executiwh
                    $this->executeRoute($route, $params);
                    return;
                }
            }
        }

        // ila ma l9inach route, n affichew 404
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1><p>URI: ' . htmlspecialchars($uri) . '</p>';
    }

    private function matchRoute(string $routePath, string $uri)
    {
        // n extractew parameter names mn route bach nst3mlohom f regex
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routePath, $paramNames);
        
        // n buildew pattern li ghan matchew bih uri
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        // n checkew ila uri kat match m3a pattern
        if (preg_match($pattern, $uri, $matches) === 1) {
            // n7aydou full match w nbqaw ghir b parameters
            array_shift($matches);
            
            // n createw array associative dial parameters
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

        // n7adro handler li ghadi yexecutew
        $next = function() use ($handler, $params) {
            if (is_callable($handler)) {
                // ila kan handler callable, n calliwh directly
                return call_user_func_array($handler, $params);
            } elseif (is_array($handler)) {
                // ila kan array [controller, method]
                [$controller, $method] = $handler;
                return call_user_func_array([$controller, $method], $params);
            }
        };

        // n executew middlewares f reverse order (stack style)
        foreach (array_reverse($middlewares) as $middleware) {
            $next = function() use ($middleware, $next) {
                return $middleware->handle(null, $next);
            };
        }

        $next();
    }

    public function group(array $attributes, callable $callback): void
    {
        // nst3mlo group bach n groupew routes li 3ndhom prefix w middlewares communs
        $prefix = $attributes['prefix'] ?? '';
        $middlewares = $attributes['middlewares'] ?? [];

        $beforeCount = count($this->routes);

        // n calliw callback bach yajouti routes jdad
        $callback($this);

        // n appliquiw prefix w middlewares 3la routes jdad li tzadou
        for ($i = $beforeCount; $i < count($this->routes); $i++) {
            $this->routes[$i]['path'] = $prefix . $this->routes[$i]['path'];
            $this->routes[$i]['middlewares'] = array_merge(
                $middlewares,
                $this->routes[$i]['middlewares']
            );
        }
    }
}