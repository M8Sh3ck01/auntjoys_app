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
                    require_once './controllers/CustomerController.php';
                    $controller = new CustomerController();
                    $controller->showProfile();
                    exit;
                case 'edit_profile':
                    require_once './controllers/CustomerController.php';
                    $controller = new CustomerController();
                    $controller->showEditProfile();
                    exit;
                case 'change_password':
                    require_once './controllers/CustomerController.php';
                    $controller = new CustomerController();
                    $controller->showChangePassword();
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
        require_once './controllers/AdminController.php';
        $controller = new AdminController();
        $controller->showDashboard();
        break;

    case 'admin/meals':
        require_once './controllers/AdminController.php';
        $controller = new AdminController();
        if ($action) {
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
            $controller->showMeals();
        }
        break;

    case 'admin/categories':
        require_once './controllers/AdminController.php';
        $controller = new AdminController();
        if ($action) {
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
            $controller->showCategories();
        }
        break;

    case 'admin/users':
        require_once './controllers/AdminController.php';
        $controller = new AdminController();
        if ($action) {
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
            $controller->showUsers();
        }
        break;

    // Sales routes
    case 'sales/orders':
        require_once './controllers/SalesController.php';
        $controller = new SalesController();
        if ($action === 'update-status') {
            $controller->updateOrderStatus();
        } else {
            $controller->showOrders();
        }
        break;

    // Manager routes
    case 'manager/dashboard':
        require_once './controllers/ManagerController.php';
        $controller = new ManagerController();
        $controller->showDashboard();
        break;

    case 'manager/reports':
        require_once './controllers/ManagerController.php';
        $controller = new ManagerController();
        if ($action === 'export-excel') {
            $controller->exportExcel();
        } elseif ($action === 'export-pdf') {
            $controller->exportPDF();
        } else {
            $controller->showReports();
        }
        break;

    // Customer routes
    case 'menu':
        require_once './controllers/CustomerController.php';
        $controller = new CustomerController();
        $controller->showMenu();
        break;
    case 'cart':
        require_once './controllers/CustomerController.php';
        $controller = new CustomerController();
        if ($action) {
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
            $controller->showCart();
        }
        break;

    case 'checkout':
        require_once './controllers/CustomerController.php';
        $controller = new CustomerController();
        if ($action === 'submit') {
            $controller->checkout();
        } else {
            $controller->showCheckout();
        }
        break;

    case 'my-orders':
        require_once './controllers/CustomerController.php';
        $controller = new CustomerController();
        $controller->showOrders();
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
