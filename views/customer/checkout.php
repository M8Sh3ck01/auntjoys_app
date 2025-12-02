<?php
// Assumes the controller has already:
// - enforced requireLogin()
// - redirected if the cart is empty
// - loaded $user, $cart, and $cartTotal
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Aunt Joy's Restaurant</title>
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
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4"><i class="fas fa-credit-card"></i> Checkout</h2>

        <?php
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
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="index.php?page=checkout&action=submit" method="POST" id="checkoutForm">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contact Number *</label>
                                <input type="tel" class="form-control" name="contact_number" 
                                       value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" 
                                       placeholder="e.g., 0999123456" required>
                                <small class="form-text text-muted">We'll use this to contact you about your delivery</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Delivery Address in Mzuzu *</label>
                                <textarea class="form-control" name="delivery_address" rows="3" 
                                          placeholder="Enter your complete address in Mzuzu" required></textarea>
                                <small class="form-text text-muted">Include street name, area, and any landmarks</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Additional Instructions (Optional)</label>
                                <textarea class="form-control" name="notes" rows="2" 
                                          placeholder="Any special instructions for delivery?"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle"></i> Place Order
                                </button>
                                <a href="index.php?page=cart" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Cart
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <?php foreach ($cart as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?> × MWK <?php echo number_format($item['price'], 2); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong>MWK <?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>MWK <?php echo number_format($cartTotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Delivery:</span>
                            <span class="text-success">FREE</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong class="h5">Total:</strong>
                            <strong class="h5 text-primary">MWK <?php echo number_format($cartTotal, 2); ?></strong>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                Payment on delivery. Cash only.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const address = document.querySelector('[name="delivery_address"]').value.trim();
            const phone = document.querySelector('[name="contact_number"]').value.trim();
            
            if (address.length < 10) {
                e.preventDefault();
                alert('Please provide a complete delivery address');
                return false;
            }
            
            if (phone.length < 10) {
                e.preventDefault();
                alert('Please provide a valid contact number');
                return false;
            }
        });
    </script>
</body>
</html>
