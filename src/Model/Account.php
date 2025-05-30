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
        return $stmt->execute();
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
        return $stmt->execute();
    }

    public function applyTransactionAmount($accountId, $amount)
    {
        $query = "UPDATE account SET balance = balance + :amount WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':id', $accountId);
        return $stmt->execute();
    }
}
