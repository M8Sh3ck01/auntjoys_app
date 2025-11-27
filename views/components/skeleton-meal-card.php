<?php
/**
 * Skeleton Loader Component - Meal Card
 * Used to show loading state while meal data is being fetched
 * 
 * Usage:
 * <?php include 'views/components/skeleton-meal-card.php'; ?>
 */
?>

<div class="col-md-4 mb-4">
    <div class="card h-100 skeleton-card">
        <!-- Image skeleton -->
        <div class="skeleton skeleton-image"></div>
        
        <!-- Card body skeleton -->
        <div class="card-body d-flex flex-column">
            <!-- Title skeleton -->
            <div class="skeleton skeleton-title mb-2"></div>
            
            <!-- Description skeleton (2 lines) -->
            <div class="skeleton skeleton-text"></div>
            <div class="skeleton skeleton-text mb-3"></div>
            
            <div class="mt-auto">
                <!-- Price and badge skeleton -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="skeleton skeleton-price"></div>
                    <div class="skeleton skeleton-badge"></div>
                </div>
                
                <!-- Button skeleton -->
                <div class="skeleton skeleton-button"></div>
            </div>
        </div>
    </div>
</div>
