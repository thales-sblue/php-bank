<?php

require_once __DIR__ . '/../Service/AccountService.php';

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
                        echo json_encode($account, JSON_UNESCAPED_UNICODE);
                    } else {
                        $accounts = $this->accountService->getAllAccounts();
                        echo json_encode($accounts, JSON_UNESCAPED_UNICODE);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $this->accountService->createAccount(
                        $data['user_id'],
                        $data['balance'],
                        $data['type']
                    );
                    http_response_code(201);
                    echo json_encode(['message' => 'Conta criada com sucesso'], JSON_UNESCAPED_UNICODE);
                    break;

                case 'PUT':
                    if ($id) {
                        $data = json_decode(file_get_contents('php://input'), true);
                        $this->accountService->updateAccount(
                            $id,
                            $data['balance'] ?? null,
                            $data['type'] ?? null,
                            $data['active'] ?? null
                        );
                        echo json_encode(['message' => 'Conta atualizada com sucesso'], JSON_UNESCAPED_UNICODE);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID da conta é obrigatório para atualizar'], JSON_UNESCAPED_UNICODE);
                    }
                    break;


                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Método não permitido'], JSON_UNESCAPED_UNICODE);
                    break;
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
