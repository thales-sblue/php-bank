<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\Controller\Utils\Response;
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
        $id = (is_numeric($param)) ? (int)$param : null;
        $action = (!is_numeric($param)) ? $param : null;

        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $account = $this->accountService->getAccount($id);
                        Response::sendJson($account);
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
                        Response::sendError('Dados obrigatórios ausentes (user_id, type)', 400);
                    }

                    $type   = $data['type'];
                    $balance = $data['balance'];

                    $account = $this->accountService->createAccount($clientId, $type, $balance);

                    if (!$account) {
                        Response::sendError('Erro ao criar a conta.', 500);
                    }

                    Response::sendJson([
                        'message' => 'Conta criada com sucesso',
                        'account' => $account
                    ], 201);

                    break;

                case 'PUT':
                    if (!$id) {
                        Response::sendError('ID da conta é obrigatório para atualizar', 400);
                    }

                    $data = json_decode(file_get_contents('php://input'), true);

                    $accountUpdated = $this->accountService->updateAccount(
                        $id,
                        $data['balance'] ?? null,
                        $data['type'] ?? null,
                        $data['active'] ?? null
                    );

                    Response::sendJson([
                        'message' => 'Conta atualizada com sucesso',
                        'account' => $accountUpdated
                    ], 200);
                    break;

                default:
                    Response::sendError('Método não permitido', 405);
            }
        } catch (Exception $e) {
            Response::sendError('Erro interno ao processar a requisição', 500, $e->getMessage());
        }
    }
}
