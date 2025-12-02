<?php
// Assumes the controller has already prepared:
// $cart and $cartTotal for the current user

$pageTitle = 'Shopping Cart';
$activePage = 'cart';
ob_start();
?>

<div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Shopping Cart</h2>

        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show">
                    ' . htmlspecialchars($_SESSION['success']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            unset($_SESSION['success']);
        }
        ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php if (isCartEmpty()): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Your cart is empty. 
                                <a href="index.php?page=menu" class="alert-link">Browse our menu</a> to add items.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($item['image_path']): ?>
                                                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                                 style="width: 60px; height: 60px; object-fit: cover;" 
                                                                 class="rounded me-3">
                                                        <?php endif; ?>
                                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                    </div>
                                                </td>
                                                <td>MWK <?php echo number_format($item['price'], 2); ?></td>
                                                <td>
                                                    <form action="index.php?page=cart&action=update" method="POST" class="d-inline">
                                                        <input type="hidden" name="meal_id" value="<?php echo $item['meal_id']; ?>">
                                                        <div class="input-group" style="max-width: 120px;">
                                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                                   min="1" class="form-control form-control-sm" 
                                                                   onchange="this.form.submit()">
                                                        </div>
                                                    </form>
                                                </td>
                                                <td><strong>MWK <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                                                <td>
                                                    <a href="index.php?page=cart&action=remove&id=<?php echo $item['meal_id']; ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       data-confirm="Remove this item?"
                                                       data-confirm-title="Remove Item"
                                                       data-confirm-ok="Remove"
                                                       data-confirm-cancel="Cancel"
                                                       data-confirm-variant="danger">
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

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items:</span>
                            <span><?php echo getCartCount(); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary">MWK <?php echo number_format($cartTotal, 2); ?></strong>
                        </div>
                        <?php if (!isCartEmpty()): ?>
                            <a href="index.php?page=checkout" class="btn btn-primary w-100">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                            <a href="index.php?page=menu" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        <?php else: ?>
                            <a href="index.php?page=menu" class="btn btn-primary w-100">
                                <i class="fas fa-utensils"></i> Browse Menu
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/customer_layout.php';
?>
