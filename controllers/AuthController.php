<?php
/**
 * Authentication Controller
 * Handles user login, registration, and logout
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/auth.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Handle user login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $errors = [];

            // Validation
            if (empty($username)) {
                $errors[] = "Username is required";
            }
            if (empty($password)) {
                $errors[] = "Password is required";
            }

            if (empty($errors)) {
                // Find user
                $user = $this->userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['role_name'] = $user['role_name'];

                    // Redirect based on role
                    switch ($user['role_id']) {
                        case 2: // Administrator
                            header('Location: /auntjoys_app/index.php?page=admin/dashboard');
                            exit;
                        case 3: // Sales Staff
                            header('Location: /auntjoys_app/index.php?page=sales/orders');
                            exit;
                        case 4: // Manager
                            header('Location: /auntjoys_app/index.php?page=manager/dashboard');
                            exit;
                        default: // Customer
                            header('Location: /auntjoys_app/index.php');
                            exit;
                    }
                } else {
                    $errors[] = "Invalid username or password";
                }
            }

            // If errors, show login form with errors
            $_SESSION['errors'] = $errors;
            header('Location: /auntjoys_app/index.php?page=login');
            exit;
        }
    }

    /**
     * Handle user registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $errors = [];

            // Validation
            if (empty($username) || strlen($username) < 3) {
                $errors[] = "Username must be at least 3 characters";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Valid email is required";
            }
            if (empty($password) || strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }

            // Check if username/email already exists
            if (empty($errors)) {
                if ($this->userModel->usernameExists($username)) {
                    $errors[] = "Username already taken";
                }
                if ($this->userModel->emailExists($email)) {
                    $errors[] = "Email already registered";
                }
            }

            if (empty($errors)) {
                // Create user
                try {
                    if ($this->userModel->create($username, $email, $phone, $password)) {
                        $_SESSION['success'] = "Registration successful! Please login.";
                        header('Location: /auntjoys_app/index.php?page=login');
                        exit;
                    } else {
                        $errors[] = "Registration failed. Please try again.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            // If errors, show registration form with errors
            $_SESSION['errors'] = $errors;
            header('Location: /auntjoys_app/index.php?page=register');
            exit;
        }
    }

    /**
     * Handle user logout
     */
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /auntjoys_app/index.php?page=login');
        exit;
    }
}
?>
