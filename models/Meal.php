<?php
/**
 * Meal Model
 * Handles meal CRUD operations with image upload
 */

require_once __DIR__ . '/../config/database.php';

class Meal {
    private $conn;
    private $table = 'Meals';

    public $meal_id;
    public $name;
    public $description;
    public $price;
    public $image_path;
    public $category_id;
    public $is_available;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Create new meal
     */
    public function create($name, $description, $price, $category_id, $image_path = null, $is_available = 1) {
        $query = "INSERT INTO " . $this->table . " 
                  SET name = :name, 
                      description = :description, 
                      price = :price, 
                      category_id = :category_id, 
                      image_path = :image_path, 
                      is_available = :is_available";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image_path', $image_path);
        $stmt->bindParam(':is_available', $is_available);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all meals with category info
     */
    public function getAll() {
        $query = "SELECT m.*, c.category_name 
                  FROM " . $this->table . " m
                  LEFT JOIN Categories c ON m.category_id = c.category_id
                  ORDER BY m.meal_id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get available meals only
     */
    public function getAvailable($category_id = null) {
        if ($category_id) {
            $query = "SELECT m.*, c.category_name 
                      FROM " . $this->table . " m
                      LEFT JOIN Categories c ON m.category_id = c.category_id
                      WHERE m.is_available = 1 AND m.category_id = :category_id
                      ORDER BY m.name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $category_id);
        } else {
            $query = "SELECT m.*, c.category_name 
                      FROM " . $this->table . " m
                      LEFT JOIN Categories c ON m.category_id = c.category_id
                      WHERE m.is_available = 1
                      ORDER BY m.name ASC";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get available meals with pagination
     */
    public function getAvailablePaginated($category_id = null, $limit = 12, $offset = 0) {
        if ($category_id) {
            $query = "SELECT m.*, c.category_name 
                      FROM " . $this->table . " m
                      LEFT JOIN Categories c ON m.category_id = c.category_id
                      WHERE m.is_available = 1 AND m.category_id = :category_id
                      ORDER BY m.name ASC
                      LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        } else {
            $query = "SELECT m.*, c.category_name 
                      FROM " . $this->table . " m
                      LEFT JOIN Categories c ON m.category_id = c.category_id
                      WHERE m.is_available = 1
                      ORDER BY m.name ASC
                      LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count available meals
     */
    public function countAvailable($category_id = null) {
        if ($category_id) {
            $query = "SELECT COUNT(*) as total
                      FROM " . $this->table . " 
                      WHERE is_available = 1 AND category_id = :category_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        } else {
            $query = "SELECT COUNT(*) as total
                      FROM " . $this->table . " 
                      WHERE is_available = 1";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    /**
     * Search meals by name
     */
    public function search($search_term) {
        $query = "SELECT m.*, c.category_name 
                  FROM " . $this->table . " m
                  LEFT JOIN Categories c ON m.category_id = c.category_id
                  WHERE m.is_available = 1 
                  AND (m.name LIKE :search OR m.description LIKE :search)
                  ORDER BY m.name ASC";

        $stmt = $this->conn->prepare($query);
        $search_param = "%{$search_term}%";
        $stmt->bindParam(':search', $search_param);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Search meals with pagination
     */
    public function searchPaginated($search_term, $limit = 12, $offset = 0) {
        $query = "SELECT m.*, c.category_name 
                  FROM " . $this->table . " m
                  LEFT JOIN Categories c ON m.category_id = c.category_id
                  WHERE m.is_available = 1 
                  AND (m.name LIKE :search OR m.description LIKE :search)
                  ORDER BY m.name ASC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $search_param = "%{$search_term}%";
        $stmt->bindParam(':search', $search_param);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count search results
     */
    public function countSearch($search_term) {
        $query = "SELECT COUNT(*) as total
                  FROM " . $this->table . " 
                  WHERE is_available = 1 
                  AND (name LIKE :search OR description LIKE :search)";

        $stmt = $this->conn->prepare($query);
        $search_param = "%{$search_term}%";
        $stmt->bindParam(':search', $search_param);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    /**
     * Get meal by ID
     */
    public function findById($meal_id) {
        $query = "SELECT m.*, c.category_name 
                  FROM " . $this->table . " m
                  LEFT JOIN Categories c ON m.category_id = c.category_id
                  WHERE m.meal_id = :meal_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':meal_id', $meal_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Update meal
     */
    public function update($meal_id, $name, $description, $price, $category_id, $image_path = null) {
        if ($image_path) {
            $query = "UPDATE " . $this->table . " 
                      SET name = :name, 
                          description = :description, 
                          price = :price, 
                          category_id = :category_id, 
                          image_path = :image_path 
                      WHERE meal_id = :meal_id";
        } else {
            $query = "UPDATE " . $this->table . " 
                      SET name = :name, 
                          description = :description, 
                          price = :price, 
                          category_id = :category_id 
                      WHERE meal_id = :meal_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':meal_id', $meal_id);
        
        if ($image_path) {
            $stmt->bindParam(':image_path', $image_path);
        }

        return $stmt->execute();
    }

    /**
     * Toggle availability
     */
    public function toggleAvailability($meal_id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_available = NOT is_available 
                  WHERE meal_id = :meal_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':meal_id', $meal_id);

        return $stmt->execute();
    }

    /**
     * Delete meal
     */
    public function delete($meal_id) {
        // Get image path to delete file
        $meal = $this->findById($meal_id);
        
        $query = "DELETE FROM " . $this->table . " 
                  WHERE meal_id = :meal_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':meal_id', $meal_id);

        if ($stmt->execute()) {
            // Delete image file if exists
            if ($meal && $meal['image_path'] && file_exists(__DIR__ . '/../' . $meal['image_path'])) {
                unlink(__DIR__ . '/../' . $meal['image_path']);
            }
            return true;
        }
        return false;
    }

    /**
     * Handle image upload
     */
    public function uploadImage($file) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF allowed.'];
        }

        // Validate file size
        if ($file['size'] > $max_size) {
            return ['success' => false, 'message' => 'File too large. Maximum 2MB allowed.'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('meal_') . '.' . $extension;
        $upload_path = __DIR__ . '/../uploads/' . $filename;
        $relative_path = 'uploads/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return ['success' => true, 'path' => $relative_path];
        } else {
            return ['success' => false, 'message' => 'Failed to upload image.'];
        }
    }
}
?>
