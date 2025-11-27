<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized - Aunt Joy's Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Design tokens and theme -->
    <link rel="stylesheet" href="/auntjoys_app/assets/css/tokens.css">
    <link rel="stylesheet" href="/auntjoys_app/assets/css/theme.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 text-center">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <i class="fas fa-ban text-danger" style="font-size: 80px;"></i>
                        <h1 class="mt-4">Access Denied</h1>
                        <p class="text-muted mb-4">You do not have permission to access this page.</p>
                        
                        <?php
                        if (isset($_SESSION['error'])) {
                            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
                            unset($_SESSION['error']);
                        }
                        ?>
                        
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Go to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
</body>
</html>
