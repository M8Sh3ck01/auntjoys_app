<?php
/**
 * Meal Details Modal
 * Include this at the bottom of menu.php before closing body tag
 * Shows full meal details and allows adding to cart
 */
?>

<!-- Meal Details Modal -->
<div class="modal fade" id="mealDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mealDetailTitle">Meal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Meal Image -->
                    <div class="col-md-5">
                        <div id="mealDetailImage" style="height: 250px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-utensils fa-3x text-muted"></i>
                        </div>
                    </div>
                    
                    <!-- Meal Info -->
                    <div class="col-md-7">
                        <div id="mealDetailContent">
                            <p class="text-muted">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if (isLoggedIn()): ?>
                    <form id="addToCartForm" action="index.php?page=cart&action=add" method="POST" class="w-100">
                        <input type="hidden" name="meal_id" id="detailMealId" value="">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="decreaseQty">−</button>
                            <input type="number" name="quantity" id="detailQuantity" value="1" min="1" class="form-control text-center" style="max-width: 100px;">
                            <button type="button" class="btn btn-outline-secondary" id="increaseQty">+</button>
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-muted w-100 text-center mb-0">
                        <i class="fas fa-sign-in-alt"></i> 
                        Please <a href="index.php?page=login" class="alert-link">login</a> to add items to cart.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    let currentMeal = null;

    // Load meal details via AJAX
    function showMealDetail(mealId) {
        if (typeof showScreenLoader === 'function') {
            showScreenLoader();
        }

        fetch(`index.php?action=get_meal_detail&meal_id=${mealId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentMeal = data.meal;
                    displayMealDetail(data.meal);
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('mealDetailModal'));
                    modal.show();
                } else {
                    alert('Could not load meal details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading meal details');
            })
            .finally(() => {
                if (typeof hideScreenLoader === 'function') {
                    hideScreenLoader();
                }
            });
    }

    // Display meal details in modal
    function displayMealDetail(meal) {
        // Update image
        const imageDiv = document.getElementById('mealDetailImage');
        if (meal.image_path) {
            imageDiv.innerHTML = `<img src="${meal.image_path}" alt="${meal.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
        } else {
            imageDiv.innerHTML = '<i class="fas fa-utensils fa-3x text-muted"></i>';
        }

        // Update title
        document.getElementById('mealDetailTitle').textContent = meal.name;

        // Update content
        const content = `
            <h5 class="mb-2">${meal.name}</h5>
            <p class="text-muted mb-3">${meal.category_name}</p>
            
            <p class="mb-3">
                <strong>Description:</strong><br>
                ${meal.description || 'No description available'}
            </p>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="h4 text-primary mb-0">MWK ${parseFloat(meal.price).toFixed(2)}</span>
                </div>
                <div>
                    ${meal.is_available ? 
                        '<span class="badge bg-success">Available</span>' : 
                        '<span class="badge bg-danger">Out of Stock</span>'
                    }
                </div>
            </div>
            
            ${!meal.is_available ? '<div class="alert alert-warning small">This item is currently out of stock.</div>' : ''}
        `;

        document.getElementById('mealDetailContent').innerHTML = content;

        // These elements exist only when the user is logged in
        const detailMealIdInput = document.getElementById('detailMealId');
        const detailQuantityInput = document.getElementById('detailQuantity');

        if (detailMealIdInput && detailQuantityInput) {
            detailMealIdInput.value = meal.meal_id;
            detailQuantityInput.value = 1;

            // Disable add to cart if out of stock
            if (!meal.is_available) {
                const addToCartForm = document.getElementById('addToCartForm');
                const submitButton = addToCartForm?.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                }
            }
        }
    }

    // Quantity controls
    document.getElementById('decreaseQty')?.addEventListener('click', function() {
        const qty = document.getElementById('detailQuantity');
        if (parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value) - 1;
        }
    });

    document.getElementById('increaseQty')?.addEventListener('click', function() {
        const qty = document.getElementById('detailQuantity');
        qty.value = parseInt(qty.value) + 1;
    });
</script>
