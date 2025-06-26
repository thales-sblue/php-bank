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
        $param = $uri[2] ?? null;
        $id = (is_numeric($param)) ? (int)$param : null;
        $action = (!is_numeric($param)) ? $param : null;

        try {
            switch ($method) {
                case 'GET':
                    if ($action === 'extracts') {
                        header('Content-Type: text/html; charset=utf-8');
                        require __DIR__ . "/../View/Transaction/extract.phtml";
                    } elseif ($id > 0) {
                        $accountTransactions = $this->transactionService->getTransactionsByAccount($id);
                        Response::sendJson($accountTransactions, 200);
                    } else {
                        header('Content-Type: text/html; charset=utf-8');
                        require __DIR__ . "/../View/Transaction/create.phtml";
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    if (empty($data['accountId']) || empty($data['amount']) || empty($data['type'])) {
                        Response::sendError("Campos obrigatórios não informados!", 400);
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
                        'message' => 'Transação efetivada com sucesso!.',
                        'transaction' => $transaction
                    ], 201);
                    break;

                default:
                    Response::sendError('Método não permitido!', 405);
            }
        } catch (Exception $e) {
            Response::sendError('Erro interno no servidor!', 500, $e->getMessage());
        }
    }
}
