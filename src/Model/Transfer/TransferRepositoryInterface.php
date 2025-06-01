<?php

namespace Thales\PhpBanking\Model\Transfer;

interface TransferRepositoryInterface
{
    public function createTransfer($fromAccountId, $toAccountId, $amount);
    public function updateTransferStatus($id, $status);
    public function getTransfersByAccount($accountId);
    public function getAllTransfers();
    public function getTransferById($id);
}
