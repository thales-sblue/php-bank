<?php
require_once __DIR__ . '/../Service/TransactionService.php';

class TransactionController
{
    private $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    public function handlerequest($method, $uri)
    {
        $id = isset($uri[2]) ? (int)$uri[2] : null;

        switch ($method) {
            case 'GET':
                try {
                    if ($id) {
                        $transaction = $this->transactionService->getTransactionsByAccount($id);
                        echo json_encode($transaction, JSON_UNESCAPED_UNICODE);
                    } else {
                        $transaction = $this->transactionService->getAllTransactions();
                        echo json_encode($transaction, JSON_UNESCAPED_UNICODE);
                    }
                } catch (Exception $e) {
                    http_response_code(404);
                    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'POST':
                try {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if (empty($data['accountId']) || empty($data['amount']) || empty($data['type'])) {
                        throw new Exception("Campos obrigatórios não informados (accountId/amount/type).");
                    }
                    $transaction = $this->transactionService->createTransaction($data['accountId'], $data['amount'], $data['type']);
                    http_response_code(201);
                    echo json_encode(['message' => 'Transação efetivada com sucesso.'], JSON_UNESCAPED_UNICODE);
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                }
        }
    }
}
