<?php
/**
 * Homepage Template
 * Hero section inspired by sadadropship.com design
 */

include APP_PATH . '/Views/layouts/header.php';
?>

<!-- Hero Section -->
<section class="hero-section py-5">
  <div class="container">
    <div class="row align-items-center min-vh-50">
      <div class="col-lg-6">
        <div class="hero-content">
          <p class="text-uppercase mb-3 text-warning fw-bold">PAKISTAN'S LEADING E-COMMERCE SOLUTION WITH INTEGRATION</p>
          <h1 class="display-4 fw-bold mb-4">
            Get the Experience of <span class="text-warning">Moxo Mart</span>
          </h1>
          
          <ul class="feature-list mb-4">
            <li><i class="fas fa-check-circle"></i> Easy Integration</li>
            <li><i class="fas fa-check-circle"></i> Same day booking</li>
            <li><i class="fas fa-check-circle"></i> WhatsApp Support</li>
            <li><i class="fas fa-check-circle"></i> Live order tracking</li>
            <li><i class="fas fa-check-circle"></i> Free signup</li>
          </ul>
          
          <div class="hero-buttons">
            <a href="<?= url('products') ?>" class="btn btn-warning btn-lg me-3">
              SHOP NOW
            </a>
            <a href="<?= url('contact') ?>" class="btn btn-dark btn-lg">
              CONTACT US
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-image text-center">
          <img src="<?= asset('images/hero.jpg') ?>" alt="Moxo Mart Hero" class="img-fluid rounded-3 shadow-lg">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products py-5 bg-light">
  <div class="container">
    <div class="row mb-5">
      <div class="col-12 text-center">
        <h2 class="display-5 fw-bold mb-3">Featured Products</h2>
        <p class="lead text-muted">
          Discover our handpicked selection of quality products
        </p>
      </div>
    </div>
    
    <div class="row g-4">
      <?php foreach ($featuredProducts as $index => $product): ?>
      <div class="col-lg-3 col-md-6">
        <div class="product-card bg-white shadow-sm h-100">
          <a href="<?= url('products/' . $product['slug']) ?>" class="text-decoration-none">
          <div class="product-image position-relative">
            <?php
            $productModel = new App\Models\Product();
            $primaryImage = $productModel->getPrimaryImage($product['id']);
            $imageUrl = $primaryImage ? asset($primaryImage['image_path']) : asset('images/placeholder-product.jpg');
            ?>
            <img src="<?= $imageUrl ?>" alt="<?= e($product['name']) ?>" class="img-fluid">
            
            <?php if ($product['compare_price'] > $product['price']): ?>
            <span class="badge bg-danger position-absolute top-0 start-0 m-2">
              Sale
            </span>
            <?php endif; ?>
            
            <div class="product-overlay">
              <div class="btn-group">
                <button type="button" class="btn btn-light add-to-cart-btn" 
                        data-product-id="<?= $product['id'] ?>" 
                        <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                  <i class="fas fa-cart-plus"></i> Add
                </button>
              </div>
            </div>
          </div>
          
          <div class="product-info">
            <h6 class="product-title">
              <a href="<?= url('products/' . $product['slug']) ?>">
                <?= e($product['name']) ?>
              </a>
            </h6>
            <p class="product-description text-muted small mb-3">
              <?= e(truncate($product['short_description'], 80)) ?>
            </p>
            
            <div class="product-price mb-3">
              <span class="current-price">
                <?= formatPrice($product['price']) ?>
              </span>
              <?php if ($product['compare_price'] > $product['price']): ?>
              <span class="original-price text-muted text-decoration-line-through ms-2">
                <?= formatPrice($product['compare_price']) ?>
              </span>
              <?php endif; ?>
            </div>
            
            <div class="d-flex gap-2">
              <form class="add-to-cart-form flex-fill" data-product-id="<?= $product['id'] ?>">
                <?= csrfField() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-outline-success w-100" <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                  <i class="fas fa-cart-plus me-2"></i>
                  <?= $product['stock_quantity'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                </button>
              </form>
              <a href="<?= url('checkout?product=' . $product['id'] . '&quantity=1') ?>" class="btn btn-success" <?= $product['stock_quantity'] <= 0 ? 'onclick="return false;"' : '' ?>>
                <i class="fas fa-shopping-cart me-2"></i>
                Buy Now
              </a>
            </div>
          </div>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-5">
      <a href="<?= url('products') ?>" class="btn btn-success btn-lg">
        View All Products
        <i class="fas fa-arrow-right ms-2"></i>
      </a>
    </div>
  </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
  <div class="container">
    <div class="row mb-5">
      <div class="col-12 text-center">
        <h2 class="display-5 fw-bold mb-3">Shop by Category</h2>
        <p class="lead text-muted">
          Explore our wide range of product categories
        </p>
      </div>
    </div>
    
    <div class="row g-4">
      <?php foreach (array_slice($categories, 0, 6) as $index => $category): ?>
      <div class="col-lg-4 col-md-6">
        <div class="category-card position-relative">
          <a href="<?= url('products/category/' . $category['slug']) ?>" class="text-decoration-none">
            <div class="category-image position-relative">
              <img src="<?= $category['image'] ? asset('images/categories/' . $category['image']) : asset('images/placeholder-category.jpg') ?>" 
                   alt="<?= e($category['name']) ?>" class="img-fluid w-100" style="height: 200px; object-fit: cover;">
              <div class="category-overlay">
                <h5><?= e($category['name']) ?></h5>
              </div>
            </div>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- USP Section -->
<section class="usp-section py-5 bg-light">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4 text-center">
        <div class="usp-card p-4 bg-white rounded-3 shadow-sm h-100">
          <div class="usp-icon mb-3">
            <i class="fas fa-shipping-fast fa-3x text-success"></i>
          </div>
          <h5 class="fw-bold mb-3">Fast Shipping</h5>
          <p class="text-muted mb-0">
            Free shipping on orders over PKR 2000. Get your products delivered quickly with our reliable shipping partners.
          </p>
        </div>
      </div>
      <div class="col-md-4 text-center">
        <div class="usp-card p-4 bg-white rounded-3 shadow-sm h-100">
          <div class="usp-icon mb-3">
            <i class="fas fa-headset fa-3x text-success"></i>
          </div>
          <h5 class="fw-bold mb-3">24/7 Support</h5>
          <p class="text-muted mb-0">
            Our dedicated customer support team is available around the clock to help you with any questions via WhatsApp.
          </p>
        </div>
      </div>
      <div class="col-md-4 text-center">
        <div class="usp-card p-4 bg-white rounded-3 shadow-sm h-100">
          <div class="usp-icon mb-3">
            <i class="fas fa-map-marker-alt fa-3x text-success"></i>
          </div>
          <h5 class="fw-bold mb-3">Live Tracking</h5>
          <p class="text-muted mb-0">
            Track your orders in real-time from our warehouse to your doorstep. Stay updated every step of the way.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- CTA Section -->
<section class="cta-section bg-success text-white py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h3 class="fw-bold mb-3">Ready to Start Shopping?</h3>
        <p class="lead mb-0">
          Join thousands of satisfied customers and discover quality products at great prices.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="<?= url('products') ?>" class="btn btn-warning btn-lg">
          Shop Now
          <i class="fas fa-shopping-cart ms-2"></i>
        </a>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Add to cart functionality
  document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const button = this.querySelector('button[type="submit"]');
      const originalText = button.innerHTML;
      
      // Show loading state
      button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
      button.disabled = true;
      
      fetch('<?= url("cart/add") ?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          showAlert('success', 'Product added to cart successfully!');
          
          // Update cart count in header
          updateCartCount();
        } else {
          showAlert('error', data.message || 'Failed to add product to cart');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred. Please try again.');
      })
      .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
      });
    });
  });
  
  // Alert function
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
  
  // Update cart count
  function updateCartCount() {
    fetch('<?= url("cart/count") ?>')
      .then(response => response.json())
      .then(data => {
        const cartBadge = document.querySelector('.navbar .badge');
        if (cartBadge) {
          cartBadge.textContent = data.count;
          if (data.count > 0) {
            cartBadge.style.display = 'inline';
          }
        }
      });
  }
});
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
