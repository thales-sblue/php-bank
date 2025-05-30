<?php

require_once __DIR__ . '/../Model/Account.php';

class AccountService
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = new Account();
    }

    public function createAccount($userId, $balance, $type)
    {
        if (empty($userId) || empty($type)) {
            throw new Exception("Campos obrigatórios não informados(userId/type).");
        }

        if (!in_array($type, ['corrente', 'poupanca'])) {
            throw new Exception("Type inválido. Use 'corrente' ou 'poupanca'.");
        }

        return $this->accountModel->createAccount($userId, $balance, $type);
    }

    public function getAccount($id)
    {
        $account = $this->accountModel->getAccount($id);
        if (!$account) {
            throw new Exception("Conta não encontrada.");
        }
        return $account;
    }

    public function getAllAccounts()
    {
        return $this->accountModel->getAllAccounts();
    }

    public function updateAccount($id, $balance, $type, $active)
    {
        $account = $this->accountModel->getAccount($id);
        if (!$account) {
            throw new Exception("Conta não encontrada para atualização.");
        }

        return $this->accountModel->updateAccount($id, $balance, $type, $active);
    }
}
