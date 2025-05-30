<?php

require_once __DIR__ . '/../Service/AccountService.php';
require_once __DIR__ . '/../Utils/Response.php';

class AccountController
{
    private $accountService;

    public function __construct()
    {
        $this->accountService = new AccountService();
    }

    public function handleRequest($method, $uri)
    {
        $id = isset($uri[2]) ? (int)$uri[2] : null;

        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $account = $this->accountService->getAccount($id);
                        sendJson($account);
                    } else {
                        $accounts = $this->accountService->getAllAccounts();
                        sendJson($accounts);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    if (!isset($data['user_id'], $data['balance'], $data['type'])) {
                        sendError('Dados obrigatórios ausentes (user_id, balance, type)', 400);
                    }

                    $account = $this->accountService->createAccount(
                        $data['user_id'],
                        $data['balance'],
                        $data['type']
                    );

                    if (!$account) {
                        sendError('Erro ao criar a conta.', 500);
                    }

                    sendJson([
                        'message' => 'Conta criada com sucesso',
                        'account' => $account
                    ], 201);
                    break;

                case 'PUT':
                    if (!$id) {
                        sendError('ID da conta é obrigatório para atualizar', 400);
                    }

                    $data = json_decode(file_get_contents('php://input'), true);

                    $accountUpdated = $this->accountService->updateAccount(
                        $id,
                        $data['balance'] ?? null,
                        $data['type'] ?? null,
                        $data['active'] ?? null
                    );

                    sendJson([
                        'message' => 'Conta atualizada com sucesso',
                        'account' => $accountUpdated
                    ], 200);
                    break;

                default:
                    sendError('Método não permitido', 405);
            }
        } catch (Exception $e) {
            sendError('Erro interno ao processar a requisição', 500, $e->getMessage());
        }
    }
}
