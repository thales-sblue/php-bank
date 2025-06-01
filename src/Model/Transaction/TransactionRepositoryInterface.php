<?php

namespace Thales\PhpBanking\Model\Transaction;

interface TransactionRepositoryInterface
{
    public function createTransaction($accountId, $amount, $type, $transferId = null);
    public function getTransactionsByAccount($accountId);
    public function getAllTransactions();
}
