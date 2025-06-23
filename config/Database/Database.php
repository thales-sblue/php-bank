<?php

namespace Thales\PhpBanking\config\Database;

use PDO;
use PDOException;

class Database
{
    private $host = 'db';
    private $db = 'bankingdb';
    private $user = 'postgres';
    private $pass = 'postgres';
    private $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("pgsql:host={$this->host};dbname={$this->db}", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        return $this->conn;
    }
}
