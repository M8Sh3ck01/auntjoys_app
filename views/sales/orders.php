<?php
require_once __DIR__ . '/../../models/Order.php';

$orderModel = new Order();

// Filter by status
$status_filter = $_GET['status'] ?? null;
$orders = $orderModel->getAll($status_filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Sales Staff</title>
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
            <a class="navbar-brand ms-3" href="#">
                <i class="fas fa-clipboard-list"></i> Sales Panel
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
                <p class="sidebar-tagline">Sales Staff</p>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=sales/orders">
                        <i class="fas fa-shopping-bag"></i> <span>Orders</span>
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
                        <p class="user-profile-role">Sales Staff</p>
                    </div>
                </div>
                <a href="index.php?page=logout" class="btn btn-outline-light btn-sm w-100 mt-3">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Main content -->
        <main class="admin-content col-md-10 px-md-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-shopping-bag"></i> Order Management</h2>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
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

        <!-- Status Filter Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?php echo !$status_filter ? 'active' : ''; ?>" 
                   href="index.php?page=sales/orders">
                    All Orders (<?php echo count($orderModel->getAll()); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $status_filter === 'Pending' ? 'active' : ''; ?>" 
                   href="index.php?page=sales/orders&status=Pending">
                    <span class="badge bg-warning text-dark">Pending</span> 
                    (<?php echo count($orderModel->getAll('Pending')); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $status_filter === 'Preparing' ? 'active' : ''; ?>" 
                   href="index.php?page=sales/orders&status=Preparing">
                    <span class="badge bg-info">Preparing</span> 
                    (<?php echo count($orderModel->getAll('Preparing')); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $status_filter === 'Out for Delivery' ? 'active' : ''; ?>" 
                   href="index.php?page=sales/orders&status=Out for Delivery">
                    <span class="badge bg-primary">Out for Delivery</span> 
                    (<?php echo count($orderModel->getAll('Out for Delivery')); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $status_filter === 'Delivered' ? 'active' : ''; ?>" 
                   href="index.php?page=sales/orders&status=Delivered">
                    <span class="badge bg-success">Delivered</span> 
                    (<?php echo count($orderModel->getAll('Delivered')); ?>)
                </a>
            </li>
        </ul>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No orders found.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $orderDetails = $orderModel->findById($order['order_id']);
                    $statusColors = [
                        'Pending' => 'warning',
                        'Preparing' => 'info',
                        'Out for Delivery' => 'primary',
                        'Delivered' => 'success',
                        'Cancelled' => 'danger'
                    ];
                    $statusColor = $statusColors[$order['status']] ?? 'secondary';
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-<?php echo $statusColor; ?> text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Order #<?php echo $order['order_id']; ?></strong>
                                    <span class="badge bg-light text-dark">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['username']); ?>
                                </h6>
                                
                                <p class="mb-2">
                                    <i class="fas fa-calendar text-muted"></i> 
                                    <small><?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></small>
                                </p>

                                <hr>

                                <h6>Order Items:</h6>
                                <ul class="list-unstyled small">
                                    <?php if ($orderDetails && isset($orderDetails['items'])): ?>
                                        <?php foreach ($orderDetails['items'] as $item): ?>
                                            <li>
                                                <i class="fas fa-circle" style="font-size: 6px;"></i> 
                                                <?php echo htmlspecialchars($item['meal_name']); ?> 
                                                (×<?php echo $item['quantity']; ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>

                                <hr>

                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt text-danger"></i> 
                                    <strong>Delivery:</strong><br>
                                    <small><?php echo htmlspecialchars($order['delivery_address']); ?></small>
                                </p>

                                <p class="mb-2">
                                    <i class="fas fa-phone text-success"></i> 
                                    <strong>Contact:</strong><br>
                                    <small><?php echo htmlspecialchars($order['contact_number']); ?></small>
                                </p>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-primary">MWK <?php echo number_format($order['total_amount'], 2); ?></strong>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#statusModal<?php echo $order['order_id']; ?>">
                                        <i class="fas fa-edit"></i> Update Status
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Modal -->
                    <div class="modal fade" id="statusModal<?php echo $order['order_id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="index.php?page=sales/orders&action=update-status" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Order #<?php echo $order['order_id']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Current Status: 
                                                <span class="badge bg-<?php echo $statusColor; ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Update to:</label>
                                            <select class="form-select" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>
                                                    Pending
                                                </option>
                                                <option value="Preparing" <?php echo $order['status'] === 'Preparing' ? 'selected' : ''; ?>>
                                                    Preparing
                                                </option>
                                                <option value="Out for Delivery" <?php echo $order['status'] === 'Out for Delivery' ? 'selected' : ''; ?>>
                                                    Out for Delivery
                                                </option>
                                                <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>
                                                    Delivered
                                                </option>
                                                <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>
                                                    Cancelled
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script src="/auntjoys_app/assets/js/sidebar.js"></script>
</body>
</html>
