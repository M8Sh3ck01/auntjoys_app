<?php
/**
 * Admin Controller
 * Handles admin operations for meals and categories
 */

require_once __DIR__ . '/../models/Meal.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../includes/auth.php';

class AdminController {
    private $mealModel;
    private $categoryModel;
    private $userModel;
    private $orderModel;

    public function __construct() {
        $this->mealModel = new Meal();
        $this->categoryModel = new Category();
        $this->userModel = new User();
        $this->orderModel = new Order();
    }

    public function showDashboard() {
        requireRole([2]);

        $totalMeals = count($this->mealModel->getAll());
        $totalCategories = count($this->categoryModel->getAll());
        $totalUsers = count($this->userModel->getAll());
        $totalOrders = count($this->orderModel->getAll());

        $stats = $this->orderModel->getStatistics();

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function showMeals() {
        requireRole([2]);

        $meals = $this->mealModel->getAll();
        $categories = $this->categoryModel->getAll();

        require __DIR__ . '/../views/admin/meals.php';
    }

    public function showCategories() {
        requireRole([2]);

        $categories = $this->categoryModel->getAll();

        require __DIR__ . '/../views/admin/categories.php';
    }

    public function showUsers() {
        requireRole([2]);

        $users = $this->userModel->getAll();

        require __DIR__ . '/../views/admin/users.php';
    }

    /**
     * Handle category creation
     */
    public function createCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_name = trim($_POST['category_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];

            // Validation
            if (empty($category_name)) {
                $errors[] = "Category name is required";
            }

            if ($this->categoryModel->nameExists($category_name)) {
                $errors[] = "Category name already exists";
            }

            if (empty($errors)) {
                if ($this->categoryModel->create($category_name, $description)) {
                    $_SESSION['success'] = "Category created successfully!";
                } else {
                    $_SESSION['errors'] = ["Failed to create category"];
                }
            } else {
                $_SESSION['errors'] = $errors;
            }

            header('Location: /auntjoys_app/index.php?page=admin/categories');
            exit;
        }
    }

    /**
     * Handle category update
     */
    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['category_id'] ?? '';
            $category_name = trim($_POST['category_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];

            if (empty($category_name)) {
                $errors[] = "Category name is required";
            }

            if ($this->categoryModel->nameExists($category_name, $category_id)) {
                $errors[] = "Category name already exists";
            }

            if (empty($errors)) {
                if ($this->categoryModel->update($category_id, $category_name, $description)) {
                    $_SESSION['success'] = "Category updated successfully!";
                } else {
                    $_SESSION['errors'] = ["Failed to update category"];
                }
            } else {
                $_SESSION['errors'] = $errors;
            }

            header('Location: /auntjoys_app/index.php?page=admin/categories');
            exit;
        }
    }

    /**
     * Handle category deletion
     */
    public function deleteCategory() {
        $category_id = $_GET['id'] ?? '';

        if ($this->categoryModel->delete($category_id)) {
            $_SESSION['success'] = "Category deleted successfully!";
        } else {
            $_SESSION['errors'] = ["Cannot delete category. It may contain meals."];
        }

        header('Location: /auntjoys_app/index.php?page=admin/categories');
        exit;
    }

    /**
     * Handle meal creation
     */
    public function createMeal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = $_POST['price'] ?? 0;
            $category_id = $_POST['category_id'] ?? '';
            $errors = [];
            $image_path = null;

            // Validation
            if (empty($name)) {
                $errors[] = "Meal name is required";
            }
            if (empty($price) || $price <= 0) {
                $errors[] = "Valid price is required";
            }
            if (empty($category_id)) {
                $errors[] = "Category is required";
            }

            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $upload_result = $this->mealModel->uploadImage($_FILES['image']);
                if ($upload_result['success']) {
                    $image_path = $upload_result['path'];
                } else {
                    $errors[] = $upload_result['message'];
                }
            }

            if (empty($errors)) {
                if ($this->mealModel->create($name, $description, $price, $category_id, $image_path)) {
                    $_SESSION['success'] = "Meal created successfully!";
                    header('Location: /auntjoys_app/index.php?page=admin/meals');
                    exit;
                } else {
                    $errors[] = "Failed to create meal";
                }
            }

            $_SESSION['errors'] = $errors;
            header('Location: /auntjoys_app/index.php?page=admin/meals');
            exit;
        }
    }

    /**
     * Handle meal update
     */
    public function updateMeal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $meal_id = $_POST['meal_id'] ?? '';
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = $_POST['price'] ?? 0;
            $category_id = $_POST['category_id'] ?? '';
            $errors = [];
            $image_path = null;

            // Validation
            if (empty($name)) {
                $errors[] = "Meal name is required";
            }
            if (empty($price) || $price <= 0) {
                $errors[] = "Valid price is required";
            }
            if (empty($category_id)) {
                $errors[] = "Category is required";
            }

            // Handle image upload (optional on update)
            if (!empty($_FILES['image']['name'])) {
                $upload_result = $this->mealModel->uploadImage($_FILES['image']);
                if ($upload_result['success']) {
                    $image_path = $upload_result['path'];
                    
                    // Delete old image
                    $old_meal = $this->mealModel->findById($meal_id);
                    if ($old_meal && $old_meal['image_path'] && file_exists(__DIR__ . '/../' . $old_meal['image_path'])) {
                        unlink(__DIR__ . '/../' . $old_meal['image_path']);
                    }
                } else {
                    $errors[] = $upload_result['message'];
                }
            }

            if (empty($errors)) {
                if ($this->mealModel->update($meal_id, $name, $description, $price, $category_id, $image_path)) {
                    $_SESSION['success'] = "Meal updated successfully!";
                } else {
                    $_SESSION['errors'] = ["Failed to update meal"];
                }
            } else {
                $_SESSION['errors'] = $errors;
            }

            header('Location: /auntjoys_app/index.php?page=admin/meals');
            exit;
        }
    }

    /**
     * Toggle meal availability
     */
    public function toggleMealAvailability() {
        $meal_id = $_GET['id'] ?? '';

        if ($this->mealModel->toggleAvailability($meal_id)) {
            $_SESSION['success'] = "Meal availability updated!";
        } else {
            $_SESSION['errors'] = ["Failed to update availability"];
        }

        header('Location: /auntjoys_app/index.php?page=admin/meals');
        exit;
    }

    /**
     * Handle meal deletion
     */
    public function deleteMeal() {
        $meal_id = $_GET['id'] ?? '';

        if ($this->mealModel->delete($meal_id)) {
            $_SESSION['success'] = "Meal deleted successfully!";
        } else {
            $_SESSION['errors'] = ["Failed to delete meal"];
        }

        header('Location: /auntjoys_app/index.php?page=admin/meals');
        exit;
    }

    /**
     * Create new user
     */
    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $role_id = $_POST['role_id'] ?? 1;
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

            // Check duplicates
            if ($this->userModel->usernameExists($username)) {
                $errors[] = "Username already exists";
            }
            if ($this->userModel->emailExists($email)) {
                $errors[] = "Email already exists";
            }

            if (empty($errors)) {
                if ($this->userModel->create($username, $email, $phone, $password, $role_id)) {
                    $_SESSION['success'] = "User created successfully!";
                } else {
                    $_SESSION['errors'] = ["Failed to create user"];
                }
            } else {
                $_SESSION['errors'] = $errors;
            }

            header('Location: /auntjoys_app/index.php?page=admin/users');
            exit;
        }
    }

    /**
     * Update user role
     */
    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? '';
            $role_id = $_POST['role_id'] ?? '';

            if (empty($user_id) || empty($role_id)) {
                $_SESSION['errors'] = ["Invalid request"];
            } else {
                if ($this->userModel->updateRole($user_id, $role_id)) {
                    $_SESSION['success'] = "User role updated successfully!";
                } else {
                    $_SESSION['errors'] = ["Failed to update role"];
                }
            }

            header('Location: /auntjoys_app/index.php?page=admin/users');
            exit;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser() {
        $user_id = $_GET['id'] ?? '';

        // Prevent deleting yourself
        if ($user_id == getUserId()) {
            $_SESSION['errors'] = ["Cannot delete your own account"];
        } else {
            if ($this->userModel->delete($user_id)) {
                $_SESSION['success'] = "User deleted successfully!";
            } else {
                $_SESSION['errors'] = ["Failed to delete user"];
            }
        }

        header('Location: /auntjoys_app/index.php?page=admin/users');
        exit;
    }
}
?>
