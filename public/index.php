<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Thales\PhpBanking\Model\Account\AccountRepository;
use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\Controller\AccountController;

use Thales\PhpBanking\Model\Client\ClientRepository;
use Thales\PhpBanking\Service\ClientService;
use Thales\PhpBanking\Controller\ClientController;

use Thales\PhpBanking\Model\Transaction\TransactionRepository;
use Thales\PhpBanking\Service\TransactionService;
use Thales\PhpBanking\Controller\TransactionController;

use Thales\PhpBanking\Model\Transfer\TransferRepository;
use Thales\PhpBanking\Service\TransferService;
use Thales\PhpBanking\Controller\TransferController;


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

set_error_handler(function ($errstr, $errfile, $errline) {
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
        $repository = new ClientRepository();
        $service = new ClientService($repository);
        $controller = new ClientController($service);
        $controller->handleRequest($_SERVER['REQUEST_METHOD'], $uri);
        break;

    case 'accounts':
        $repository = new AccountRepository();
        $service = new AccountService($repository);

        $controller = new AccountController($service);
        $controller->handleRequest($_SERVER['REQUEST_METHOD'], $uri);
        break;

    case 'transactions':
        $accountRepository = new AccountRepository();
        $accountService = new AccountService($accountRepository);

        $transactionRepository = new TransactionRepository();
        $transactionService = new TransactionService($transactionRepository, $accountService);

        $controller = new TransactionController($transactionService);
        $controller->handleRequest($_SERVER['REQUEST_METHOD'], $uri);
        break;

    case 'transfers':
        $transferRepository = new TransferRepository();
        $transactionRepository = new TransactionRepository();
        $accountRepository = new AccountRepository();

        $accountService = new AccountService($accountRepository);
        $transactionService = new TransactionService($transactionRepository, $accountService);
        $transferService = new TransferService($transferRepository, $transactionService, $accountService);

        $controller = new TransferController($transferService);
        $controller->handleRequest($_SERVER['REQUEST_METHOD'], $uri);
        break;

    default:
        $repository = new ClientRepository();
        $service = new ClientService($repository);
        $controller = new ClientController($service);
        $uri = [];
        $uri[0] = 'default';
        $uri[1] = 'clients';
        $uri[2] = 'login';
        $controller->handleRequest($_SERVER['REQUEST_METHOD'], $uri);
        break;
}
