<?php
// Define APP_PATH if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

// Ensure we have the required variables
if (!isset($user)) {
    $user = getCurrentUser() ?? [];
}

// Set default user stats if not provided
if (!isset($userStats)) {
    $userStats = [
        'total_orders' => 0,
        'total_spent' => 0,
        'wishlist_count' => 0,
        'reviews_count' => 0
    ];
}

include APP_PATH . '/Views/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Dashboard Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="bg-light sidebar-dashboard">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/dashboard/orders">
                                <i class="fas fa-shopping-bag me-2"></i>
                                My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-success" href="/dashboard/profile">
                                <i class="fas fa-user me-2"></i>
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/dashboard/addresses">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Addresses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/wishlist">
                                <i class="fas fa-heart me-2"></i>
                                Wishlist
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-success">My Profile</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="/dashboard/profile/update" method="POST" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?= htmlspecialchars($user['phone']) ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                               value="<?= $user['date_of_birth'] ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" <?= $user['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                                            <option value="female" <?= $user['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                                            <option value="other" <?= $user['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3" 
                                              placeholder="Tell us about yourself..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form action="/dashboard/profile/change-password" method="POST">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                               minlength="8" required>
                                        <div class="form-text">Password must be at least 8 characters long</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                               minlength="8" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Profile Sidebar -->
                <div class="col-lg-4">
                    <!-- Profile Picture -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Profile Picture</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img src="<?= $user['avatar'] ?? '/assets/images/default-avatar.png' ?>" 
                                     alt="Profile Picture" class="rounded-circle" 
                                     style="width: 120px; height: 120px; object-fit: cover;" id="profilePreview">
                            </div>
                            <form action="/dashboard/profile/upload-avatar" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="avatar" name="avatar" 
                                           accept="image/*" onchange="previewImage(this)">
                                </div>
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-upload me-2"></i>Upload Picture
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Account Statistics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Account Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Member Since:</span>
                                <span><?= date('M Y', strtotime($user['created_at'])) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Orders:</span>
                                <span><?= $userStats['total_orders'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Spent:</span>
                                <span>₨<?= number_format($userStats['total_spent']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Wishlist Items:</span>
                                <span><?= $userStats['wishlist_count'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Reviews Written:</span>
                                <span><?= $userStats['reviews_count'] ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Account Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" 
                                                                               <?= ($user['email_notifications'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="emailNotifications">
                                    Email Notifications
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="smsNotifications" 
                                                                               <?= ($user['sms_notifications'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="smsNotifications">
                                    SMS Notifications
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="marketingEmails" 
                                                                               <?= ($user['marketing_emails'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="marketingEmails">
                                    Marketing Emails
                                </label>
                            </div>
                            <button class="btn btn-outline-success btn-sm w-100" onclick="updateNotificationSettings()">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateNotificationSettings() {
    const settings = {
        email_notifications: document.getElementById('emailNotifications').checked,
        sms_notifications: document.getElementById('smsNotifications').checked,
        marketing_emails: document.getElementById('marketingEmails').checked
    };

    fetch('/dashboard/profile/notification-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notification settings updated successfully!');
        } else {
            alert('Error updating settings');
        }
    });
}

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
