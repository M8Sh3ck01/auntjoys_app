<?php
/**
 * Sales Controller
 * Handles order management for Sales Personnel
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../includes/auth.php';

class SalesController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    /**
     * Update order status
     */
    public function updateOrderStatus() {
        requireRole([3]); // Sales Staff only

        $order_id = $_POST['order_id'] ?? '';
        $status = $_POST['status'] ?? '';

        $allowed_statuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];

        if (!in_array($status, $allowed_statuses)) {
            $_SESSION['errors'] = ["Invalid status"];
            header('Location: /auntjoys_app/index.php?page=sales/orders');
            exit;
        }

        if ($this->orderModel->updateStatus($order_id, $status)) {
            $_SESSION['success'] = "Order status updated to: " . $status;
        } else {
            $_SESSION['errors'] = ["Failed to update order status"];
        }

        header('Location: /auntjoys_app/index.php?page=sales/orders');
        exit;
    }
}
?>
