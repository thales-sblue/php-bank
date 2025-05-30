<?php
require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Controller/ClientController.php';
require_once __DIR__ . '/../src/Controller/AccountController.php';
require_once __DIR__ . '/../src/Controller/TransactionController.php';

header('Content-Type: application/json; charset=utf-8');

set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro inesperado no servidor',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno no servidor',
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Erro fatal no servidor',
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ], JSON_UNESCAPED_UNICODE);
    }
});

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$route = $uri[1] ?? '';

switch ($route) {
    case 'clients':
        $controller = new ClientController();
        $controller->handleRequest($_SERVER['REQUEST_METHOD'], $uri);
        break;

    case 'accounts':
        $controller = new AccountController();
        $controller->handleRequest($_SERVER['REQUEST_METHOD'], $uri);
        break;

    case 'transactions':
        $controller = new TransactionController();
        $controller->handlerequest($_SERVER['REQUEST_METHOD'], $uri);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}
