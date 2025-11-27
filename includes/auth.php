<?php
/**
 * Authentication and Role-Based Access Control (RBAC)
 * Include this file at the top of protected pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
}

/**
 * Require user to be logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to access this page";
        header('Location: /auntjoys_app/index.php?page=login');
        exit;
    }
}

/**
 * Require specific role(s)
 * @param array $allowed_roles - Array of role_ids that can access
 * Role IDs: 1=Customer, 2=Administrator, 3=Sales Staff, 4=Manager
 */
function requireRole($allowed_roles) {
    requireLogin();
    
    if (!in_array($_SESSION['role_id'], $allowed_roles)) {
        $_SESSION['error'] = "You do not have permission to access this page";
        header('Location: /auntjoys_app/index.php?page=unauthorized');
        exit;
    }
}

/**
 * Get current user's role ID
 */
function getUserRole() {
    return $_SESSION['role_id'] ?? null;
}

/**
 * Get current user's ID
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 */
function getUsername() {
    return $_SESSION['username'] ?? 'Guest';
}

/**
 * Check if user is Customer
 */
function isCustomer() {
    return getUserRole() === 1;
}

/**
 * Check if user is Administrator
 */
function isAdmin() {
    return getUserRole() === 2;
}

/**
 * Check if user is Sales Staff
 */
function isSales() {
    return getUserRole() === 3;
}

/**
 * Check if user is Manager
 */
function isManager() {
    return getUserRole() === 4;
}
?>
