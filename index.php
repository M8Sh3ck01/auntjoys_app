<?php
/**
 * Front Controller - Main Entry Point
 * All requests go through this file
 */

session_start();

// Load authentication functions
require_once __DIR__ . '/includes/auth.php';

// Get requested page
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';

// Handle action-based routes first
if ($action) {
    switch ($action) {
        case 'customer':
            requireLogin();
            $subpage = $_GET['page'] ?? '';
            switch ($subpage) {
                case 'profile':
                    require_once './views/customer/profile.php';
                    exit;
                case 'edit_profile':
                    require_once './views/customer/edit_profile.php';
                    exit;
                case 'change_password':
                    require_once './views/customer/change_password.php';
                    exit;
            }
            break;

        case 'update_profile':
            requireLogin();
            require_once './controllers/CustomerController.php';
            $controller = new CustomerController();
            $controller->updateProfile();
            exit;

        case 'update_password':
            requireLogin();
            require_once './controllers/CustomerController.php';
            $controller = new CustomerController();
            $controller->updatePassword();
            exit;
    }
}

// Handle AJAX requests first
if ($action === 'get_meal_detail' && isset($_GET['meal_id'])) {
    header('Content-Type: application/json');
    require_once './models/Meal.php';
    $mealModel = new Meal();
    $meal = $mealModel->findById($_GET['meal_id']);
    
    if ($meal) {
        echo json_encode([
            'success' => true,
            'meal' => $meal
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Meal not found'
        ]);
    }
    exit;
}

// Routing logic
switch ($page) {
    // Auth routes
    case 'login':
        if ($action === 'submit') {
            require_once './controllers/AuthController.php';
            $controller = new AuthController();
            $controller->login();
        } else {
            require_once './views/auth/login.php';
        }
        break;

    case 'register':
        if ($action === 'submit') {
            require_once './controllers/AuthController.php';
            $controller = new AuthController();
            $controller->register();
        } else {
            require_once './views/auth/register.php';
        }
        break;

    case 'logout':
        require_once './controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    // Admin routes
    case 'admin/dashboard':
        requireRole([2]); // Admin only
        require_once './views/admin/dashboard.php';
        break;

    case 'admin/meals':
        requireRole([2]);
        if ($action) {
            require_once './controllers/AdminController.php';
            $controller = new AdminController();
            switch ($action) {
                case 'create':
                    $controller->createMeal();
                    break;
                case 'update':
                    $controller->updateMeal();
                    break;
                case 'toggle':
                    $controller->toggleMealAvailability();
                    break;
                case 'delete':
                    $controller->deleteMeal();
                    break;
            }
        } else {
            require_once './views/admin/meals.php';
        }
        break;

    case 'admin/categories':
        requireRole([2]);
        if ($action) {
            require_once './controllers/AdminController.php';
            $controller = new AdminController();
            switch ($action) {
                case 'create':
                    $controller->createCategory();
                    break;
                case 'update':
                    $controller->updateCategory();
                    break;
                case 'delete':
                    $controller->deleteCategory();
                    break;
            }
        } else {
            require_once './views/admin/categories.php';
        }
        break;

    case 'admin/users':
        requireRole([2]);
        if ($action) {
            require_once './controllers/AdminController.php';
            $controller = new AdminController();
            switch ($action) {
                case 'create':
                    $controller->createUser();
                    break;
                case 'update-role':
                    $controller->updateUserRole();
                    break;
                case 'delete':
                    $controller->deleteUser();
                    break;
            }
        } else {
            require_once './views/admin/users.php';
        }
        break;

    // Sales routes
    case 'sales/orders':
        requireRole([3]); // Sales Staff only
        if ($action === 'update-status') {
            require_once './controllers/SalesController.php';
            $controller = new SalesController();
            $controller->updateOrderStatus();
        } else {
            require_once './views/sales/orders.php';
        }
        break;

    // Manager routes
    case 'manager/dashboard':
        requireRole([4]); // Manager only
        require_once './views/manager/dashboard.php';
        break;

    case 'manager/reports':
        requireRole([4]);
        if ($action === 'export-excel') {
            require_once './controllers/ManagerController.php';
            $controller = new ManagerController();
            $controller->exportExcel();
        } elseif ($action === 'export-pdf') {
            require_once './controllers/ManagerController.php';
            $controller = new ManagerController();
            $controller->exportPDF();
        } else {
            require_once './views/manager/reports.php';
        }
        break;

    // Customer routes
    case 'menu':
        require_once './views/customer/menu.php';
        break;

    case 'cart':
        if ($action) {
            require_once './controllers/CustomerController.php';
            $controller = new CustomerController();
            switch ($action) {
                case 'add':
                    $controller->addToCart();
                    break;
                case 'update':
                    $controller->updateCart();
                    break;
                case 'remove':
                    $controller->removeFromCart();
                    break;
            }
        } else {
            requireLogin();
            require_once './views/customer/cart.php';
        }
        break;

    case 'checkout':
        requireLogin();
        if ($action === 'submit') {
            require_once './controllers/CustomerController.php';
            $controller = new CustomerController();
            $controller->checkout();
        } else {
            require_once './views/customer/checkout.php';
        }
        break;

    case 'my-orders':
        requireLogin();
        require_once './views/customer/orders.php';
        break;

    // Unauthorized page
    case 'unauthorized':
        require_once './views/auth/unauthorized.php';
        break;

    // Home page
    case 'home':
    default:
        if (isLoggedIn()) {
            // Redirect based on role
            switch (getUserRole()) {
                case 2:
                    header('Location: index.php?page=admin/dashboard');
                    exit;
                case 3:
                    header('Location: index.php?page=sales/orders');
                    exit;
                case 4:
                    header('Location: index.php?page=manager/dashboard');
                    exit;
                default:
                    header('Location: index.php?page=menu');
                    exit;
            }
        } else {
            header('Location: index.php?page=menu');
            exit;
        }
        break;
}
?>
