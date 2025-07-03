<?php

namespace Thales\PhpBanking\resources;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $this->convertPattern($pattern),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($method === $route['method'] && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
    }

    private function convertPattern(string $pattern): string
    {
        $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $pattern);
        return '#^' . rtrim($pattern, '/') . '/?$#';
    }
}
