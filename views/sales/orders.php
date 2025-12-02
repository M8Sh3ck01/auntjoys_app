<?php
// Assumes the controller has already prepared:
// - $orderModel (Order model instance)
// - $status_filter and $orders
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
            <a class="navbar-brand" href="index.php?page=sales/orders">
                <i class="fas fa-clipboard-list"></i> Sales Panel
            </a>

            <div class="d-flex align-items-center ms-auto gap-2">
                <button class="btn btn-sm btn-outline-light" onclick="location.reload()">
                    <i class="fas fa-sync"></i> Refresh
                </button>

                <a href="index.php?page=sales/orders" class="btn btn-sm btn-light text-primary">
                    <i class="fas fa-shopping-bag"></i> Orders
                </a>

                <a href="index.php?page=logout" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar temporarily disabled on this page to avoid overlay issues with modals -->

    <!-- Main content -->
    <main class="admin-content px-3 px-md-4 pt-3 pt-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3 mb-md-4">
            <h2 class="mb-0"><i class="fas fa-shopping-bag me-2"></i> Order Management</h2>
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
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1">
                                    <strong>Order #<?php echo $order['order_id']; ?></strong>
                                    <span class="badge bg-light text-dark mt-1 mt-sm-0">
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
                                                <i class="fas fa-circle fa-xs me-1"></i>
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

                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                                    <strong class="text-primary">MWK <?php echo number_format($order['total_amount'], 2); ?></strong>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#statusModal<?php echo $order['order_id']; ?>">
                                        <i class="fas fa-edit me-1"></i> Update Status
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Modal -->
                    <div class="modal fade" id="statusModal<?php echo $order['order_id']; ?>" tabindex="-1" aria-labelledby="statusModalLabel<?php echo $order['order_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <form action="index.php?page=sales/orders&action=update-status" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">

                                    <div class="modal-header border-0 pb-0">
                                        <div>
                                            <h5 class="modal-title" id="statusModalLabel<?php echo $order['order_id']; ?>">
                                                Update Order #<?php echo $order['order_id']; ?>
                                            </h5>
                                            <p class="mb-0 small text-muted">
                                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($order['username']); ?>
                                                &middot;
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?>
                                            </p>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body pt-2">
                                        <!-- Current status -->
                                        <div class="mb-3">
                                            <span class="text-muted small d-block mb-1">Current status</span>
                                            <span class="badge bg-<?php echo $statusColor; ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </div>

                                        <!-- New status selection as vertical button group -->
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Update status to</label>
                                            <div class="btn-group-vertical w-100" role="group" aria-label="Update order status">
                                                <?php
                                                $statusOptions = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];
                                                foreach ($statusOptions as $option):
                                                    $optionColor = $statusColors[$option] ?? 'secondary';
                                                    $isCurrent = $order['status'] === $option;
                                                    $optionId = 'status-' . $order['order_id'] . '-' . strtolower(str_replace(' ', '-', $option));
                                                ?>
                                                    <input
                                                        type="radio"
                                                        class="btn-check"
                                                        name="status"
                                                        id="<?php echo $optionId; ?>"
                                                        value="<?php echo htmlspecialchars($option); ?>"
                                                        <?php echo $isCurrent ? 'checked' : ''; ?>
                                                        required
                                                    >
                                                    <label class="btn btn-outline-<?php echo $optionColor; ?> d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1 mb-2" for="<?php echo $optionId; ?>">
                                                        <span><?php echo htmlspecialchars($option); ?></span>
                                                        <?php if ($isCurrent): ?>
                                                            <span class="badge bg-<?php echo $optionColor; ?> mt-1 mt-sm-0">Current</span>
                                                        <?php endif; ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <!-- Optional note -->
                                        <div class="mb-0">
                                            <label for="statusNote<?php echo $order['order_id']; ?>" class="form-label small text-muted">
                                                Optional note for kitchen / rider
                                            </label>
                                            <textarea
                                                class="form-control"
                                                id="statusNote<?php echo $order['order_id']; ?>"
                                                name="status_note"
                                                rows="2"
                                                placeholder="Add a short note (e.g. customer not answering, rider en route, etc.)"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Save changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script src="/auntjoys_app/assets/js/sidebar.js"></script>
</body>
</html>