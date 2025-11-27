<?php
require_once __DIR__ . '/../../models/Category.php';
$categoryModel = new Category();
$categories = $categoryModel->getAll();

// For editing
$editCategory = null;
if (isset($_GET['edit'])) {
    $editCategory = $categoryModel->findById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
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
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 mb-0">Manage Categories</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Add New Category
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

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Categories List (<?php echo count($categories); ?>)</span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary" id="tableViewBtn">
                                        <i class="fas fa-table"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary active" id="cardViewBtn">
                                        <i class="fas fa-th"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($categories)): ?>
                                    <div class="alert alert-info">No categories yet. Create one to get started!</div>
                                <?php else: ?>
                                    <!-- Card View -->
                                    <div id="cardView" class="row">
                                        <?php foreach ($categories as $category): ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100 border-start border-primary border-4">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h5 class="card-title mb-0">
                                                                <i class="fas fa-tag text-primary"></i> 
                                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                                            </h5>
                                                            <span class="badge bg-secondary">#<?php echo $category['category_id']; ?></span>
                                                        </div>
                                                        <p class="card-text text-muted small">
                                                            <?php echo htmlspecialchars($category['description'] ?: 'No description provided'); ?>
                                                        </p>
                                                        <div class="d-flex gap-2 mt-3">
                                            <button class="btn btn-sm btn-warning" 
                                                               onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <a href="index.php?page=admin/categories&action=delete&id=<?php echo $category['category_id']; ?>" 
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('Delete this category? This will fail if it has meals.')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Table View -->
                                    <div id="tableView" class="table-responsive" style="display: none;">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($categories as $category): ?>
                                                    <tr>
                                                        <td><span class="badge bg-secondary"><?php echo $category['category_id']; ?></span></td>
                                                        <td><strong><?php echo htmlspecialchars($category['category_name']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars($category['description'] ?: '-'); ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning"
                                                               onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="index.php?page=admin/categories&action=delete&id=<?php echo $category['category_id']; ?>" 
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('Delete this category? This will fail if it has meals.')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
        </main>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php?page=admin/categories&action=create" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" class="form-control" name="category_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php?page=admin/categories&action=update" method="POST">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" class="form-control" name="category_name" id="edit_category_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_category_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script src="/auntjoys_app/assets/js/sidebar.js"></script>
    <script>
        function editCategory(category) {
            document.getElementById('edit_category_id').value = category.category_id;
            document.getElementById('edit_category_name').value = category.category_name;
            document.getElementById('edit_category_description').value = category.description || '';
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }
        // Toggle between card and table view
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        const cardViewBtn = document.getElementById('cardViewBtn');
        const tableViewBtn = document.getElementById('tableViewBtn');

        if (cardViewBtn && tableViewBtn) {
            cardViewBtn.addEventListener('click', () => {
                cardView.style.display = 'flex';
                tableView.style.display = 'none';
                cardViewBtn.classList.add('active');
                tableViewBtn.classList.remove('active');
                localStorage.setItem('categoryView', 'card');
            });

            tableViewBtn.addEventListener('click', () => {
                cardView.style.display = 'none';
                tableView.style.display = 'block';
                tableViewBtn.classList.add('active');
                cardViewBtn.classList.remove('active');
                localStorage.setItem('categoryView', 'table');
            });

            // Remember user preference
            const savedView = localStorage.getItem('categoryView');
            if (savedView === 'table') {
                tableViewBtn.click();
            }
        }
    </script>
</body>
</html>
