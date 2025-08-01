<?php
// Define APP_PATH if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

// Ensure we have the required variables
if (!isset($user)) {
    $user = getCurrentUser() ?? [];
}
if (!isset($cartItems)) {
    $cartItems = [];
}
if (!isset($total)) {
    $total = 0;
}

include APP_PATH . '/Views/layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-success">Home</a></li>
<li class="breadcrumb-item"><a href="<?= url('cart') ?>" class="text-success">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Checkout</h4>
                </div>
                <div class="card-body">
                    <form action="<?= url('checkout/process') ?>" method="POST" id="checkoutForm">
                        <?= csrfField() ?>
                        <!-- Step 1: Shipping Information -->
                        <div class="checkout-step active" id="step1">
                            <h5 class="mb-3">
                                <span class="step-number">1</span>
                                Shipping Information
                            </h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Street Address *</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="House number and street name" required>
                            </div>

                            <div class="mb-3">
                                <label for="address2" class="form-label">Apartment, suite, etc. (optional)</label>
                                <input type="text" class="form-control" id="address2" name="address2" 
                                       placeholder="Apartment, suite, unit, building, floor, etc.">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State/Province *</label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">Select State</option>
                                        <option value="Punjab">Punjab</option>
                                        <option value="Sindh">Sindh</option>
                                        <option value="KPK">Khyber Pakhtunkhwa</option>
                                        <option value="Balochistan">Balochistan</option>
                                        <option value="Gilgit-Baltistan">Gilgit-Baltistan</option>
                                        <option value="AJK">Azad Jammu & Kashmir</option>
                                        <option value="Islamabad">Islamabad Capital Territory</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label">Postal Code *</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="save_address" name="save_address">
                                <label class="form-check-label" for="save_address">
                                    Save this address for future orders
                                </label>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="billing_same" name="billing_same" checked>
                                <label class="form-check-label" for="billing_same">
                                    Billing address is the same as shipping address
                                </label>
                            </div>

                            <!-- Address Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="billing_address_id" class="form-label">Billing Address *</label>
                                    <select class="form-select" id="billing_address_id" name="billing_address_id" required>
                                        <option value="">Select Billing Address</option>
                                        <?php if (!empty($addresses)): ?>
                                            <?php foreach ($addresses as $address): ?>
                                                <option value="<?= $address['id'] ?>">
                                                    <?= htmlspecialchars($address['address_line1'] . ', ' . $address['city'] . ', ' . $address['state']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="new">Use current address as billing address</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_address_id" class="form-label">Shipping Address *</label>
                                    <select class="form-select" id="shipping_address_id" name="shipping_address_id" required>
                                        <option value="">Select Shipping Address</option>
                                        <?php if (!empty($addresses)): ?>
                                            <?php foreach ($addresses as $address): ?>
                                                <option value="<?= $address['id'] ?>">
                                                    <?= htmlspecialchars($address['address_line1'] . ', ' . $address['city'] . ', ' . $address['state']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="new">Use current address as shipping address</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <button type="button" class="btn btn-success" onclick="nextStep(2)">
                                Continue to Payment <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <!-- Step 2: Payment Information -->
                        <div class="checkout-step" id="step2" style="display: none;">
                            <h5 class="mb-3">
                                <span class="step-number">2</span>
                                Payment Information
                            </h5>

                            <div class="payment-methods mb-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Cash on Delivery (COD)
                                    </label>
                                    <div class="form-text">Pay when your order is delivered to your doorstep</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                    <label class="form-check-label" for="bank_transfer">
                                        <i class="fas fa-university me-2"></i>
                                        Bank Transfer
                                    </label>
                                    <div class="form-text">Transfer payment directly to our bank account</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="jazzcash" value="jazzcash">
                                    <label class="form-check-label" for="jazzcash">
                                        <i class="fas fa-mobile-alt me-2"></i>
                                        JazzCash
                                    </label>
                                    <div class="form-text">Pay using JazzCash mobile wallet</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="easypaisa" value="easypaisa">
                                    <label class="form-check-label" for="easypaisa">
                                        <i class="fas fa-mobile-alt me-2"></i>
                                        EasyPaisa
                                    </label>
                                    <div class="form-text">Pay using EasyPaisa mobile wallet</div>
                                </div>
                            </div>

                            <!-- Payment Details (shown based on selected method) -->
                            <div id="payment-details" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div id="bank-details" style="display: none;">
                                            <h6>Bank Transfer Details:</h6>
                                            <p class="mb-1"><strong>Bank:</strong> Meezan Bank</p>
                                            <p class="mb-1"><strong>Account Title:</strong> Moxo Mart</p>
                                            <p class="mb-1"><strong>Account Number:</strong> 0123456789</p>
                                            <p class="mb-3"><strong>IBAN:</strong> PK12MEZN0000000123456789</p>
                                            
                                            <div class="mb-3">
                                                <label for="transaction_id" class="form-label">Transaction ID/Reference</label>
                                                <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                                                       placeholder="Enter transaction reference">
                                            </div>
                                        </div>

                                        <div id="mobile-wallet-details" style="display: none;">
                                            <h6>Mobile Wallet Details:</h6>
                                            <p class="mb-3"><strong>Account Number:</strong> 03XX-XXXXXXX</p>
                                            
                                            <div class="mb-3">
                                                <label for="wallet_transaction_id" class="form-label">Transaction ID</label>
                                                <input type="text" class="form-control" id="wallet_transaction_id" name="wallet_transaction_id" 
                                                       placeholder="Enter transaction ID">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="order_notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="order_notes" name="order_notes" rows="3" 
                                          placeholder="Special instructions for your order..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Shipping
                                </button>
                                <button type="button" class="btn btn-success" onclick="nextStep(3)">
                                    Review Order <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Order Review -->
                        <div class="checkout-step" id="step3" style="display: none;">
                            <h5 class="mb-3">
                                <span class="step-number">3</span>
                                Review Your Order
                            </h5>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Order Items</h6>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($cartItems as $item): ?>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-2">
                                                <img src="<?= $item['image'] ?? '/assets/images/placeholder.jpg' ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="img-fluid rounded">
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                                <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="fw-bold">₨<?= number_format($item['price'] * $item['quantity']) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms_conditions" name="terms_conditions" required>
                                <label class="form-check-label" for="terms_conditions">
                                    I agree to the <a href="<?= url('terms') ?>" target="_blank" class="text-success">Terms & Conditions</a>
and <a href="<?= url('privacy') ?>" target="_blank" class="text-success">Privacy Policy</a>
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Payment
                                </button>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Place Order
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?= $cartCount ?> items):</span>
                        <span>₨<?= number_format($subtotal) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span id="shipping-cost">₨<?= number_format($shippingCost) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <span>₨<?= number_format($taxAmount) ?></span>
                    </div>
                    <?php if ($discountAmount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <span>-₨<?= number_format($discountAmount) ?></span>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total:</span>
                        <span class="text-success">₨<?= number_format($total) ?></span>
                    </div>
                </div>
            </div>

            <!-- Promo Code -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Have a promo code?</h6>
                    <div class="input-group">
                        <input type="text" class="form-control" id="promo_code" placeholder="Enter code">
                        <button class="btn btn-outline-success" type="button" onclick="applyPromoCode()">Apply</button>
                    </div>
                </div>
            </div>

            <!-- Security Badge -->
            <div class="text-center mt-3">
                <div class="d-flex justify-content-center align-items-center text-muted">
                    <i class="fas fa-lock me-2"></i>
                    <small>Secure SSL encrypted checkout</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    background-color: #198754;
    color: white;
    border-radius: 50%;
    font-weight: bold;
    margin-right: 10px;
}

.checkout-step {
    padding: 20px 0;
}

.payment-methods .form-check {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.payment-methods .form-check:hover {
    border-color: #198754;
    background-color: rgba(25, 135, 84, 0.05);
}

.payment-methods .form-check-input:checked + .form-check-label {
    color: #198754;
    font-weight: 500;
}
</style>

<script>
function nextStep(step) {
    // Hide all steps
    document.querySelectorAll('.checkout-step').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show target step
    document.getElementById('step' + step).style.display = 'block';
    
    // Scroll to top
    window.scrollTo(0, 0);
}

function prevStep(step) {
    // Hide all steps
    document.querySelectorAll('.checkout-step').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show target step
    document.getElementById('step' + step).style.display = 'block';
    
    // Scroll to top
    window.scrollTo(0, 0);
}

// Payment method change handler
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const paymentDetails = document.getElementById('payment-details');
        const bankDetails = document.getElementById('bank-details');
        const mobileWalletDetails = document.getElementById('mobile-wallet-details');
        
        if (this.value === 'cod') {
            paymentDetails.style.display = 'none';
        } else if (this.value === 'bank_transfer') {
            paymentDetails.style.display = 'block';
            bankDetails.style.display = 'block';
            mobileWalletDetails.style.display = 'none';
        } else if (this.value === 'jazzcash' || this.value === 'easypaisa') {
            paymentDetails.style.display = 'block';
            bankDetails.style.display = 'none';
            mobileWalletDetails.style.display = 'block';
        }
    });
});

function applyPromoCode() {
    const promoCode = document.getElementById('promo_code').value;
    
    if (!promoCode) {
        alert('Please enter a promo code');
        return;
    }
    
    fetch('<?= url("api/apply-promo") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ promo_code: promoCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Promo code applied successfully!');
            location.reload();
        } else {
            alert(data.message || 'Invalid promo code');
        }
    });
}

// Form validation
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    
    if (paymentMethod === 'bank_transfer') {
        const transactionId = document.getElementById('transaction_id').value;
        if (!transactionId) {
            e.preventDefault();
            alert('Please enter the transaction ID for bank transfer');
            return;
        }
    }
    
    if (paymentMethod === 'jazzcash' || paymentMethod === 'easypaisa') {
        const walletTransactionId = document.getElementById('wallet_transaction_id').value;
        if (!walletTransactionId) {
            e.preventDefault();
            alert('Please enter the transaction ID for mobile wallet payment');
            return;
        }
    }
});
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
