<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\resources\Response;
use Thales\PhpBanking\resources\Session;
use Exception;

class AccountController
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function handleRequest($method, $uri): void
    {
        $param = $uri[2] ?? null;
        $clientId = (is_numeric($param)) ? (int)$param : null;
        $action = (!is_numeric($param)) ? $param : null;

        try {
            switch ($method) {
                case 'GET':
                    if ($clientId) {
                        $data = $this->accountService->getAccount($clientId, null);
                        Response::sendJson($data);
                    } elseif ($action == 'create') {
                        Session::requireLogin();
                        header('Content-Type: text/html; charset=utf-8');
                        require __DIR__ . "/../View/Account/create.phtml";
                    } else {
                        $accounts = $this->accountService->getAllAccounts();
                        Response::sendJson($accounts);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    $client = Session::get('client');
                    $clientId = $data['client_id'] ?? ($client['id'] ?? null);

                    if (!isset($data['type']) || empty($clientId)) {
                        Response::sendError('Dados obrigatórios ausentes!', 400);
                    }

                    $type   = $data['type'];
                    $balance = $data['balance'];

                    $account = $this->accountService->createAccount($clientId, $type, $balance);

                    if (!$account) {
                        Response::sendError('Erro ao criar a conta!', 500);
                    }

                    Response::sendJson([
                        'message' => 'Conta cadastrada com sucesso!',
                        'account' => $account
                    ], 201);

                    break;

                case 'PUT':
                    if (!$id) {
                        Response::sendError('Campo obrigatório não informado!', 400);
                    }

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
                    break;

                default:
                    Response::sendError('Método não permitido!', 405);
            }
        } catch (Exception $e) {
            Response::sendError('Erro interno ao processar a requisição!', 500, $e->getMessage());
        }
    }
}
