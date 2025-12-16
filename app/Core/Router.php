<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        // Charger toutes les routes depuis routes/load.php
        // Le fichier doit retourner un tableau de routes, chaque route = [ 'GET', '/path', 'Controller@method' ] ou [ 'POST', '/path', function(...) {...} ]
        $this->routes = require __DIR__ . '/../routes/load.php';
    }

    /**
     * Run the router: match current request to a route and dispatch.
     */
    public function run(): void
    {
        // Start session if not started yet (safe here)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $requestUri = rtrim($requestUri, '/') ?: '/';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach ($this->routes as $route) {
            // Normalize route array to exactly 3 items: method, path, action
            $route = array_values(array_pad($route, 3, null));
            [$routeMethod, $routePath, $action] = $route;

            // Skip if method doesn't match
            if (strtoupper($routeMethod) !== strtoupper($requestMethod)) {
                continue;
            }

            // Try exact match first
            if ($this->matchExact($routePath, $requestUri)) {
                $this->dispatch($action, []);
                return;
            }

            // Try parameterized match (e.g. /users/{id})
            $params = $this->matchParams($routePath, $requestUri);
            if ($params !== false) {
                $this->dispatch($action, $params);
                return;
            }
        }

        // No route matched -> 404
        http_response_code(404);
        echo "404 - Not Found";
    }

    /**
     * Check exact match (both considered with trailing slash normalization).
     */
    private function matchExact(string $routePath, string $requestUri): bool
    {
        $routeNormalized = rtrim($routePath, '/') ?: '/';
        return $routeNormalized === $requestUri;
    }

    /**
     * Match a route with parameters.
     * Example: route '/users/{id}/posts/{postId}' against '/users/5/posts/10'
     * Returns associative array of params on success, or false if it doesn't match.
     */
    private function matchParams(string $routePath, string $requestUri)
    {
        // If route has no braces, not parameterized
        if (strpos($routePath, '{') === false) {
            return false;
        }

        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts   = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];
        foreach ($routeParts as $i => $part) {
            if (preg_match('/^\{([a-zA-Z0-9_]+)\}$/', $part, $m)) {
                $paramName = $m[1];
                $params[$paramName] = urldecode($uriParts[$i]);
                continue;
            }

            // literal part must match exactly
            if ($part !== $uriParts[$i]) {
                return false;
            }
        }

        return $params;
    }

    /**
     * Dispatch an action: either a closure or "Controller@method"
     */
    private function dispatch($action, array $params): void
    {
        if (is_callable($action)) {
            // call the closure/controller function now with params
            call_user_func_array($action, $params);
            return;
        }

        if (is_string($action) && strpos($action, '@') !== false) {
            [$controllerName, $method] = explode('@', $action, 2);

            // Assume controllers live in App\Controllers and follow PSR-4 autoloading
            $controllerClass = 'App\\Controllers\\' . $controllerName;

            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "Controller not found: $controllerClass";
                return;
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                http_response_code(500);
                echo "Method not found: {$controllerClass}::{$method}";
                return;
            }

            call_user_func_array([$controller, $method], $params);
            return;
        }

        // Unknown action type
        http_response_code(500);
        echo "Invalid route action.";
    }
}
