<?php
/**
 * User Model
 * Handles user CRUD operations, authentication, and role management
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'Users';

    public $user_id;
    public $username;
    public $password_hash;
    public $email;
    public $phone_number;
    public $role_id;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Create new user (Customer by default)
     * Throws exception with specific error message for constraint violations
     */
    public function create($username, $email, $phone, $password, $role_id = 1) {
        $query = "INSERT INTO " . $this->table . " 
                  SET username = :username, 
                      email = :email, 
                      phone_number = :phone, 
                      password_hash = :password_hash, 
                      role_id = :role_id";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password_hash', $hashed_password);
        $stmt->bindParam(':role_id', $role_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Check for duplicate username or email
            if (strpos($e->getMessage(), 'username') !== false || $e->getCode() == 23000) {
                throw new Exception("Username already taken", 1);
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                throw new Exception("Email already registered", 2);
            }
            throw $e;
        }
    }

    /**
     * Find user by username for login
     */
    public function findByUsername($username) {
        $query = "SELECT u.user_id, u.username, u.password_hash, u.email, u.phone_number, u.role_id, r.role_name
                  FROM " . $this->table . " u
                  INNER JOIN Roles r ON u.role_id = r.role_id
                  WHERE u.username = :username 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Find user by ID
     */
    public function findById($user_id) {
        $query = "SELECT u.user_id, u.username, u.email, u.phone_number, u.role_id, r.role_name
                  FROM " . $this->table . " u
                  INNER JOIN Roles r ON u.role_id = r.role_id
                  WHERE u.user_id = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get user by ID (with password hash for verification)
     */
    public function getById($user_id) {
        $query = "SELECT u.user_id, u.username, u.password_hash, u.email, u.phone_number, u.role_id, r.role_name
                  FROM " . $this->table . " u
                  INNER JOIN Roles r ON u.role_id = r.role_id
                  WHERE u.user_id = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get all users (for Admin)
     */
    public function getAll() {
        $query = "SELECT u.user_id, u.username, u.email, u.phone_number, r.role_name
                  FROM " . $this->table . " u
                  INNER JOIN Roles r ON u.role_id = r.role_id
                  ORDER BY u.user_id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Update user role (Admin only)
     */
    public function updateRole($user_id, $role_id) {
        $query = "UPDATE " . $this->table . " 
                  SET role_id = :role_id 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    /**
     * Delete user
     */
    public function delete($user_id) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        $query = "SELECT user_id FROM " . $this->table . " 
                  WHERE username = :username LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table . " 
                  WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Update user profile
     */
    public function updateProfile($user_id, $username, $email, $phone_number) {
        $query = "UPDATE " . $this->table . " 
                  SET username = :username, 
                      email = :email, 
                      phone_number = :phone_number 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':user_id', $user_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update password
     */
    public function updatePassword($user_id, $new_password) {
        $query = "UPDATE " . $this->table . " 
                  SET password_hash = :password_hash 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $hashed_password);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }
}
?>
