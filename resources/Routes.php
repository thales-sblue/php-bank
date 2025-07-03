<?php

use Thales\PhpBanking\Controller\ClientController;
use Thales\PhpBanking\Controller\AccountController;
use Thales\PhpBanking\Controller\TransactionController;
use Thales\PhpBanking\Controller\TransferController;
use Thales\PhpBanking\Service\{ClientService, AccountService, TransactionService, TransferService};
use Thales\PhpBanking\Model\Client\ClientRepository;
use Thales\PhpBanking\Model\Account\AccountRepository;
use Thales\PhpBanking\Model\Transaction\TransactionRepository;
use Thales\PhpBanking\Model\Transfer\TransferRepository;

$clientService = new ClientService(new ClientRepository());
$accountService = new AccountService(new AccountRepository());
$transactionService = new TransactionService(new TransactionRepository(), $accountService);
$transferService = new TransferService(new TransferRepository(), $transactionService, $accountService);

$clientController = new ClientController($clientService);
$accountController = new AccountController($accountService);
$transactionController = new TransactionController($transactionService);
$transferController = new TransferController($transferService);

return [
    ['GET',  '/clients/login',       [$clientController, 'loginForm']],
    ['POST', '/clients/login',       [$clientController, 'login']],
    ['GET',  '/clients/create',      [$clientController, 'createForm']],
    ['GET',  '/clients/home',        [$clientController, 'homeData']],
    ['GET',  '/clients/auth',        [$clientController, 'homePage']],
    ['GET',  '/clients',             [$clientController, 'index']],
    ['GET',  '/clients/{id}',        [$clientController, 'show']],
    ['POST', '/clients',             [$clientController, 'create']],
    ['PUT',  '/clients/{id}',        [$clientController, 'update']],
    ['GET',  '/logout',              function () {
        \Thales\PhpBanking\resources\Session::destroy();
        header('Location: /clients/login');
        exit;
    }],

    ['GET',  '/accounts/create', [$accountController, 'createForm']],
    ['GET',  '/accounts',        [$accountController, 'index']],
    ['GET',  '/accounts/{id}',   [$accountController, 'show']],
    ['POST', '/accounts',        [$accountController, 'create']],
    ['PUT',  '/accounts/{id}',   [$accountController, 'update']],

    ['GET',  '/transactions',    [$transactionController, 'createForm']],
    ['GET',  '/transactions/extracts',  [$transactionController, 'extractForm']],
    ['GET',  '/transactions/{accountId}', [$transactionController, 'showByAccount']],
    ['POST', '/transactions/create',           [$transactionController, 'create']],

    ['GET',  '/transfers', [$transferController, 'createForm']],
    ['POST', '/transfers/create',        [$transferController, 'create']],
];
