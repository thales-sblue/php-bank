<?php

require_once __DIR__ . '/../Database/Database.php';

class Account
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
            $id = $this->conn->lastInsertId();
            return $this->getAccount($id);
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

    public function getAccount($id)
    {
        $query = "SELECT * FROM account WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
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

    public function updateAccount($id, $balance, $type, $active)
    {
        $query = "UPDATE account SET balance = :balance, type = :type, active = :active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':balance', $balance);
        $stmt->bindParam(':type', $type);
        $active = (bool)$active;
        $stmt->bindParam(':active', $active, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return $this->getAccount($id);
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
            return $this->getAccount($accountId);
        }

        return false;
    }
}
