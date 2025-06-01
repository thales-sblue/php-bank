<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\Utils\Response;
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
        $id = isset($uri[2]) ? (int)$uri[2] : null;

        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $account = $this->accountService->getAccount($id);
                        Response::sendJson($account);
                    } else {
                        $accounts = $this->accountService->getAllAccounts();
                        Response::sendJson($accounts);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    if (!isset($data['user_id'], $data['balance'], $data['type'])) {
                        Response::sendError('Dados obrigatórios ausentes (user_id, balance, type)', 400);
                    }

                    $account = $this->accountService->createAccount(
                        $data['user_id'],
                        $data['balance'],
                        $data['type']
                    );

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
