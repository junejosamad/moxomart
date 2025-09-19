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

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h1 class="text-success mb-3">Order Placed Successfully!</h1>
                <p class="lead text-muted">Thank you for your order. We've received your order and will process it shortly.</p>
            </div>

            <!-- Order Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Order #<?= $order['id'] ?></h5>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-light text-dark">
                                <?= date('M d, Y H:i', strtotime($order['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">Shipping Address</h6>
                            <address class="mb-3">
                                <strong><?= htmlspecialchars(($order['shipping_address']['first_name'] ?? '') . ' ' . ($order['shipping_address']['last_name'] ?? '')) ?></strong><br>
                                <?= htmlspecialchars($order['shipping_address']['address_line_1'] ?? '') ?><br>
                                <?php if (!empty($order['shipping_address']['address_line_2'])): ?>
                                    <?= htmlspecialchars($order['shipping_address']['address_line_2']) ?><br>
                                <?php endif; ?>
                                <?= htmlspecialchars($order['shipping_address']['city'] ?? '') ?>, <?= htmlspecialchars($order['shipping_address']['state'] ?? '') ?><br>
                                <?= htmlspecialchars($order['shipping_address']['postal_code'] ?? '') ?><br>
                                <abbr title="Phone">P:</abbr> <?= htmlspecialchars($order['shipping_address']['phone'] ?? '') ?>
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Payment Information</h6>
                            <p class="mb-1"><strong>Method:</strong> <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span>
                            </p>
                            <p class="mb-0"><strong>Total:</strong> ₨<?= number_format($order['total_amount']) ?></p>
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
                                <?php if(isset($item['description']) && $item['description']): ?>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($item['description']) ?></p>
                                <?php endif; ?>
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

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₨<?= number_format($order['total_amount'] - ($order['shipping_amount'] ?? 0) - ($order['tax_amount'] ?? 0) + ($order['discount_amount'] ?? 0)) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>₨<?= number_format($order['shipping_amount'] ?? 0) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>₨<?= number_format($order['tax_amount'] ?? 0) ?></span>
                            </div>
                            <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount:</span>
                                    <span>-₨<?= number_format($order['discount_amount'] ?? 0) ?></span>
                                </div>
                            <?php endif; ?>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total:</span>
                                <span class="text-success">₨<?= number_format($order['total_amount']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">What's Next?</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-envelope fa-2x text-success mb-2"></i>
                            <h6>Order Confirmation</h6>
                            <p class="text-muted small">You'll receive an email confirmation shortly with your order details.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-cog fa-2x text-info mb-2"></i>
                            <h6>Processing</h6>
                            <p class="text-muted small">We'll start processing your order within 24 hours.</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                            <h6>Delivery</h6>
                            <p class="text-muted small">Your order will be delivered within 3-5 business days.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center">
                <a href="/dashboard/orders" class="btn btn-success btn-lg me-3">
                    <i class="fas fa-list me-2"></i>View My Orders
                </a>
                <a href="/products" class="btn btn-outline-success btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
            </div>

            <!-- Contact Support -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    Need help with your order? 
                    <a href="/contact" class="text-success">Contact our support team</a> or 
                    <a href="https://wa.me/923001234567" class="text-success" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp us
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: bounceIn 1s ease-in-out;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
