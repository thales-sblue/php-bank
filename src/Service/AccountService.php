<?php

namespace Thales\PhpBanking\Service;

use Thales\PhpBanking\Model\Account\AccountRepositoryInterface;
use Thales\PhpBanking\resources\Response;
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

    public function getAccount($clientId = '', $accountId = '')
    {
        $account = $this->accountRepository->getAccount($clientId, $accountId);

        if (!$account) {
            throw new Exception("Cliente não possui conta ativa!");
        }

        return $account;
    }

    public function getAllAccounts()
    {
        return $this->accountRepository->getAllAccounts();
    }

    public function updateAccount($accountId, $balance = null, $type = null, $active = null)
    {
        $account = $this->accountRepository->getAccount(null, $accountId);

        Response::sendError($account, 500);
        if (!$account) {
            throw new Exception("Conta não encontrada para atualização.");
        }

        $balance = $balance !== null ? $balance : $account['balance'];
        $type    = $type    !== null ? $type    : $account['type'];
        $active  = $active  !== null ? $active : $account['active'] ?? '';

        if (!in_array($type, ['corrente', 'poupanca'])) {
            throw new Exception("Tipo de conta inválido.");
        }

        return $this->accountRepository->updateAccount($accountId, $balance, $type, $active);
    }

    public function processTransaction($accountId, $amount, $type)
    {
        if (empty($accountId) || empty($amount) || empty($type)) {
            throw new Exception("Campos obrigatórios não informados.");
        }

        if (!in_array($type, ['deposito', 'saque'])) {
            throw new Exception("Tipo inválido.");
        }

        $account = $this->getAccount(null, $accountId);
        if (!$account) {
            throw new Exception("Conta não encontrada.");
        }

        if ($account['active'] === false) {
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
