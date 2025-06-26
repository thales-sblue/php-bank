<?php

namespace Thales\PhpBanking\Service;

use Thales\PhpBanking\Model\Transaction\TransactionRepositoryInterface;
use Thales\PhpBanking\Service\AccountService;
use Exception;

class TransactionService
{
    private $transactionRepository;
    private $accountService;

    public function __construct(TransactionRepositoryInterface $transactionRepository, AccountService $accountService)
    {
        $this->transactionRepository = $transactionRepository;
        $this->accountService = $accountService;
    }

    public function createTransaction($accountId, $amount, $type, $transferId = null)
    {
        if (empty($accountId) || empty($amount) || empty($type)) {
            throw new Exception("Campos obrigatórios não informados!");
        }

        if (!in_array($type, ['deposito', 'saque'])) {
            throw new Exception("Tipo inválido. Use 'deposito' ou 'saque'!");
        }

        $processedTransaction = $this->accountService->processTransaction($accountId, $amount, $type);

        if (!$processedTransaction) {
            throw new Exception("Erro ao atualizar saldo da conta!");
        }

        return $this->transactionRepository->createTransaction($accountId, $amount, $type, $transferId);
    }

    public function processTransferTransactions($transfer)
    {
        $transferId     = $transfer['id'] ?? null;
        $fromAccountId = $transfer['fromAccountId'] ?? null;
        $toAccountId   = $transfer['toAccountId'] ?? null;
        $amount        = $transfer['amount'] ?? null;

        if (!$fromAccountId || !$toAccountId || !$amount || !$transferId) {
            throw new Exception("Campos obrigatórios não informados!");
        }


        $this->createTransaction($fromAccountId, $amount, 'saque', $transferId);
        $this->createTransaction($toAccountId, $amount, 'deposito', $transferId);
    }

    public function getTransactionsByAccount($accountId)
    {
        return $this->transactionRepository->getTransactionsByAccount($accountId);
    }

    public function getAllTransactions()
    {
        return $this->transactionRepository->getAllTransactions();
    }
}
