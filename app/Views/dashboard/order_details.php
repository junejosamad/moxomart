<?php
// Define APP_PATH if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

// Ensure we have the required variables
if (!isset($order)) {
    echo '<div class="alert alert-danger">Order not found.</div>';
    return;
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

        <!-- Order Detail Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2 text-success">Order #<?= $order['id'] ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/dashboard" class="text-success">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/dashboard/orders" class="text-success">Orders</a></li>
                            <li class="breadcrumb-item active">Order #<?= $order['id'] ?></li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <span class="badge bg-<?= $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info') ?> fs-6">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>

            <div class="row">
                <!-- Order Summary -->
                <div class="col-lg-8">
                    <!-- Order Progress -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Order Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="progress-steps">
                                <div class="step <?= in_array($order['status'], ['pending', 'processing', 'shipped', 'delivered']) ? 'completed' : '' ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="step-content">
                                        <h6>Order Placed</h6>
                                        <small><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></small>
                                    </div>
                                </div>
                                <div class="step <?= in_array($order['status'], ['processing', 'shipped', 'delivered']) ? 'completed' : '' ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="step-content">
                                        <h6>Processing</h6>
                                        <small><?= $order['status'] !== 'pending' ? date('M d, Y H:i', strtotime($order['updated_at'])) : 'Pending' ?></small>
                                    </div>
                                </div>
                                <div class="step <?= in_array($order['status'], ['shipped', 'delivered']) ? 'completed' : '' ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div class="step-content">
                                        <h6>Shipped</h6>
                                        <small><?= $order['status'] === 'shipped' || $order['status'] === 'delivered' ? 'In Transit' : 'Not Shipped' ?></small>
                                    </div>
                                </div>
                                <div class="step <?= $order['status'] === 'delivered' ? 'completed' : '' ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-content">
                                        <h6>Delivered</h6>
                                        <small><?= $order['status'] === 'delivered' ? 'Completed' : 'Pending' ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="row align-items-center mb-3 pb-3 border-bottom">
                                    <div class="col-md-2">
                                        <img src="<?= $item['image'] ?? '/assets/images/placeholder.jpg' ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                             class="img-fluid rounded">
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                        <p class="text-muted mb-1"><?= htmlspecialchars($item['description']) ?></p>
                                        <small class="text-muted">SKU: <?= $item['sku'] ?></small>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="fw-bold">Qty: <?= $item['quantity'] ?></span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="fw-bold">₨<?= number_format($item['price']) ?></div>
                                        <small class="text-muted">₨<?= number_format($item['price'] * $item['quantity']) ?> total</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₨<?= number_format($order['subtotal']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>₨<?= number_format($order['shipping_cost']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>₨<?= number_format($order['tax_amount']) ?></span>
                            </div>
                            <?php if ($order['discount_amount'] > 0): ?>
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount:</span>
                                    <span>-₨<?= number_format($order['discount_amount']) ?></span>
                                </div>
                            <?php endif; ?>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span>₨<?= number_format($order['total_amount']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <address class="mb-0">
                                <strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
                                <?= htmlspecialchars($order['shipping_address']) ?><br>
                                <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_state']) ?><br>
                                <?= htmlspecialchars($order['shipping_postal_code']) ?><br>
                                <abbr title="Phone">P:</abbr> <?= htmlspecialchars($order['shipping_phone']) ?>
                            </address>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
                            <p class="mb-1"><strong>Payment Status:</strong> 
                                <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span>
                            </p>
                            <?php if ($order['transaction_id']): ?>
                                <p class="mb-0"><strong>Transaction ID:</strong> <?= $order['transaction_id'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-body">
                            <?php if ($order['status'] === 'delivered'): ?>
                                <button class="btn btn-success w-100 mb-2" onclick="reorderItems(<?= $order['id'] ?>)">
                                    <i class="fas fa-redo me-2"></i>Reorder Items
                                </button>
                            <?php endif; ?>
                            
                            <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                                <button class="btn btn-outline-danger w-100 mb-2" onclick="cancelOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-times me-2"></i>Cancel Order
                                </button>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-primary w-100 mb-2" onclick="downloadInvoice(<?= $order['id'] ?>)">
                                <i class="fas fa-download me-2"></i>Download Invoice
                            </button>
                            
                            <a href="/contact" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-headset me-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #e9ecef;
    z-index: 1;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    background: white;
    padding: 0 10px;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    color: #6c757d;
}

.step.completed .step-icon {
    background-color: #198754;
    color: white;
}

.step-content {
    text-align: center;
}

.step-content h6 {
    margin-bottom: 5px;
    font-size: 0.875rem;
}

.step-content small {
    color: #6c757d;
}
</style>

<script>
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

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch(`/api/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order cancelled successfully!');
                location.reload();
            } else {
                alert('Error cancelling order');
            }
        });
    }
}

function downloadInvoice(orderId) {
    window.open(`/api/orders/${orderId}/invoice`, '_blank');
}
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
