<?php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private $host = '127.0.0.1';
    private $db_name = 'talent_hub';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection(): ?PDO
    {
        $this->conn = null;

        try {
            // n connectiw m3a mysql database
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            // n configuriw pdo bach i throw exceptions ila kan chi error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // nkhliw fetch mode dima associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            // ila ma connectatch database, ghans7bso script o n affichew error
            die("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}