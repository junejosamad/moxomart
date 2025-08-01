<?php
// Define APP_PATH if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

// Ensure we have the required variables
if (!isset($orders)) {
    $orders = [];
}

// Set default pagination variables if not provided
if (!isset($totalPages)) {
    $totalPages = 1;
}
if (!isset($currentPage)) {
    $currentPage = 1;
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
                            <a class="nav-link active text-success" href="/dashboard/orders">
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

        <!-- Orders Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-success">My Orders</h1>
            </div>

            <!-- Order Filters -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="orderFilter" id="all" autocomplete="off" checked>
                        <label class="btn btn-outline-success" for="all">All Orders</label>

                        <input type="radio" class="btn-check" name="orderFilter" id="pending" autocomplete="off">
                        <label class="btn btn-outline-warning" for="pending">Pending</label>

                        <input type="radio" class="btn-check" name="orderFilter" id="processing" autocomplete="off">
                        <label class="btn btn-outline-info" for="processing">Processing</label>

                        <input type="radio" class="btn-check" name="orderFilter" id="delivered" autocomplete="off">
                        <label class="btn btn-outline-success" for="delivered">Delivered</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search orders..." id="orderSearch">
                        <button class="btn btn-outline-success" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <?php 
                    // Ensure order is an array and has required fields
                    if (!is_array($order)) continue;
                    $orderId = $order['id'] ?? 'N/A';
                    $orderDate = $order['created_at'] ?? '';
                    $orderStatus = $order['status'] ?? 'pending';
                    $orderTotal = $order['total_amount'] ?? 0;
                    ?>
                    <div class="card mb-3 order-card" data-status="<?= htmlspecialchars($orderStatus) ?>">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Order #<?= htmlspecialchars($orderId) ?></h6>
                                <small class="text-muted">Placed on <?= $orderDate ? date('M d, Y', strtotime($orderDate)) : 'N/A' ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?= $orderStatus === 'delivered' ? 'success' : ($orderStatus === 'pending' ? 'warning' : 'info') ?> mb-2">
                                    <?= ucfirst($orderStatus) ?>
                                </span>
                                <div class="h6 mb-0">₨<?= number_format($orderTotal) ?></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <?php if (!empty($order['items']) && is_array($order['items'])): ?>
                                            <?php $firstItem = $order['items'][0] ?? []; ?>
                                            <img src="<?= $firstItem['image'] ?? '/assets/images/placeholder.jpg' ?>" 
                                                 alt="<?= htmlspecialchars($firstItem['name'] ?? 'Product') ?>" 
                                                 class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($firstItem['name'] ?? 'Product') ?></h6>
                                                <?php if (count($order['items']) > 1): ?>
                                                    <small class="text-muted">and <?= count($order['items']) - 1 ?> more item(s)</small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div>
                                                <h6 class="mb-1">Order Items</h6>
                                                <small class="text-muted">View details for item information</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="/dashboard/order-detail/<?= htmlspecialchars($orderId) ?>" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    <?php if ($orderStatus === 'delivered'): ?>
                                        <button class="btn btn-outline-primary btn-sm ms-2" onclick="reorderItems(<?= htmlspecialchars($orderId) ?>)">
                                            <i class="fas fa-redo me-1"></i>Reorder
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Orders pagination">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No orders found</h4>
                    <p class="text-muted">You haven't placed any orders yet</p>
                    <a href="/products" class="btn btn-success">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Order filtering
document.querySelectorAll('input[name="orderFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const status = this.id;
        const orderCards = document.querySelectorAll('.order-card');
        
        orderCards.forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Order search
document.getElementById('orderSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const orderCards = document.querySelectorAll('.order-card');
    
    orderCards.forEach(card => {
        const orderText = card.textContent.toLowerCase();
        if (orderText.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Reorder function
function reorderItems(orderId) {
    if (confirm('Add all items from this order to your cart?')) {
        fetch(`/api/reorder/${orderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Items added to cart successfully!');
                window.location.href = '/cart';
            } else {
                alert('Error adding items to cart');
            }
        });
    }
}
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
