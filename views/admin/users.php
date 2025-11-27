<?php
require_once __DIR__ . '/../../models/User.php';

$userModel = new User();
$users = $userModel->getAll();

// Role names mapping
$roleNames = [
    1 => 'Customer',
    2 => 'Administrator', 
    3 => 'Sales Staff',
    4 => 'Manager'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
                    <h1 class="h2">User Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus"></i> Add New User
                    </button>
                </div>

                <?php
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

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td>
                                        <i class="fas fa-user-circle"></i> 
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone_number'] ?: '-'); ?></td>
                                    <td>
                                        <?php
                                        $roleBadges = [
                                            'Customer' => 'secondary',
                                            'Administrator' => 'danger',
                                            'Sales Staff' => 'info',
                                            'Manager' => 'success'
                                        ];
                                        $badge = $roleBadges[$user['role_name']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $badge; ?>">
                                            <?php echo htmlspecialchars($user['role_name']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($user['user_id'] != getUserId()): ?>
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="if(confirm('Delete this user?')) window.location.href='index.php?page=admin/users&action=delete&id=<?php echo $user['user_id']; ?>'">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
        </main>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php?page=admin/users&action=create" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" required minlength="3">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role_id" required>
                                <option value="">Select Role</option>
                                <option value="1">Customer</option>
                                <option value="2">Administrator</option>
                                <option value="3">Sales Staff</option>
                                <option value="4">Manager</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="index.php?page=admin/users&action=update-role" method="POST">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Change Role *</label>
                            <select class="form-select" name="role_id" id="edit_role_id" required>
                                <option value="1">Customer</option>
                                <option value="2">Administrator</option>
                                <option value="3">Sales Staff</option>
                                <option value="4">Manager</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script src="/auntjoys_app/assets/js/sidebar.js"></script>
    <script>
        function editUser(user) {
            document.getElementById('edit_user_id').value = user.user_id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            
            // Map role_name to role_id
            const roleMap = {
                'Customer': 1,
                'Administrator': 2,
                'Sales Staff': 3,
                'Manager': 4
            };
            document.getElementById('edit_role_id').value = roleMap[user.role_name] || 1;
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }
    </script>
</body>
</html>
