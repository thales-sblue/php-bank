<?php

require_once __DIR__ . '/../Model/Transaction.php';
require_once __DIR__ . '/../Service/AccountService.php';

class TransactionService
{
    private $transactionModel;
    private $accountService;

    public function __construct()
    {
        $this->transactionModel = new Transaction();
        $this->accountService = new AccountService();
    }

    public function createTransaction($accountId, $amount, $type)
    {
        if (empty($accountId) || empty($amount) || empty($type)) {
            throw new Exception("Campos obrigatórios não informados (accountId/amount/type).");
        }

        if (!in_array($type, ['deposito', 'saque'])) {
            throw new Exception("Tipo inválido. Use 'deposito' ou 'saque'.");
        }

        $processedTransaction = $this->accountService->processTransaction($accountId, $amount, $type);

        if (!$processedTransaction) {
            throw new Exception("Erro ao atualizar saldo da conta.");
        }

        return $this->transactionModel->createTransaction($accountId, $amount, $type);
    }

    public function processTransferTransactions($transfer)
    {
        $fromAccountId = $transfer['fromAccountId'] ?? null;
        $toAccountId   = $transfer['toAccountId'] ?? null;
        $amount        = $transfer['amount'] ?? null;

        if (!$fromAccountId || !$toAccountId || !$amount) {
            throw new Exception("Transferência incompleta: campos obrigatórios ausentes.");
        }


        $this->createTransaction($fromAccountId, $amount, 'saque');
        $this->createTransaction($toAccountId, $amount, 'deposito');
    }

    public function getTransactionsByAccount($accountId)
    {
        return $this->transactionModel->getTransactionsByAccount($accountId);
    }

    public function getAllTransactions()
    {
        return $this->transactionModel->getAllTransactions();
    }
}
