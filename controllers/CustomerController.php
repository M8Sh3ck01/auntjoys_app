<?php
/**
 * Customer Controller
 * Handles customer cart and order operations
 */

require_once __DIR__ . '/../models/Meal.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';

class CustomerController {
    private $mealModel;
    private $orderModel;
    private $userModel;

    public function __construct() {
        $this->mealModel = new Meal();
        $this->orderModel = new Order();
        $this->userModel = new User();
    }

    /**
     * Add item to cart
     */
    public function addToCart() {
        $meal_id = $_POST['meal_id'] ?? '';
        $quantity = $_POST['quantity'] ?? 1;

        if (empty($meal_id)) {
            $_SESSION['errors'] = ["Invalid meal selected"];
            header('Location: /auntjoys_app/index.php?page=menu');
            exit;
        }

        // Get meal details
        $meal = $this->mealModel->findById($meal_id);

        if (!$meal || !$meal['is_available']) {
            $_SESSION['errors'] = ["This meal is not available"];
            header('Location: /auntjoys_app/index.php?page=menu');
            exit;
        }

        // Add to cart
        addToCart($meal['meal_id'], $meal['name'], $meal['price'], $meal['image_path'], $quantity);

        $_SESSION['success'] = "Added to cart!";
        header('Location: /auntjoys_app/index.php?page=menu');
        exit;
    }

    /**
     * Update cart item
     */
    public function updateCart() {
        $meal_id = $_POST['meal_id'] ?? '';
        $quantity = $_POST['quantity'] ?? 0;

        updateCartQuantity($meal_id, $quantity);

        header('Location: /auntjoys_app/index.php?page=cart');
        exit;
    }

    /**
     * Remove from cart
     */
    public function removeFromCart() {
        $meal_id = $_GET['id'] ?? '';
        
        removeFromCart($meal_id);

        $_SESSION['success'] = "Item removed from cart";
        header('Location: /auntjoys_app/index.php?page=cart');
        exit;
    }

    /**
     * Process checkout
     */
    public function checkout() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delivery_address = trim($_POST['delivery_address'] ?? '');
            $contact_number = trim($_POST['contact_number'] ?? '');
            $errors = [];

            // Validation
            if (empty($delivery_address)) {
                $errors[] = "Delivery address is required";
            }
            if (empty($contact_number)) {
                $errors[] = "Contact number is required";
            }

            if (isCartEmpty()) {
                $errors[] = "Your cart is empty";
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: /auntjoys_app/index.php?page=checkout');
                exit;
            }

            // Create order
            $cart_items = getCart();
            $order_id = $this->orderModel->create(
                getUserId(),
                $delivery_address,
                $contact_number,
                $cart_items
            );

            if ($order_id) {
                clearCart();
                $_SESSION['success'] = "Order placed successfully! Order #" . $order_id;
                header('Location: /auntjoys_app/index.php?page=my-orders');
                exit;
            } else {
                $_SESSION['errors'] = ["Failed to place order. Please try again."];
                header('Location: /auntjoys_app/index.php?page=checkout');
                exit;
            }
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auntjoys_app/index.php?action=customer&page=profile');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $errors = [];

        // Validation
        if (empty($username) || strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters";
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        if (!empty($errors)) {
            $_SESSION['error_message'] = implode(', ', $errors);
            header('Location: /auntjoys_app/index.php?action=customer&page=edit_profile');
            exit;
        }

        // Check if username is taken by another user
        $existing = $this->userModel->findByUsername($username);
        if ($existing && $existing['user_id'] != getUserId()) {
            $_SESSION['error_message'] = "Username already taken";
            header('Location: /auntjoys_app/index.php?action=customer&page=edit_profile');
            exit;
        }

        // Update profile
        $updated = $this->userModel->updateProfile(getUserId(), $username, $email, $phone_number);

        if ($updated) {
            $_SESSION['username'] = $username; // Update session username
            $_SESSION['success_message'] = "Profile updated successfully";
            header('Location: /auntjoys_app/index.php?action=customer&page=profile');
        } else {
            $_SESSION['error_message'] = "Failed to update profile";
            header('Location: /auntjoys_app/index.php?action=customer&page=edit_profile');
        }
        exit;
    }

    /**
     * Update password
     */
    public function updatePassword() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auntjoys_app/index.php?action=customer&page=profile');
            exit;
        }

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $errors = [];

        // Validation
        if (empty($current_password)) {
            $errors[] = "Current password is required";
        }

        if (empty($new_password) || strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }

        if (!empty($errors)) {
            $_SESSION['error_message'] = implode(', ', $errors);
            header('Location: /auntjoys_app/index.php?action=customer&page=change_password');
            exit;
        }

        // Verify current password
        $user = $this->userModel->getById(getUserId());
        if (!password_verify($current_password, $user['password_hash'])) {
            $_SESSION['error_message'] = "Current password is incorrect";
            header('Location: /auntjoys_app/index.php?action=customer&page=change_password');
            exit;
        }

        // Update password
        $updated = $this->userModel->updatePassword(getUserId(), $new_password);

        if ($updated) {
            $_SESSION['success_message'] = "Password updated successfully";
            header('Location: /auntjoys_app/index.php?action=customer&page=profile');
        } else {
            $_SESSION['error_message'] = "Failed to update password";
            header('Location: /auntjoys_app/index.php?action=customer&page=change_password');
        }
        exit;
    }
}
?>
