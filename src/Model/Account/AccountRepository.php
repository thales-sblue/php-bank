<?php

namespace Thales\PhpBanking\Model\Account;

use Exception;
use Thales\PhpBanking\config\Database\Database;
use PDO;

class AccountRepository implements AccountRepositoryInterface
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    public function createAccount($clientId, $balance, $type)
    {
        $query = "INSERT INTO account (client_id, balance, type) VALUES (:clientId, :balance, :type)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clientId', $clientId);
        $stmt->bindParam(':balance', $balance);
        $stmt->bindParam(':type', $type);

        if ($stmt->execute()) {
            return $this->getAccount($clientId, null);
        }

        return false;
    }

    public function getAccountByClientId($clientId, $type = null)
    {
        if ($type) {
            $query = "SELECT * FROM account WHERE client_id = :clientId AND type = :type";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':type', $type);
        } else {
            $query = "SELECT * FROM account WHERE client_id = :clientId";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->bindParam(':clientId', $clientId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAccount($clientId = null, $accountId = null)
    {
        if (!$clientId && !$accountId) {
            throw new Exception('É necessário informar clientId ou accountId.');
        }

        if ($clientId) {
            $query = "SELECT * FROM account WHERE client_id = :clientId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':clientId', $clientId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $query = "SELECT * FROM account WHERE id = :accountId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':accountId', $accountId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAllAccounts()
    {
        $query = "SELECT * FROM account";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAccount($accountId, $balance, $type, $active)
    {
        $query = "UPDATE account SET balance = :balance, type = :type, active = :active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $accountId);
        $stmt->bindParam(':balance', $balance);
        $stmt->bindParam(':type', $type);
        $active = (bool)$active;
        $stmt->bindParam(':active', $active, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return $this->getAccount(null, $accountId);
        }

        return false;
    }

    public function applyTransactionAmount($accountId, $amount)
    {
        $query = "UPDATE account SET balance = balance + :amount WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':id', $accountId);

        if ($stmt->execute()) {
            return $this->getAccount(null, $accountId);
        }

        return false;
    }
}
