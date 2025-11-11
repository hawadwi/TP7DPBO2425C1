<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "best_schedule";
    public $conn;

    public function __construct() {
        try { // conn tuh connection
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            // untuk mengakses database dengan satu codingan / shortcut nya untuk mengakses database
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
?>