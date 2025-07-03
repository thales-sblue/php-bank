<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\resources\Response;
use Thales\PhpBanking\resources\Session;
use Thales\PhpBanking\resources\View;

class AccountController
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index(): void
    {
        $accounts = $this->accountService->getAllAccounts();
        Response::sendJson($accounts);
    }

    public function show(int $clientId): void
    {
        $data = $this->accountService->getAccount($clientId, null);
        Response::sendJson($data);
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $client = Session::get('client');
        $clientId = $data['client_id'] ?? ($client['id'] ?? null);

        if (!isset($data['type']) || empty($clientId)) {
            Response::sendError('Dados obrigatórios ausentes!', 400);
        }

        $type = $data['type'];
        $balance = $data['balance'];

        $account = $this->accountService->createAccount($clientId, $type, $balance);

        if (!$account) {
            Response::sendError('Erro ao criar a conta!', 500);
        }

        Response::sendJson([
            'message' => 'Conta cadastrada com sucesso!',
            'account' => $account
        ], 201);
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $accountUpdated = $this->accountService->updateAccount(
            $id,
            $data['balance'] ?? null,
            $data['type'] ?? null,
            $data['active'] ?? null
        );

        Response::sendJson([
            'message' => 'Conta atualizada com sucesso!',
            'account' => $accountUpdated
        ], 200);
    }

    public function createForm(): void
    {
        Session::requireLogin();
        View::render('Account.create');
    }
}
