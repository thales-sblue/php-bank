<?php

namespace Thales\PhpBanking\resources;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, $handler): void
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
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if (
                $method === $route['method'] &&
                preg_match($route['pattern'], $uri, $matches)
            ) {
                array_shift($matches);
                $args = array_values(array_filter($matches, 'is_int', ARRAY_FILTER_USE_KEY));
                call_user_func_array($route['handler'], $args);
                return;
            }
        }

        header('Location: /clients/login');
        exit;
    }

    private function convertPattern(string $pattern): string
    {
        $pattern = preg_replace_callback('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', function ($matches) {
            return '(?<' . $matches[1] . '>[^/]+)';
        }, $pattern);

        return '#^' . rtrim($pattern, '/') . '/?$#';
    }
}
