<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\TransactionService;
use Thales\PhpBanking\resources\Response;
use Exception;

class TransactionController
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function handleRequest(string $method, array $uri): void
    {
        $id = isset($uri[2]) ? (int)$uri[2] : null;

        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $transaction = $this->transactionService->getTransactionsByAccount($id);
                        Response::sendJson($transaction);
                    } else {
                        $transaction = $this->transactionService->getAllTransactions();
                        Response::sendJson($transaction);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    if (empty($data['accountId']) || empty($data['amount']) || empty($data['type'])) {
                        Response::sendError("Campos obrigatórios não informados (accountId/amount/type).", 400);
                    }

                    $transaction = $this->transactionService->createTransaction(
                        $data['accountId'],
                        $data['amount'],
                        $data['type']
                    );

                    if (!$transaction) {
                        Response::sendError('Erro ao criar a transação.', 500);
                    }

                    Response::sendJson([
                        'message' => 'Transação efetivada com sucesso.',
                        'transaction' => $transaction
                    ], 201);
                    break;

                default:
                    Response::sendError('Método não permitido', 405);
            }
        } catch (Exception $e) {
            Response::sendError('Erro interno no servidor', 500, $e->getMessage());
        }
    }
}
