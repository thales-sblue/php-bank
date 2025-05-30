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
        $query = "INSERT INTO transfer (from_account_id, to_account_id, amount, status) 
                  VALUES (:fromAccountId, :toAccountId, :amount, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fromAccountId', $fromAccountId);
        $stmt->bindParam(':toAccountId', $toAccountId);
        $stmt->bindParam(':amount', $amount);
        $status = 'pending';
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $id = $this->conn->lastInsertId();
            return ['id' => $id, 'fromAccountId' => $fromAccountId, 'toAccountId' => $toAccountId, 'amount' => $amount];
        }

        return false;
    }

    public function getTransfersByAccount($accountId)
    {
        $query = "SELECT * FROM transfer WHERE from_account_id = :accountId OR to_account_id = :accountId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':accountId', $accountId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTransfers()
    {
        $query = "SELECT * FROM transfer";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTransferStatus($id, $status)
    {
        $query = "UPDATE transfer SET status = :status, executed_at = :executedAt WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $executedAt = date('Y-m-d H:i:s');
        $stmt->bindParam(':executedAt', $executedAt);

        return $stmt->execute();
    }
}
