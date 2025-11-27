<?php
/**
 * Order Model
 * Handles order creation and management
 */

require_once __DIR__ . '/../config/database.php';

class Order {
    private $conn;
    private $table = 'Orders';
    private $items_table = 'Order_Items';

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Create new order with items
     */
    public function create($user_id, $delivery_address, $contact_number, $cart_items) {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // Calculate total
            $total = 0;
            foreach ($cart_items as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Insert order
            $query = "INSERT INTO " . $this->table . " 
                      SET user_id = :user_id, 
                          delivery_address = :delivery_address, 
                          contact_number = :contact_number, 
                          total_amount = :total_amount, 
                          status = 'Pending'";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':delivery_address', $delivery_address);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':total_amount', $total);
            $stmt->execute();

            $order_id = $this->conn->lastInsertId();

            // Insert order items
            $itemQuery = "INSERT INTO " . $this->items_table . " 
                          SET order_id = :order_id, 
                              meal_id = :meal_id, 
                              quantity = :quantity, 
                              unit_price = :unit_price";

            $itemStmt = $this->conn->prepare($itemQuery);

            foreach ($cart_items as $item) {
                $itemStmt->bindParam(':order_id', $order_id);
                $itemStmt->bindParam(':meal_id', $item['meal_id']);
                $itemStmt->bindParam(':quantity', $item['quantity']);
                $itemStmt->bindParam(':unit_price', $item['price']);
                $itemStmt->execute();
            }

            // Commit transaction
            $this->conn->commit();

            return $order_id;

        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Get orders by user ID
     */
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  ORDER BY order_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get all orders (for admin/sales)
     */
    public function getAll($status = null) {
        if ($status) {
            $query = "SELECT o.*, u.username 
                      FROM " . $this->table . " o
                      LEFT JOIN Users u ON o.user_id = u.user_id
                      WHERE o.status = :status
                      ORDER BY o.order_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
        } else {
            $query = "SELECT o.*, u.username 
                      FROM " . $this->table . " o
                      LEFT JOIN Users u ON o.user_id = u.user_id
                      ORDER BY o.order_date DESC";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get order by ID with items
     */
    public function findById($order_id) {
        $query = "SELECT o.*, u.username, u.email 
                  FROM " . $this->table . " o
                  LEFT JOIN Users u ON o.user_id = u.user_id
                  WHERE o.order_id = :order_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();

        $order = $stmt->fetch();

        if ($order) {
            // Get order items
            $order['items'] = $this->getOrderItems($order_id);
        }

        return $order;
    }

    /**
     * Get order items
     */
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, m.name as meal_name, m.image_path 
                  FROM " . $this->items_table . " oi
                  LEFT JOIN Meals m ON oi.meal_id = m.meal_id
                  WHERE oi.order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Update order status
     */
    public function updateStatus($order_id, $status) {
        $allowed_statuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];
        
        if (!in_array($status, $allowed_statuses)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " 
                  SET status = :status 
                  WHERE order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $order_id);

        return $stmt->execute();
    }

    /**
     * Get orders statistics for reporting
     */
    public function getStatistics($month = null, $year = null) {
        if ($month && $year) {
            $query = "SELECT 
                        COUNT(*) as total_orders,
                        SUM(total_amount) as total_revenue,
                        AVG(total_amount) as avg_order_value
                      FROM " . $this->table . "
                      WHERE MONTH(order_date) = :month 
                      AND YEAR(order_date) = :year";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year);
        } else {
            $query = "SELECT 
                        COUNT(*) as total_orders,
                        SUM(total_amount) as total_revenue,
                        AVG(total_amount) as avg_order_value
                      FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get best selling meals
     */
    public function getBestSellers($limit = 10, $month = null, $year = null) {
        if ($month && $year) {
            $query = "SELECT 
                        m.name,
                        m.image_path,
                        SUM(oi.quantity) as total_sold,
                        SUM(oi.quantity * oi.unit_price) as revenue
                      FROM " . $this->items_table . " oi
                      JOIN Meals m ON oi.meal_id = m.meal_id
                      JOIN " . $this->table . " o ON oi.order_id = o.order_id
                      WHERE MONTH(o.order_date) = :month 
                      AND YEAR(o.order_date) = :year
                      GROUP BY oi.meal_id
                      ORDER BY total_sold DESC
                      LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        } else {
            $query = "SELECT 
                        m.name,
                        m.image_path,
                        SUM(oi.quantity) as total_sold,
                        SUM(oi.quantity * oi.unit_price) as revenue
                      FROM " . $this->items_table . " oi
                      JOIN Meals m ON oi.meal_id = m.meal_id
                      GROUP BY oi.meal_id
                      ORDER BY total_sold DESC
                      LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
