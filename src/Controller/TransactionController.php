<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\TransactionService;
use Thales\PhpBanking\resources\Response;
use Thales\PhpBanking\resources\View;
use Exception;

class TransactionController
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function create(): void
    {
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
            'message' => 'Transação efetivada com sucesso!',
            'transaction' => $transaction
        ], 201);
    }

    public function showByAccount(int $accountId): void
    {
        $transactions = $this->transactionService->getTransactionsByAccount($accountId);
        Response::sendJson($transactions, 200);
    }

    public function createForm(): void
    {
        View::render('Transaction.create');
    }

    public function extractForm(): void
    {
        View::render('Transaction.extract');
    }
}
