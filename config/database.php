<?php
/**
 * Database Configuration and Connection Class
 * Singleton pattern for secure PDO connection to MySQL
 * Loads credentials from .env file for security
 */

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Load environment variables
        $env_file = __DIR__ . '/../.env';
        if (!file_exists($env_file)) {
            die("Error: .env file not found at " . $env_file);
        }
        
        $env = $this->loadEnv($env_file);
        
        $host = $env['DB_HOST'] ?? 'localhost';
        $db_name = $env['DB_NAME'] ?? 'users';
        $username = $env['DB_USER'] ?? 'root';
        $password = $env['DB_PASSWORD'] ?? '';
        
        try {
            $dsn = "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $username, $password);
            
            // Set error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Return associative arrays by default
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    
    /**
     * Load environment variables from .env file
     */
    private function loadEnv($file) {
        $env = [];
        if (file_exists($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                // Parse KEY=VALUE
                if (strpos($line, '=') !== false) {
                    [$key, $value] = explode('=', $line, 2);
                    $env[trim($key)] = trim($value);
                }
            }
        }
        return $env;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
