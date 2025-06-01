<?php

namespace Thales\PhpBanking\Model\Transfer;

use Thales\PhpBanking\Database\Database;
use PDO;

class TransferRepository implements TransferRepositoryInterface
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
            return [
                'id' => $id,
                'fromAccountId' => $fromAccountId,
                'toAccountId' => $toAccountId,
                'amount' => $amount,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'executed_at' => null
            ];
        }

        return false;
    }

    public function updateTransferStatus($id, $status)
    {
        $query = "UPDATE transfer SET status = :status, executed_at = :executedAt WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $executedAt = date('Y-m-d H:i:s');
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':executedAt', $executedAt);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
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

    public function getTransferById($id)
    {
        $query = "SELECT * FROM transfer WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
