<?php

namespace Thales\PhpBanking\Service;

use Thales\PhpBanking\Model\Account\AccountRepositoryInterface;
use Exception;

class AccountService
{
    private AccountRepositoryInterface $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function createAccount($clientId, $type, $balance = 0)
    {
        if (empty($clientId) || empty($type)) {
            throw new Exception("Campos obrigatórios não informados(clientId/type).");
        }

        if (!in_array($type, ['corrente', 'poupanca'])) {
            throw new Exception("Type inválido. Use 'corrente' ou 'poupanca'.");
        }

        $accountExists = $this->accountRepository->getAccountByClientId($clientId, $type);
        if ($accountExists) {
            throw new Exception("Já existe uma conta do tipo {$type} para o cliente com ID {$clientId}.");
        }

        return $this->accountRepository->createAccount($clientId, $balance, $type);
    }

    public function getAccount($id)
    {
        $account = $this->accountRepository->getAccount($id);
        if (!$account) {
            throw new Exception("Conta com ID {$id} não encontrada.");
        }

        return $account;
    }

    public function getAllAccounts()
    {
        return $this->accountRepository->getAllAccounts();
    }

    public function updateAccount($id, $balance = null, $type = null, $active = null)
    {
        $account = $this->accountRepository->getAccount($id);

        if (!$account) {
            throw new Exception("Conta não encontrada para atualização.");
        }

        $balance = $balance !== null ? $balance : $account['balance'];
        $type    = $type    !== null ? $type    : $account['type'];
        $active  = $active  !== null ? (bool)$active : $account['active'];

        if (!in_array($type, ['corrente', 'poupanca'])) {
            throw new Exception("Tipo de conta inválido.");
        }

        return $this->accountRepository->updateAccount($id, $balance, $type, $active);
    }

    public function processTransaction($accountId, $amount, $type)
    {
        if (empty($accountId) || empty($amount) || empty($type)) {
            throw new Exception("Campos obrigatórios não informados.");
        }

        if (!in_array($type, ['deposito', 'saque'])) {
            throw new Exception("Tipo inválido.");
        }

        $account = $this->getAccount($accountId);
        if (!$account) {
            throw new Exception("Conta não encontrada.");
        }

        if (!$account['active']) {
            throw new Exception("Conta inativa.");
        }

        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception("Valor inválido.");
        }

        if ($type === 'saque' && $amount > $account['balance']) {
            throw new Exception("Saldo insuficiente.");
        }

        $amount = $type === 'saque' ? -abs($amount) : abs($amount);

        return $this->accountRepository->applyTransactionAmount($accountId, $amount);
    }
}
