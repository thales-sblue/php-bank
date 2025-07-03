<?php

namespace Thales\PhpBanking\resources;

class View
{
    public static function render(string $template, array $data = []): void
    {
        $viewPath = __DIR__ . "/../src/View/" . str_replace('.', '/', $template) . ".phtml";

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "Erro: view '$template' não encontrada.";
            exit;
        }

        extract($data);
        header('Content-Type: text/html; charset=utf-8');
        require $viewPath;
    }
}
