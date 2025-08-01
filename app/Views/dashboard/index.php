<?php
// Define APP_PATH if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

// Ensure we have the required variables
if (!isset($user)) {
    $user = getCurrentUser() ?? [];
}

// Set default values for missing variables
$totalOrders = $totalOrders ?? 0;
$pendingOrders = $pendingOrders ?? 0;
$wishlistCount = $wishlistCount ?? 0;
$totalSpent = $totalSpent ?? 0;
$recentOrders = $recentOrders ?? [];

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
                            <a class="nav-link active text-success" href="/dashboard">
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
                            <a class="nav-link text-dark" href="/dashboard/profile">
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

        <!-- Main Dashboard Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-success">Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'User') ?>!</h1>
            </div>

            <!-- Dashboard Stats -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-bag fa-2x text-success mb-2"></i>
                            <h5 class="card-title"><?= $totalOrders ?></h5>
                            <p class="card-text text-muted">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h5 class="card-title"><?= $pendingOrders ?></h5>
                            <p class="card-text text-muted">Pending Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                            <h5 class="card-title"><?= $wishlistCount ?></h5>
                            <p class="card-text text-muted">Wishlist Items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-rupee-sign fa-2x text-success mb-2"></i>
                            <h5 class="card-title">₨<?= number_format($totalSpent) ?></h5>
                            <p class="card-text text-muted">Total Spent</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentOrders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?= $order['id'] ?></td>
                                            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info') ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td>₨<?= number_format($order['total_amount']) ?></td>
                                            <td>
                                                <a href="/dashboard/order-detail/<?= $order['id'] ?>" class="btn btn-sm btn-outline-success">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders yet</h5>
                            <p class="text-muted">Start shopping to see your orders here</p>
                            <a href="/products" class="btn btn-success">Browse Products</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar-dashboard {
    min-height: calc(100vh - 76px);
}
.sidebar-dashboard .nav-link {
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin: 0.125rem 0.5rem;
}
.sidebar-dashboard .nav-link:hover {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754 !important;
}
.sidebar-dashboard .nav-link.active {
    background-color: #198754;
    color: white !important;
}
</style>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
