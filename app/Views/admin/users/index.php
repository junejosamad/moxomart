<?php
/**
 * Admin Users Management View
 * Display and manage all users
 */

$pageTitle = 'Users Management';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - Admin | Moxo Mart</title>
    <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">
    <?php include '../app/Views/layouts/admin-sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Users Management</h1>
            <div class="admin-breadcrumb">
                <a href="/admin">Dashboard</a>
                <span>/</span>
                <span>Users</span>
            </div>
        </div>

        <div class="admin-main">
            <?php if ($flash = getFlash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= e($flash) ?>
                </div>
            <?php endif; ?>

            <!-- User Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($stats['total_users']) ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($stats['active_users']) ?></h3>
                        <p>Active Users</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($stats['new_users_today']) ?></h3>
                        <p>New Today</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($stats['verified_users']) ?></h3>
                        <p>Email Verified</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="role">Role:</label>
                        <select name="role" id="role">
                            <option value="">All Roles</option>
                            <option value="customer" <?= ($filters['role'] ?? '') == 'customer' ? 'selected' : '' ?>>Customer</option>
                            <option value="admin" <?= ($filters['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">All Statuses</option>
                            <option value="active" <?= ($filters['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="verified">Email:</label>
                        <select name="verified" id="verified">
                            <option value="">All</option>
                            <option value="1" <?= ($filters['verified'] ?? '') == '1' ? 'selected' : '' ?>>Verified</option>
                            <option value="0" <?= ($filters['verified'] ?? '') == '0' ? 'selected' : '' ?>>Unverified</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="search">Search:</label>
                        <input type="text" name="search" id="search" 
                               placeholder="Name, email..." 
                               value="<?= e($filters['search'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filter
                    </button>

                    <a href="/admin/users" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </form>
            </div>

            <!-- Users Table -->
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Last Login</th>
                            <th>Orders</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users['data'])): ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>No users found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users['data'] as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                            </div>
                                            <div class="user-details">
                                                <strong><?= e($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                                <small>ID: <?= $user['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="email-info">
                                            <?= e($user['email']) ?>
                                            <?php if ($user['email_verified']): ?>
                                                <i class="fas fa-check-circle text-success" title="Email Verified"></i>
                                            <?php else: ?>
                                                <i class="fas fa-exclamation-circle text-warning" title="Email Not Verified"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="role-badge role-<?= $user['role'] ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <strong><?= formatDate($user['created_at'], 'M j, Y') ?></strong>
                                            <small><?= formatDate($user['created_at'], 'g:i A') ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($user['last_login']): ?>
                                            <div class="date-info">
                                                <strong><?= formatDate($user['last_login'], 'M j, Y') ?></strong>
                                                <small><?= formatDate($user['last_login'], 'g:i A') ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Never</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="orders-info">
                                            <strong><?= number_format($user['order_count'] ?? 0) ?></strong>
                                            <small><?= formatPrice($user['total_spent'] ?? 0) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/admin/users/<?= $user['id'] ?>" 
                                               class="btn-icon" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <button class="btn-icon toggle-status" 
                                                    data-user-id="<?= $user['id'] ?>" 
                                                    data-current-status="<?= $user['is_active'] ? 'active' : 'inactive' ?>"
                                                    title="Toggle Status">
                                                <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                            </button>

                                            <?php if (!$user['email_verified']): ?>
                                                <button class="btn-icon resend-verification" 
                                                        data-user-id="<?= $user['id'] ?>" 
                                                        title="Resend Verification">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            <?php endif; ?>

                                            <?php if ($user['role'] !== 'admin'): ?>
                                                <button class="btn-icon delete-user" 
                                                        data-user-id="<?= $user['id'] ?>" 
                                                        title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($users['last_page'] > 1): ?>
                <div class="pagination">
                    <?php if ($users['current_page'] > 1): ?>
                        <a href="?page=<?= $users['current_page'] - 1 ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>" 
                           class="pagination-btn">
                            <i class="fas fa-chevron-left"></i>
                            Previous
                        </a>
                    <?php endif; ?>

                    <div class="pagination-info">
                        Page <?= $users['current_page'] ?> of <?= $users['last_page'] ?>
                        (<?= number_format($users['total']) ?> total users)
                    </div>

                    <?php if ($users['current_page'] < $users['last_page']): ?>
                        <a href="?page=<?= $users['current_page'] + 1 ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>" 
                           class="pagination-btn">
                            Next
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="confirm-title">Confirm Action</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="confirm-message">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer">
                <button id="confirm-yes" class="btn btn-danger">Yes</button>
                <button class="btn btn-secondary modal-close">Cancel</button>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/app.min.js') ?>"></script>
    <script>
        // Modal functionality
        const modal = document.getElementById('confirm-modal');
        const modalCloses = document.querySelectorAll('.modal-close');
        const confirmYes = document.getElementById('confirm-yes');
        const confirmTitle = document.getElementById('confirm-title');
        const confirmMessage = document.getElementById('confirm-message');

        modalCloses.forEach(close => {
            close.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        });

        // Toggle user status
        document.querySelectorAll('.toggle-status').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = btn.dataset.userId;
                const currentStatus = btn.dataset.currentStatus;
                const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                
                confirmTitle.textContent = 'Toggle User Status';
                confirmMessage.textContent = `Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this user?`;
                
                confirmYes.onclick = () => {
                    // Create form to toggle status
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/users/${userId}/status`;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '<?= generateCsrfToken() ?>';
                    
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = newStatus;
                    
                    form.appendChild(csrfInput);
                    form.appendChild(statusInput);
                    document.body.appendChild(form);
                    form.submit();
                };
                
                modal.style.display = 'block';
            });
        });

        // Delete user
        document.querySelectorAll('.delete-user').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = btn.dataset.userId;
                
                confirmTitle.textContent = 'Delete User';
                confirmMessage.textContent = 'Are you sure you want to delete this user? This action cannot be undone.';
                
                confirmYes.onclick = () => {
                    // Create form to delete user
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/users/${userId}/delete`;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = '<?= generateCsrfToken() ?>';
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                };
                
                modal.style.display = 'block';
            });
        });

        // Resend verification
        document.querySelectorAll('.resend-verification').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = btn.dataset.userId;
                
                // Create form to resend verification
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/users/${userId}/resend-verification`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = '<?= generateCsrfToken() ?>';
                
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            });
        });

        // Close modal on outside click
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
