<?php

namespace Thales\PhpBanking\Model\Client;

use Thales\PhpBanking\config\Database\Database;
use PDO;
use PDOException;

class ClientRepository implements ClientRepositoryInterface
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    public function createClient($username, $password, $name, $cpfcnpj, $email)
    {
        $query = "INSERT INTO client (username, password, name, cpfcnpj, email)
                  VALUES (:username, :password, :name, :cpfcnpj, :email)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':cpfcnpj', $cpfcnpj);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            $id = $this->conn->lastInsertId();
            return $this->getClient($id);
        }

        return false;
    }

    public function getClient($param)
    {
        if (is_numeric($param)) {
            $query = "SELECT id, username, name, cpfcnpj, email FROM client WHERE id = :param";
        } else {
            $query = "SELECT id, username, name, password, cpfcnpj, email FROM client WHERE username = :param";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':param', $param);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAllClients()
    {
        $query = "SELECT id, username, name, cpfcnpj, email FROM client";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateClient($id, $username, $password, $name, $email)
    {
        $query = "UPDATE client
                  SET username = :username,
                      password = :password,
                      name = :name,
                      email = :email
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            return $this->getClient($id);
        }

        return false;
    }

    public function getClientAccounts($idClient)
    {
        $query = "SELECT cli.username, cli.name, cli.cpfcnpj, cli.email, acc.id, acc.balance, acc.type, acc.active
                    FROM client cli
                    JOIN account acc
                    ON cli.id = acc.client_id
                   WHERE cli.id = :idClient";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idClient', $idClient);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
