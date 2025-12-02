<?php
// Assumes the controller has already:
// - enforced requireLogin()
// - loaded the current user into $user

$pageTitle = "My Profile";
$activePage = 'profile';
ob_start();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-circle"></i> My Profile</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php 
                            echo htmlspecialchars($_SESSION['success_message']); 
                            unset($_SESSION['success_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php 
                            echo htmlspecialchars($_SESSION['error_message']); 
                            unset($_SESSION['error_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Account Information</h5>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Username</label>
                                <p class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Email</label>
                                <p class="fw-bold"><?php echo htmlspecialchars($user['email'] ?? 'Not set'); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Phone Number</label>
                                <p class="fw-bold"><?php echo htmlspecialchars($user['phone_number'] ?? 'Not set'); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Account Type</label>
                                <p class="fw-bold"><span class="badge bg-success">Customer</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="?action=customer&page=edit_profile" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <a href="?action=customer&page=change_password" class="btn btn-warning">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/customer_layout.php';
?>
