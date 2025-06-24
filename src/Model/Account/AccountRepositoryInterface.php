<?php

namespace Thales\PhpBanking\Model\Account;

interface AccountRepositoryInterface
{
    public function createAccount($clientId, $balance, $type);
    public function getAccount($clientId, $accountId);
    public function getAllAccounts();
    public function updateAccount($id, $balance, $type, $active);
    public function applyTransactionAmount($accountId, $amount);
}
