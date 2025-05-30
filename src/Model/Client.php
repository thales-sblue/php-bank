<?php

require_once __DIR__ . '/../Database/Database.php';

class Client
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

        return $stmt->execute();
    }

    public function getClient($id)
    {
        $query = "SELECT * FROM client WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllClients()
    {
        $query = "SELECT * FROM client";
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
        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }
}
