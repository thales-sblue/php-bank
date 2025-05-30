<?php
require_once __DIR__ . '/../Service/TransactionService.php';
require_once __DIR__ . '/../Utils/Response.php';

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

        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $transaction = $this->transactionService->getTransactionsByAccount($id);
                        sendJson($transaction);
                    } else {
                        $transaction = $this->transactionService->getAllTransactions();
                        sendJson($transaction);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    if (empty($data['accountId']) || empty($data['amount']) || empty($data['type'])) {
                        sendError("Campos obrigatórios não informados (accountId/amount/type).", 400);
                    }

                    $transaction = $this->transactionService->createTransaction(
                        $data['accountId'],
                        $data['amount'],
                        $data['type']
                    );

                    if (!$transaction) {
                        sendError('Erro ao criar a transação.', 500);
                    }

                    sendJson([
                        'message' => 'Transação efetivada com sucesso.',
                        'transaction' => $transaction
                    ], 201);
                    break;

                default:
                    sendError('Método não permitido', 405);
            }
        } catch (Exception $e) {
            sendError('Erro interno no servidor', 500, $e->getMessage());
        }
    }
}
