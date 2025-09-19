<?php
/**
 * Shopping Cart Page
 * Shows cart items, totals, and checkout button
 */

include APP_PATH . '/Views/layouts/header.php';
?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
    </ol>
  </nav>
  
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-12">
      <h1 class="display-6 fw-bold">Shopping Cart</h1>
    </div>
  </div>
  
  <?php if (!empty($cartData['items'])): ?>
  <div class="row">
    <!-- Cart Items -->
    <div class="col-lg-8">
      <div class="cart-items">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Cart Items (<?= $cartData['item_count'] ?>)</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($cartData['items'] as $item): ?>
                  <tr data-product-id="<?= $item['product_id'] ?>">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="product-image me-3">
                                                    <img src="<?= asset('images/placeholder-product.jpg') ?>"
                               alt="<?= e($item['name']) ?>"
                               class="img-fluid rounded"
                               style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="product-info">
                          <h6 class="mb-1">
                            <a href="<?= url('products/' . $item['slug']) ?>" class="text-decoration-none text-dark">
                              <?= e($item['name']) ?>
                            </a>
                          </h6>
                          <small class="text-muted">SKU: <?= e($item['sku'] ?? 'N/A') ?></small>
                          <?php if ($item['stock_quantity'] < $item['quantity']): ?>
                          <div class="text-warning small mt-1">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Only <?= $item['stock_quantity'] ?> left in stock
                          </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td class="align-middle">
                      <span class="fw-bold"><?= formatPrice($item['price']) ?></span>
                    </td>
                    <td class="align-middle">
                      <div class="quantity-controls d-flex align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                onclick="updateQuantity(<?= $item['product_id'] ?>, parseInt(this.parentNode.querySelector('input').value) - 1)">
                          -
                        </button>
                        <input type="number" class="form-control form-control-sm mx-2 text-center" 
                               style="width: 70px;" 
                               value="<?= $item['quantity'] ?>" 
                               min="1" 
                               max="<?= $item['stock_quantity'] ?>"
                               onchange="updateQuantity(<?= $item['product_id'] ?>, this.value)">
                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                onclick="updateQuantity(<?= $item['product_id'] ?>, parseInt(this.parentNode.querySelector('input').value) + 1)"
                                <?= $item['quantity'] >= $item['stock_quantity'] ? 'disabled' : '' ?>>
                          +
                        </button>
                      </div>
                    </td>
                    <td class="align-middle">
                      <span class="fw-bold item-total"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                    </td>
                    <td class="align-middle">
                      <button type="button" class="btn btn-outline-danger btn-sm" 
                              onclick="removeItem(<?= $item['product_id'] ?>)"
                              title="Remove item">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Cart Actions -->
        <div class="cart-actions mt-3 d-flex justify-content-between">
          <a href="<?= url('products') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>
            Continue Shopping
          </a>
          <button type="button" class="btn btn-outline-secondary" onclick="clearCart()">
            <i class="fas fa-trash me-2"></i>
            Clear Cart
          </button>
        </div>
      </div>
    </div>
    
    <!-- Cart Summary -->
    <div class="col-lg-4">
      <div class="cart-summary">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Order Summary</h5>
          </div>
          <div class="card-body">
            <div class="summary-row d-flex justify-content-between mb-2">
              <span>Subtotal:</span>
              <span id="cart-subtotal"><?= formatPrice($cartData['subtotal']) ?></span>
            </div>
            <div class="summary-row d-flex justify-content-between mb-2">
              <span>Shipping:</span>
              <span id="cart-shipping"><?= formatPrice($cartData['shipping']) ?></span>
            </div>
            <div class="summary-row d-flex justify-content-between mb-2">
              <span>Tax:</span>
              <span id="cart-tax"><?= formatPrice($cartData['tax']) ?></span>
            </div>
            <hr>
            <div class="summary-row d-flex justify-content-between mb-3">
              <strong>Total:</strong>
              <strong class="text-primary" id="cart-total"><?= formatPrice($cartData['total']) ?></strong>
            </div>
            
            <!-- Coupon Code -->
            <div class="coupon-section mb-3">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Coupon code" id="coupon-code">
                <button class="btn btn-outline-secondary" type="button" onclick="applyCoupon()">
                  Apply
                </button>
              </div>
            </div>
            
            <!-- Checkout Button -->
            <a href="<?= url('checkout') ?>" class="btn btn-primary btn-lg w-100">
              <i class="fas fa-lock me-2"></i>
              Proceed to Checkout
            </a>
            
            <!-- Security Badges -->
            <div class="security-badges text-center mt-3">
              <small class="text-muted d-block mb-2">Secure Checkout</small>
              <div class="d-flex justify-content-center gap-2">
                <img src="<?= asset('images/security/ssl.svg') ?>" alt="SSL Secure" height="24">
                <img src="<?= asset('images/payment/visa.svg') ?>" alt="Visa" height="24">
                <img src="<?= asset('images/payment/mastercard.svg') ?>" alt="Mastercard" height="24">
                <img src="<?= asset('images/payment/paypal.svg') ?>" alt="PayPal" height="24">
              </div>
            </div>
          </div>
        </div>
        
        <!-- Shipping Info -->
        <div class="card mt-3">
          <div class="card-body">
            <h6 class="card-title">
              <i class="fas fa-shipping-fast text-primary me-2"></i>
              Shipping Information
            </h6>
            <ul class="list-unstyled mb-0 small">
              <li class="mb-1">
                <i class="fas fa-check text-success me-2"></i>
                Free shipping on orders over $50
              </li>
              <li class="mb-1">
                <i class="fas fa-clock text-info me-2"></i>
                Standard delivery: 3-5 business days
              </li>
              <li>
                <i class="fas fa-bolt text-warning me-2"></i>
                Express delivery available
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php else: ?>
  <!-- Empty Cart -->
  <div class="empty-cart text-center py-5">
    <div class="empty-cart-icon mb-4">
      <i class="fas fa-shopping-cart fa-5x text-muted"></i>
    </div>
    <h3 class="mb-3">Your cart is empty</h3>
    <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
    <a href="<?= url('products') ?>" class="btn btn-primary btn-lg">
      <i class="fas fa-shopping-bag me-2"></i>
      Start Shopping
    </a>
  </div>
  <?php endif; ?>
</div>

<script>
// Update item quantity
function updateQuantity(productId, quantity) {
  if (quantity < 1) {
    removeItem(productId);
    return;
  }
  
  const formData = new FormData();
  formData.append('product_id', productId);
  formData.append('quantity', quantity);
  formData.append('csrf_token', '<?= generateCsrfToken() ?>');
  
  fetch('<?= url("cart/update") ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Update the row
      const row = document.querySelector(`tr[data-product-id="${productId}"]`);
      const itemTotal = row.querySelector('.item-total');
      itemTotal.textContent = data.item_total;
      
      // Update cart totals
      updateCartTotals(data.cart_totals);
      
      // Update cart count in header
      updateCartCount();
    } else {
      showAlert('error', data.message || 'Failed to update quantity');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAlert('error', 'An error occurred. Please try again.');
  });
}

// Remove item from cart
function removeItem(productId) {
  if (!confirm('Are you sure you want to remove this item from your cart?')) {
    return;
  }
  
  const formData = new FormData();
  formData.append('product_id', productId);
  formData.append('csrf_token', '<?= generateCsrfToken() ?>');
  
  fetch('<?= url("cart/remove") ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Remove the row
      const row = document.querySelector(`tr[data-product-id="${productId}"]`);
      row.remove();
      
      // Update cart totals
      updateCartTotals(data.cart_totals);
      
      // Update cart count in header
      updateCartCount();
      
      // Check if cart is empty
      if (data.cart_totals.item_count === 0) {
        location.reload();
      }
      
      showAlert('success', 'Item removed from cart');
    } else {
      showAlert('error', data.message || 'Failed to remove item');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAlert('error', 'An error occurred. Please try again.');
  });
}

// Clear entire cart
function clearCart() {
  if (!confirm('Are you sure you want to clear your entire cart?')) {
    return;
  }
  
  const formData = new FormData();
  formData.append('csrf_token', '<?= generateCsrfToken() ?>');
  
  fetch('<?= url("cart/clear") ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      showAlert('error', data.message || 'Failed to clear cart');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAlert('error', 'An error occurred. Please try again.');
  });
}

// Apply coupon code
function applyCoupon() {
  const couponCode = document.getElementById('coupon-code').value.trim();
  
  if (!couponCode) {
    showAlert('warning', 'Please enter a coupon code');
    return;
  }
  
  const formData = new FormData();
  formData.append('coupon_code', couponCode);
  formData.append('csrf_token', '<?= generateCsrfToken() ?>');
  
  fetch('<?= url("cart/apply-coupon") ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      updateCartTotals(data.cart_totals);
      showAlert('success', 'Coupon applied successfully!');
    } else {
      showAlert('error', data.message || 'Invalid coupon code');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showAlert('error', 'An error occurred. Please try again.');
  });
}

// Update cart totals display
function updateCartTotals(totals) {
  document.getElementById('cart-subtotal').textContent = totals.subtotal_formatted;
  document.getElementById('cart-shipping').textContent = totals.shipping_formatted;
  document.getElementById('cart-tax').textContent = totals.tax_formatted;
  document.getElementById('cart-total').textContent = totals.total_formatted;
}

// Update cart count in header
function updateCartCount() {
  fetch('<?= url("cart/count") ?>')
    .then(response => response.json())
    .then(data => {
      const cartBadge = document.querySelector('.navbar .badge');
      if (cartBadge) {
        cartBadge.textContent = data.count;
        if (data.count === 0) {
          cartBadge.style.display = 'none';
        }
      }
    });
}

// Show alert message
function showAlert(type, message) {
  const alertClass = type === 'error' ? 'danger' : type;
  const alertHtml = `
    <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
  
  document.body.insertAdjacentHTML('beforeend', alertHtml);
  
  // Auto dismiss after 5 seconds
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      alert.remove();
    }
  }, 5000);
}
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
