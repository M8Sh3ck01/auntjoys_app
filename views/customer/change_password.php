<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin(); // Customers only

$pageTitle = "Change Password";
$activePage = 'profile';
ob_start();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-key"></i> Change Password</h4>
                </div>
                <div class="card-body">
                    <form action="?action=update_password" method="POST" id="changePasswordForm">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" required>
                            <div class="form-text">Must be at least 6 characters</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" required>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Choose a strong password with letters, numbers, and symbols.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                            <a href="?action=customer&page=profile" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword.length < 6) {
        e.preventDefault();
        alert('New password must be at least 6 characters long');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('New password and confirmation do not match');
        return;
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/customer_layout.php';
?>
