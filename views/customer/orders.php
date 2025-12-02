<?php
// Assumes the controller has already prepared:
// - $orderModel (Order model instance)
// - $orders for the current user

$pageTitle = 'My Orders';
$activePage = 'orders';
ob_start();
?>

<div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-receipt"></i> My Orders</h2>

        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show">
                    ' . htmlspecialchars($_SESSION['success']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            unset($_SESSION['success']);
        }
        ?>

        <?php if (empty($orders)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                    <a href="index.php?page=menu" class="btn btn-primary">
                        <i class="fas fa-utensils"></i> Browse Menu
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <strong>Order #<?php echo $order['order_id']; ?></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?>
                                </small>
                            </div>
                            <div class="col-md-3">
                                <?php
                                $statusColors = [
                                    'Pending' => 'warning',
                                    'Preparing' => 'info',
                                    'Out for Delivery' => 'primary',
                                    'Delivered' => 'success',
                                    'Cancelled' => 'danger'
                                ];
                                $statusColor = $statusColors[$order['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $statusColor; ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </div>
                            <div class="col-md-3 text-end">
                                <strong class="text-primary">MWK <?php echo number_format($order['total_amount'], 2); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $orderDetails = $orderModel->findById($order['order_id']);
                        if ($orderDetails && isset($orderDetails['items'])):
                        ?>
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>Order Items:</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($orderDetails['items'] as $item): ?>
                                            <li class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['image_path']): ?>
                                                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                             alt="<?php echo htmlspecialchars($item['meal_name']); ?>" 
                                                             style="width: 50px; height: 50px; object-fit: cover;" 
                                                             class="rounded me-3">
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($item['meal_name']); ?></strong><br>
                                                        <small class="text-muted">
                                                            Qty: <?php echo $item['quantity']; ?> × 
                                                            MWK <?php echo number_format($item['unit_price'], 2); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6>Delivery Details:</h6>
                                    <p class="mb-1">
                                        <i class="fas fa-map-marker-alt text-muted"></i> 
                                        <?php echo htmlspecialchars($order['delivery_address']); ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-phone text-muted"></i> 
                                        <?php echo htmlspecialchars($order['contact_number']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/customer_layout.php';
?>
