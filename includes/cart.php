<?php
/**
 * Shopping Cart Helper
 * Session-based cart management
 */

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Add item to cart
 */
function addToCart($meal_id, $name, $price, $image_path, $quantity = 1) {
    if (isset($_SESSION['cart'][$meal_id])) {
        $_SESSION['cart'][$meal_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$meal_id] = [
            'meal_id' => $meal_id,
            'name' => $name,
            'price' => $price,
            'image_path' => $image_path,
            'quantity' => $quantity
        ];
    }
}

/**
 * Update cart item quantity
 */
function updateCartQuantity($meal_id, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($meal_id);
    } else if (isset($_SESSION['cart'][$meal_id])) {
        $_SESSION['cart'][$meal_id]['quantity'] = $quantity;
    }
}

/**
 * Remove item from cart
 */
function removeFromCart($meal_id) {
    if (isset($_SESSION['cart'][$meal_id])) {
        unset($_SESSION['cart'][$meal_id]);
    }
}

/**
 * Get cart items
 */
function getCart() {
    return $_SESSION['cart'] ?? [];
}

/**
 * Get cart count
 */
function getCartCount() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

/**
 * Get cart total
 */
function getCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

/**
 * Clear cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
}

/**
 * Check if cart is empty
 */
function isCartEmpty() {
    return empty($_SESSION['cart']);
}
?>
