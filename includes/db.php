<?php
require_once 'config.php';

class Database {
    private $conn;
    private static $instance = null;
    
    // Private constructor - singleton pattern
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => true
                ]
            );
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                die("Database connection failed. Please try again later.");
            }
        }
    }
    
    // Get singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Get database connection
    public function getConnection() {
        return $this->conn;
    }
    
    // Execute a query with parameters
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Query Error: " . $e->getMessage() . " in query: " . $sql);
            } else {
                die("An error occurred while processing your request.");
            }
        }
    }

    // Fetch only one value (first column of the first row) from the result set.
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    

    // Fetch an entire row
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    // Fetch all rows
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Get the last inserted ID
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    // Begin a transaction
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    // Commit a transaction
    public function commit() {
        return $this->conn->commit();
    }
    
    // Rollback a transaction
    public function rollback() {
        return $this->conn->rollBack();
    }
    
    // Count rows
    public function count($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        return $this->conn->lastInsertId();
    }
}

// Create a helper function for easy access
function db() {
    return Database::getInstance();
}