<?php
// Assumes the controller has already prepared:
// $meals, $categories, $category_filter, $search,
// $page_num, $items_per_page, $total_items, $total_pages

// Prepare top vs. more categories for chip navigation
$topLimit = 8;
$topCategories = array_slice($categories, 0, $topLimit);
$moreCategories = array_slice($categories, $topLimit);

$pageTitle = "Menu";
$activePage = 'menu';
ob_start();
?>

<link rel="stylesheet" href="assets/css/style.css">
<style>

        /* Mobile improvements for category chips */
        .chip-row {
            overflow-x: auto;
            overflow-y: visible;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* Firefox */
            padding-bottom: 0.5rem;
        }
        .chip-row::-webkit-scrollbar { 
            display: none; 
        }
        .chip-row .btn {
            white-space: nowrap;
            flex-shrink: 0;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
        
        /* Pagination styling */
        .pagination {
            gap: 0.25rem;
        }
        
        .pagination .page-link {
            border-color: var(--color-brand-200);
            color: var(--color-primary);
            transition: all 0.3s ease;
        }
        
        .pagination .page-link:hover {
            background-color: var(--color-brand-50);
            border-color: var(--color-primary);
            color: var(--color-primary);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }
        
        .pagination .page-item.disabled .page-link {
            color: var(--text-muted);
            border-color: var(--color-brand-100);
        }
        
        /* Sidebar category filter styling */
        .list-group-item {
            border-color: var(--color-brand-100);
            color: var(--text-body);
            transition: all 0.3s ease;
        }
        
        .list-group-item:hover {
            background-color: var(--color-brand-50);
            border-color: var(--color-primary);
            color: var(--color-primary);
        }
        
        .list-group-item.active {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }
        
        .list-group-item.active .badge {
            background-color: rgba(255, 255, 255, 0.3) !important;
        }
        
        /* Offcanvas styling */
        .offcanvas {
            background-color: var(--bg-body);
        }
        
        .offcanvas-header {
            border-bottom: 2px solid var(--color-brand-100);
        }
        
        .offcanvas-title {
            color: var(--color-primary);
            font-weight: 600;
        }
        
        @media (max-width: 767.98px) {
            .card .card-body { padding: 0.75rem 1rem; }
            .chip-row {
                margin-bottom: 0.5rem;
            }
            .chip-row .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
                min-width: auto;
            }
            .pagination {
                font-size: 0.875rem;
            }
        }
    @media (max-width: 767.98px) {
        .card .card-body { padding: 0.75rem 1rem; }
        .chip-row {
            margin-bottom: 0.5rem;
        }
        .chip-row .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            min-width: auto;
        }
        .pagination {
            font-size: 0.875rem;
        }
    }
</style>

<?php if (!isLoggedIn()): ?>
    <!-- Hero Section -->
    <div class="hero">
        <div class="container hero-content">
            <h1 class="display-4 fw-bold">Welcome to Aunt Joy's</h1>
            <p class="lead">Delicious meals delivered to your door in Mzuzu</p>
        </div>
    </div>
    <?php endif; ?>

<!-- Menu Section -->
<div class="container my-5">
        <?php
        // Display messages
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

        <div class="row">
            <div class="col-md-3 d-none d-md-block">
                <!-- Search First -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-search"></i> Search
                        </h5>
                        <form action="index.php" method="GET">
                            <input type="hidden" name="page" value="menu">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search meals..." 
                                       name="search" value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-filter"></i> Filter by Category
                        </h5>
                        <p class="text-muted small mb-3">
                            Use the chips above the menu to filter quickly. Need full list or counts?
                        </p>
                        <button class="btn btn-outline-primary w-100" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#categoryOffcanvas">
                            <i class="fas fa-sliders-h"></i> Open Filters
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-9">
                <div class="d-md-none mb-3">
                    <form action="index.php" method="GET" class="mb-2">
                        <input type="hidden" name="page" value="menu">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search meals..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <!-- Category chips (horizontal, scrollable) -->
                <div class="mb-3">
<div class="chip-row d-flex gap-2 flex-nowrap pb-2 border-bottom">
                        <?php 
                        // All chip
                        $allActive = !$category_filter ? 'btn-primary' : 'btn-outline-primary';
                        ?>
                        <a href="index.php?page=menu<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="btn btn-sm rounded-pill <?php echo $allActive; ?>">All</a>

                        <?php foreach ($topCategories as $cat): 
                            $isActive = ($category_filter == $cat['category_id']);
                            $btnClass = $isActive ? 'btn-primary' : 'btn-outline-primary';
                        ?>
                            <a href="index.php?page=menu&category=<?php echo $cat['category_id']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                               class="btn btn-sm rounded-pill <?php echo $btnClass; ?>">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </a>
                        <?php endforeach; ?>

                        <?php if (count($moreCategories) > 0): ?>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#categoryOffcanvas">
                            More
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!isLoggedIn()): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-sign-in-alt"></i> 
                    Please <a href="index.php?page=login" class="alert-link">login</a> or 
                    <a href="index.php?page=register" class="alert-link">register</a> to place orders.
                </div>
                <?php endif; ?>

                <?php if (empty($meals)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        No meals found. <?php echo $search ? 'Try a different search term.' : 'Check back later!'; ?>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($meals as $meal): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100" style="transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                                    <!-- Clickable image area for details -->
                                    <div onclick="showMealDetail(<?php echo $meal['meal_id']; ?>)" style="cursor: pointer;">
                                        <?php if ($meal['image_path']): ?>
                                            <img src="<?php echo htmlspecialchars($meal['image_path']); ?>" 
                                                 class="card-img-top" alt="<?php echo htmlspecialchars($meal['name']); ?>" 
                                                 style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary d-flex align-items-center justify-content-center" 
                                                 style="height: 200px;">
                                                <i class="fas fa-utensils fa-3x text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <!-- Clickable title for details -->
                                        <h5 class="card-title" onclick="showMealDetail(<?php echo $meal['meal_id']; ?>)" style="cursor: pointer;" title="Click to view details"><?php echo htmlspecialchars($meal['name']); ?></h5>
                                        <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($meal['description'] ?: 'Delicious meal from Aunt Joy\'s', 0, 50)); ?>...</p>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="h5 mb-0 text-primary">MWK <?php echo number_format($meal['price'], 2); ?></span>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($meal['category_name']); ?></span>
                                            </div>
                                            <?php if (isLoggedIn()): ?>
                                                <form action="index.php?page=cart&action=add" method="POST" onclick="event.stopPropagation();">
                                                    <input type="hidden" name="meal_id" value="<?php echo $meal['meal_id']; ?>">
                                                    <div class="input-group">
                                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="max-width: 80px;">
                                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                                        </button>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Menu pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Previous -->
                                <?php if ($page_num > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=menu<?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&p=<?php echo $page_num - 1; ?>">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-left"></i> Previous</span>
                                    </li>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $start_page = max(1, $page_num - 2);
                                $end_page = min($total_pages, $page_num + 2);
                                
                                if ($start_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=menu<?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&p=1">1</a>
                                    </li>
                                    <?php if ($start_page > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <li class="page-item <?php echo $i == $page_num ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=menu<?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($end_page < $total_pages): ?>
                                    <?php if ($end_page < $total_pages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=menu<?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&p=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next -->
                                <?php if ($page_num < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=menu<?php echo $category_filter ? '&category=' . $category_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>&p=<?php echo $page_num + 1; ?>">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">Next <i class="fas fa-chevron-right"></i></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Offcanvas: Full Category Filter -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="categoryOffcanvas" aria-labelledby="categoryOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="categoryOffcanvasLabel">
                    <i class="fas fa-filter"></i> Filter by Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="list-group">
                    <a href="index.php?page=menu" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo !$category_filter ? 'active' : ''; ?>">
                        <span><i class="fas fa-th-large"></i> All Items</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $total_items; ?></span>
                    </a>
                    <?php foreach ($categories as $cat): 
                        $cat_count = $mealModel->countAvailable($cat['category_id']);
                    ?>
                        <a href="index.php?page=menu&category=<?php echo $cat['category_id']; ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $category_filter == $cat['category_id'] ? 'active' : ''; ?>">
                            <span>
                                <i class="fas fa-utensils"></i> 
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </span>
                            <span class="badge <?php echo $category_filter == $cat['category_id'] ? 'bg-light text-dark' : 'bg-secondary'; ?> rounded-pill">
                                <?php echo $cat_count; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/meal_detail_modal.php'; ?>
<script src="/auntjoys_app/assets/js/skeleton-loader.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/customer_layout.php';
?>
