<?php
/**
 * Category Model
 * Handles meal category CRUD operations
 */

require_once __DIR__ . '/../config/database.php';

class Category {
    private $conn;
    private $table = 'Categories';

    public $category_id;
    public $category_name;
    public $description;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Create new category
     */
    public function create($category_name, $description = '') {
        $query = "INSERT INTO " . $this->table . " 
                  SET category_name = :category_name, 
                      description = :description";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all categories
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY category_name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get category by ID
     */
    public function findById($category_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE category_id = :category_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Update category
     */
    public function update($category_id, $category_name, $description) {
        $query = "UPDATE " . $this->table . " 
                  SET category_name = :category_name, 
                      description = :description 
                  WHERE category_id = :category_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);

        return $stmt->execute();
    }

    /**
     * Delete category
     */
    public function delete($category_id) {
        // Check if category has meals
        $checkQuery = "SELECT COUNT(*) as count FROM Meals WHERE category_id = :category_id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':category_id', $category_id);
        $checkStmt->execute();
        $result = $checkStmt->fetch();

        if ($result['count'] > 0) {
            return false; // Cannot delete category with meals
        }

        $query = "DELETE FROM " . $this->table . " 
                  WHERE category_id = :category_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);

        return $stmt->execute();
    }

    /**
     * Check if category name exists
     */
    public function nameExists($category_name, $exclude_id = null) {
        if ($exclude_id) {
            $query = "SELECT category_id FROM " . $this->table . " 
                      WHERE category_name = :category_name AND category_id != :exclude_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_name', $category_name);
            $stmt->bindParam(':exclude_id', $exclude_id);
        } else {
            $query = "SELECT category_id FROM " . $this->table . " 
                      WHERE category_name = :category_name LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_name', $category_name);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
