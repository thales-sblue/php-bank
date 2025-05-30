<?php

require_once __DIR__ . '/../Model/Transfer.php';
require_once __DIR__ . '/../Service/TransactionService.php';
require_once __DIR__ . '/../Service/AccountService.php';

class TransferService
{
    private $transferModel;
    private $transactionService;
    private $accountService;

    public function __construct()
    {
        $this->transferModel = new Transfer();
        $this->transactionService = new TransactionService();
        $this->accountService = new AccountService();
    }

    public function createTransfer($fromAccountId, $toAccountId, $amount)
    {
        if (empty($fromAccountId) || empty($toAccountId) || empty($amount)) {
            throw new Exception("Campos obrigatórios não informados (fromAccountId/toAccountId/amount).");
        }

        return $this->transferModel->createTransfer($fromAccountId, $toAccountId, $amount);
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
            $transfer = $this->transferModel->createTransfer($fromAccountId, $toAccountId, $amount);
            if (!$transfer) {
                throw new Exception("Erro ao criar transferência.");
            }

            $this->transactionService->processTransferTransactions($transfer);
            $this->transferModel->updateTransferStatus($transfer['id'], 'completed');

            return $transfer;
        } catch (Exception $e) {
            $this->transferModel->updateTransferStatus($transfer['id'], 'failed');
            throw new Exception("Erro ao processar transferência: " . $e->getMessage());
        }
    }

    public function getTransfersByAccount($accountId)
    {
        if (empty($accountId)) {
            throw new Exception("Campo obrigatório não informado (accountId).");
        }

        return $this->transferModel->getTransfersByAccount($accountId);
    }

    public function getAllTransfers()
    {
        return $this->transferModel->getAllTransfers();
    }
}
