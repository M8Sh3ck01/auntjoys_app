<?php
// Assumes the controller has already prepared:
// - $stats and $bestSellers for the manager dashboard
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
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
                <i class="fas fa-chart-line"></i> Manager Panel
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
                <p class="sidebar-tagline">Manager</p>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=manager/dashboard">
                        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=manager/reports">
                        <i class="fas fa-chart-bar"></i> <span>Reports</span>
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
                        <p class="user-profile-role">Manager</p>
                    </div>
                </div>
                <a href="index.php?page=logout" class="btn btn-outline-light btn-sm w-100 mt-3">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Main content -->
        <main class="admin-content col-md-10 px-md-4">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h2><?php echo $stats['total_orders'] ?? 0; ?></h2>
                        <small>All time</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2>MWK <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h2>
                        <small>All time</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Avg Order Value</h5>
                        <h2>MWK <?php echo number_format($stats['avg_order_value'] ?? 0, 2); ?></h2>
                        <small>Per order</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-trophy"></i> Top 5 Best Sellers</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($bestSellers)): ?>
                            <p class="text-muted">No sales data yet</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Meal</th>
                                            <th>Sold</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $rank = 1;
                                        foreach ($bestSellers as $item): 
                                        ?>
                                            <tr>
                                                <td><?php echo $rank++; ?></td>
                                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                <td><?php echo $item['total_sold']; ?></td>
                                                <td>MWK <?php echo number_format($item['revenue'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-download"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="index.php?page=manager/reports" class="btn btn-lg btn-success">
                                <i class="fas fa-chart-bar"></i> View Detailed Reports
                            </a>
                            <a href="index.php?page=manager/reports&action=export-pdf" class="btn btn-lg btn-danger" target="_blank">
                                <i class="fas fa-file-pdf"></i> Export Full Report (PDF)
                            </a>
                            <a href="index.php?page=manager/reports&action=export-excel" class="btn btn-lg btn-primary">
                                <i class="fas fa-file-excel"></i> Export Full Report (Excel)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/auntjoys_app/assets/js/button-handler.js"></script>
    <script src="/auntjoys_app/assets/js/sidebar.js"></script>
</body>
</html>
