<?php
require_once __DIR__ . '/../../includes/cart.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Aunt Joy\'s Restaurant'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Design tokens and theme -->
    <link rel="stylesheet" href="/auntjoys_app/assets/css/tokens.css">
    <link rel="stylesheet" href="/auntjoys_app/assets/css/theme.css">
    <style>
        /* Offset for fixed navbar on customer pages */
        body.d-flex.min-vh-100 {
            padding-top: 72px; /* approximate navbar height */
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
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
                        <a class="nav-link <?php echo ($activePage ?? '') === 'menu' ? 'active' : ''; ?>" 
                           href="index.php?page=menu">Menu</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($activePage ?? '') === 'cart' ? 'active' : ''; ?>" 
                               href="index.php?page=cart">
                                <i class="fas fa-shopping-cart"></i> Cart 
                                <?php if (getCartCount() > 0): ?>
                                    <span class="badge bg-danger"><?php echo getCartCount(); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($activePage ?? '') === 'orders' ? 'active' : ''; ?>" 
                               href="index.php?page=my-orders">My Orders</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo ($activePage ?? '') === 'profile' ? 'active' : ''; ?>" 
                               href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars(getUsername()); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?action=customer&page=profile">
                                    <i class="fas fa-user-circle"></i> My Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?page=logout"
                                       data-confirm="Log out from your account?"
                                       data-confirm-title="Log Out"
                                       data-confirm-ok="Log Out"
                                       data-confirm-cancel="Stay Signed In"
                                       data-confirm-variant="danger">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-fill">
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Aunt Joy's Restaurant - Mzuzu</p>
            <p class="small">Delicious meals delivered to your door</p>
        </div>
    </footer>

    <?php include __DIR__ . '/../partials/screen_loader.php'; ?>
    <?php include __DIR__ . '/../partials/confirm_dialog.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script src="/auntjoys_app/assets/js/screen-loader.js"></script>
    <script src="/auntjoys_app/assets/js/confirm-dialog.js"></script>
</body>
</html>
