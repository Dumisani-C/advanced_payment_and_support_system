<?php
/**
 * Database Configuration Class
 * Modern PDO-based database connection with prepared statements
 */
class Database {
    private $host = "localhost";
    private $db_name = "ark_apartment_db";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            throw new Exception("Connection Error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
