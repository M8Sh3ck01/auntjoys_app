<?php
require_once __DIR__ . '/../../includes/cart.php';

$cart = getCart();
$cartTotal = getCartTotal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Aunt Joy's Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Design tokens and theme -->
    <link rel="stylesheet" href="/auntjoys_app/assets/css/tokens.css">
    <link rel="stylesheet" href="/auntjoys_app/assets/css/theme.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-utensils"></i> Aunt Joy's Restaurant
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=menu">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=cart">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if (getCartCount() > 0): ?>
                                <span class="badge bg-danger"><?php echo getCartCount(); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=my-orders">My Orders</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars(getUsername()); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?page=logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
                                                       onclick="return confirm('Remove this item?')">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
</body>
</html>
