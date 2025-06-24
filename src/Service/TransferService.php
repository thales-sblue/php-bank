<?php

namespace Thales\PhpBanking\Service;

use Thales\PhpBanking\Model\Transfer\TransferRepositoryInterface;
use Thales\PhpBanking\Service\TransactionService;
use Thales\PhpBanking\Service\AccountService;
use Exception;

class TransferService
{
    private TransferRepositoryInterface $transferRepository;
    private TransactionService $transactionService;
    private AccountService $accountService;

    public function __construct(
        TransferRepositoryInterface $transferRepository,
        TransactionService $transactionService,
        AccountService $accountService
    ) {
        $this->transferRepository = $transferRepository;
        $this->transactionService = $transactionService;
        $this->accountService = $accountService;
    }

    public function createTransfer($fromAccountId, $toAccountId, $amount)
    {
        if (empty($fromAccountId) || empty($toAccountId) || empty($amount)) {
            throw new Exception("Campos obrigatórios não informados!");
        }

        return $this->transferRepository->createTransfer($fromAccountId, $toAccountId, $amount);
    }

    public function processTransfer($fromAccountId, $toAccountId, $amount)
    {
        if (empty($fromAccountId) || empty($toAccountId) || empty($amount)) {
            throw new Exception("Campos obrigatórios não informados (fromAccountId/toAccountId/amount).");
        }
        if (!$this->accountService->getAccount($fromAccountId)) {
            throw new Exception("Conta de origem não existe.");
        }

        if (!$this->accountService->getAccount($toAccountId)) {
            throw new Exception("Conta de destino não existe.");
        }

        try {
            $transfer = $this->transferRepository->createTransfer($fromAccountId, $toAccountId, $amount);
            if (!$transfer) {
                throw new Exception("Erro ao criar transferência.");
            }

            $this->transactionService->processTransferTransactions($transfer);
            $this->transferRepository->updateTransferStatus($transfer['id'], 'completed');

            return $transfer;
        } catch (Exception $e) {
            $this->transferRepository->updateTransferStatus($transfer['id'], 'failed');
            throw new Exception("Erro ao processar transferência: " . $e->getMessage());
        }
    }

    public function getTransfersByAccount($accountId)
    {
        if (empty($accountId)) {
            throw new Exception("Campo obrigatório não informado (accountId).");
        }

        return $this->transferRepository->getTransfersByAccount($accountId);
    }

    public function getAllTransfers()
    {
        return $this->transferRepository->getAllTransfers();
    }
}
