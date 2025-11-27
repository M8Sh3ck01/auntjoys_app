<?php
require_once __DIR__ . '/../../models/Meal.php';
require_once __DIR__ . '/../../models/Category.php';

$mealModel = new Meal();
$categoryModel = new Category();

$meals = $mealModel->getAll();
$categories = $categoryModel->getAll();

// For editing
$editMeal = null;
if (isset($_GET['edit'])) {
    $editMeal = $mealModel->findById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meals - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Design tokens and theme -->
    <link rel="stylesheet" href="/auntjoys_app/assets/css/tokens.css">
    <link rel="stylesheet" href="/auntjoys_app/assets/css/theme.css">
    <link rel="stylesheet" href="/auntjoys_app/assets/css/sidebar.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand ms-3" href="index.php?page=admin/dashboard">
                <i class="fas fa-utensils"></i> Admin Panel
            </a>
        </div>
    </nav>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="admin-sidebar col-md-2">
            <div class="sidebar-header">
                <div class="logo-wrapper">
                    <i class="fas fa-utensils"></i>
                </div>
                <h5 class="restaurant-name">Aunt Joy's</h5>
                <p class="sidebar-tagline">Admin Panel</p>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=admin/dashboard">
                        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=admin/meals">
                        <i class="fas fa-hamburger"></i> <span>Meals</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=admin/categories">
                        <i class="fas fa-list"></i> <span>Categories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=admin/users">
                        <i class="fas fa-users"></i> <span>Users</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-profile-avatar">
                        <?php echo strtoupper(substr(getUsername(), 0, 1)); ?>
                    </div>
                    <div class="user-profile-info">
                        <p class="user-profile-name"><?php echo htmlspecialchars(getUsername()); ?></p>
                        <p class="user-profile-role">Administrator</p>
                    </div>
                </div>
                <a href="index.php?page=logout" class="btn btn-outline-light btn-sm w-100 mt-3">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Main content -->
        <main class="admin-content col-md-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Meals</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMealModal">
                        <i class="fas fa-plus"></i> Add New Meal
                    </button>
                </div>

                <?php
                // Display messages
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show">
                            ' . htmlspecialchars($_SESSION['success']) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    unset($_SESSION['success']);
                }
                if (isset($_SESSION['errors'])) {
                    foreach ($_SESSION['errors'] as $error) {
                        echo '<div class="alert alert-danger alert-dismissible fade show">
                                ' . htmlspecialchars($error) . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
                    }
                    unset($_SESSION['errors']);
                }
                ?>

                <?php if (empty($categories)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Please create <a href="index.php?page=admin/categories">categories</a> first before adding meals.
                    </div>
                <?php endif; ?>

                <?php if (empty($meals)): ?>
                    <div class="alert alert-info">No meals yet. Click "Add New Meal" to create one!</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($meals as $meal): ?>
                                    <tr>
                                        <td>
                                            <?php if ($meal['image_path']): ?>
                                                <img src="<?php echo htmlspecialchars($meal['image_path']); ?>" 
                                                     alt="<?php echo htmlspecialchars($meal['name']); ?>" 
                                                     style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                                            <?php else: ?>
                                                <div style="width: 60px; height: 60px;" class="bg-secondary rounded d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-utensils text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($meal['name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars(substr($meal['description'], 0, 50)); ?><?php echo strlen($meal['description']) > 50 ? '...' : ''; ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($meal['category_name']); ?></td>
                                        <td>MWK <?php echo number_format($meal['price'], 2); ?></td>
                                        <td>
                                            <?php if ($meal['is_available']): ?>
                                                <span class="badge bg-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Out of Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?page=admin/meals&action=toggle&id=<?php echo $meal['meal_id']; ?>" 
                                               class="btn btn-sm btn-info" title="Toggle Availability">
                                                <i class="fas fa-toggle-on"></i>
                                            </a>
                                            <button class="btn btn-sm btn-warning" onclick="editMeal(<?php echo htmlspecialchars(json_encode($meal)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="index.php?page=admin/meals&action=delete&id=<?php echo $meal['meal_id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this meal?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
        </main>
    </div>

    <!-- Add Meal Modal -->
    <div class="modal fade" id="addMealModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php?page=admin/meals&action=create" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Meal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Meal Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (MWK) *</label>
                            <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>">
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image (JPG, PNG, GIF - Max 2MB)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Meal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Meal Modal -->
    <div class="modal fade" id="editMealModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php?page=admin/meals&action=update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="meal_id" id="edit_meal_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Meal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Meal Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (MWK) *</label>
                            <input type="number" class="form-control" name="price" id="edit_price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category_id" id="edit_category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>">
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Change Image (Optional)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Meal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script src="/auntjoys_app/assets/js/sidebar.js"></script>
    <script>
        function editMeal(meal) {
            document.getElementById('edit_meal_id').value = meal.meal_id;
            document.getElementById('edit_name').value = meal.name;
            document.getElementById('edit_description').value = meal.description;
            document.getElementById('edit_price').value = meal.price;
            document.getElementById('edit_category_id').value = meal.category_id;
            
            new bootstrap.Modal(document.getElementById('editMealModal')).show();
        }
    </script>
</body>
</html>
