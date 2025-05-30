<?php

require_once __DIR__ . '/../Database/Database.php';

class Transfer
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    public function createTransfer($fromAccountId, $toAccountId, $amount)
    {
        $query = "INSERT INTO transfer (from_account_id, to_account_id, amount) 
                  VALUES (:fromAccountId, :toAccountId, :amount)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fromAccountId', $fromAccountId);
        $stmt->bindParam(':toAccountId', $toAccountId);
        $stmt->bindParam(':amount', $amount);
        return $stmt->execute();
    }
}
