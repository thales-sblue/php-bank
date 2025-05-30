<?php

require_once __DIR__ . '/../Database/Database.php';

class Transaction
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    public function createTransaction($accountId, $amount, $type, $transferId = null)
    {
        if ($transferId !== null) {
            $query = "INSERT INTO account_transaction (account_id, amount, type, transfer_id) 
                  VALUES (:accountId, :amount, :type, :transferId)";
        } else {
            $query = "INSERT INTO account_transaction (account_id, amount, type) 
                  VALUES (:accountId, :amount, :type)";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':accountId', $accountId);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':type', $type);

        if ($transferId !== null) {
            $stmt->bindParam(':transferId', $transferId);
        }

        return $stmt->execute();
    }

    public function getTransactionsByAccount($accountId)
    {
        $query = "SELECT * FROM account_transaction WHERE account_id = :accountId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':accountId', $accountId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTransactions()
    {
        $query = "SELECT * FROM account_transaction";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
