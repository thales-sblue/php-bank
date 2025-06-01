<?php

namespace Thales\PhpBanking\Model\Account;

interface AccountRepositoryInterface
{
    public function createAccount($clientId, $balance, $type);
    public function getAccountByClientId($clientId, $type = null);
    public function getAccount($id);
    public function getAllAccounts();
    public function updateAccount($id, $balance, $type, $active);
    public function applyTransactionAmount($accountId, $amount);
}
