<?php
/**
 * Product Detail Page - Mobile Responsive
 * Shows individual product with images, details, and add to cart
 */

// Define APP_PATH if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

// Ensure we have the required variables
if (!isset($product)) {
    echo '<div class="alert alert-danger">Product not found.</div>';
    return;
}

// Set default meta if not provided
if (!isset($meta)) {
    $meta = [
        'title' => $product['name'] . ' - Moxo Mart',
        'description' => $product['short_description'] ?? 'Product details',
        'og_title' => $product['name'],
        'og_description' => $product['short_description'] ?? '',
        'og_image' => (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) ? asset($product['images'][0]['image_path']) : asset('images/placeholder-product.jpg')
    ];
}

// Set page class for styling
$pageClass = 'product-detail-page';

// Include header with proper variable scope
include APP_PATH . '/Views/layouts/header.php';
?>

<style>
/* Mobile Responsive Styles */
@media (max-width: 768px) {
  .container {
    padding-left: 15px;
    padding-right: 15px;
  }
  
  .breadcrumb {
    font-size: 0.875rem;
    padding: 0.5rem 0;
  }
  
  .breadcrumb-item + .breadcrumb-item::before {
    padding: 0 0.25rem;
  }
  
  .display-6 {
    font-size: 1.75rem !important;
  }
  
  .product-images .main-image {
    margin-bottom: 1rem;
  }
  
  .product-images .main-image img {
    height: 300px !important;
    border-radius: 0.5rem;
  }
  
  .thumbnail-images .col-3 {
    flex: 0 0 20%;
    max-width: 20%;
  }
  
  .thumbnail-images img {
    height: 60px !important;
    border-radius: 0.25rem;
  }
  
  .product-price .current-price {
    font-size: 1.5rem !important;
  }
  
  .product-price .original-price {
    font-size: 1rem !important;
  }
  
  .product-actions {
    flex-direction: column;
    gap: 0.5rem !important;
  }
  
  .product-actions .btn {
    width: 100%;
    padding: 0.75rem;
  }
  
  .product-meta .row > div {
    margin-bottom: 0.5rem;
  }
  
  .nav-tabs {
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .nav-tabs .nav-link {
    white-space: nowrap;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
  
  .tab-content .p-4 {
    padding: 1rem !important;
  }
  
  .related-products .col-lg-3 {
    flex: 0 0 50%;
    max-width: 50%;
  }
  
  .related-products .product-card img {
    height: 150px !important;
  }
  
  .alert {
    font-size: 0.875rem;
    padding: 0.75rem;
  }
  
  .btn-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
  }
  
  .input-group {
    flex-wrap: nowrap;
  }
  
  .input-group .btn {
    padding: 0.5rem 0.75rem;
  }
  
  .input-group .form-control {
    min-width: 60px;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 10px;
    padding-right: 10px;
  }
  
  .display-6 {
    font-size: 1.5rem !important;
    line-height: 1.3;
  }
  
  .product-images .main-image img {
    height: 250px !important;
  }
  
  .thumbnail-images img {
    height: 50px !important;
  }
  
  .product-price {
    text-align: center;
    margin-bottom: 1.5rem !important;
  }
  
  .product-description .lead {
    font-size: 1rem;
  }
  
  .row.g-3 {
    --bs-gutter-x: 0.5rem;
  }
  
  .col-md-4, .col-md-8 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 1rem;
  }
  
  .related-products .col-lg-3 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .review-summary .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 1rem;
  }
  
  .review-summary .btn {
    width: 100%;
  }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
  .thumbnail-image {
    border: 2px solid transparent;
    transition: border-color 0.2s;
  }
  
  .thumbnail-image.active {
    border-color: #198754;
  }
  
  .btn {
    min-height: 44px;
  }
  
  .nav-link {
    min-height: 44px;
    display: flex;
    align-items: center;
  }
}
</style>

<div class="container py-3 py-md-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3 mb-md-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= url('products') ?>">Products</a></li>
      <?php if (!empty($product['category_name'])): ?>
      <li class="breadcrumb-item"><a href="<?= url('products/category/' . generateSlug($product['category_name'])) ?>"><?= e($product['category_name']) ?></a></li>
      <?php endif; ?>
      <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
    </ol>
  </nav>
  
  <!-- Product Details -->
  <div class="row">
    <!-- Product Images -->
    <div class="col-lg-6 mb-4">
      <div class="product-images">
        <!-- Main Image -->
        <div class="main-image mb-3">
          <?php 
          $mainImage = null;
          if (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
              $mainImage = $product['images'][0];
          }
          ?>
          <img id="main-product-image" 
               src="<?= $mainImage ? asset($mainImage['image_path']) : asset('images/placeholder-product.jpg') ?>" 
               alt="<?= e($product['name']) ?>" 
               class="img-fluid rounded-3 shadow-sm w-100" 
               style="height: 500px; object-fit: cover;">
        </div>
        
        <!-- Thumbnail Images -->
        <?php if (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 1): ?>
        <div class="thumbnail-images">
          <div class="row g-2">
            <?php foreach ($product['images'] as $index => $image): ?>
            <div class="col-3">
              <img src="<?= asset($image['image_path']) ?>" 
                   alt="<?= e($image['alt_text'] ?: $product['name']) ?>" 
                   class="img-fluid rounded-2 thumbnail-image <?= $index === 0 ? 'active' : '' ?>" 
                   style="height: 100px; object-fit: cover; cursor: pointer;"
                   onclick="changeMainImage('<?= asset($image['image_path']) ?>', this)">
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Product Info -->
    <div class="col-lg-6">
      <div class="product-info">
        <!-- Product Title -->
        <h1 class="display-6 fw-bold mb-3"><?= e($product['name']) ?></h1>
        
        <!-- Product Rating -->
        <div class="product-rating mb-3">
          <div class="d-flex align-items-center flex-wrap">
            <div class="stars text-warning me-2">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="far fa-star"></i>
            </div>
            <span class="text-muted">(4.0) • 24 reviews</span>
          </div>
        </div>
        
        <!-- Product Price -->
        <div class="product-price mb-4">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="current-price display-6 fw-bold text-primary">
              <?= formatPrice($product['price']) ?>
            </span>
            <?php if ($product['compare_price'] > $product['price']): ?>
            <span class="original-price fs-4 text-muted text-decoration-line-through">
              <?= formatPrice($product['compare_price']) ?>
            </span>
            <span class="badge bg-danger">
              Save <?= round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100) ?>%
            </span>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- Product Description -->
        <div class="product-description mb-4">
          <p class="lead"><?= e($product['short_description']) ?></p>
        </div>
        
        <!-- Product Attributes -->
        <?php if (!empty($product['attributes'])): ?>
        <div class="product-attributes mb-4">
          <h6 class="fw-bold mb-3">Specifications</h6>
          <div class="row">
            <?php foreach ($product['attributes'] as $attribute): ?>
            <div class="col-sm-6 mb-2">
              <strong><?= e($attribute['name']) ?>:</strong>
              <span class="text-muted"><?= e($attribute['value']) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Stock Status -->
        <div class="stock-status mb-4">
          <?php if ($product['stock_quantity'] > 0): ?>
          <div class="alert alert-success d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <span>In Stock (<?= $product['stock_quantity'] ?> available)</span>
          </div>
          <?php else: ?>
          <div class="alert alert-danger d-flex align-items-center">
            <i class="fas fa-times-circle me-2"></i>
            <span>Out of Stock</span>
          </div>
          <?php endif; ?>
        </div>
        
        <!-- Add to Cart Form -->
        <form id="add-to-cart-form" class="mb-4" method="POST">
          <?= csrfField() ?>
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          
          <div class="row g-3 align-items-end">
            <div class="col-md-4 col-12">
              <label for="quantity" class="form-label">Quantity</label>
              <div class="input-group">
                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">-</button>
                <input type="number" id="quantity" name="quantity" class="form-control text-center" 
                       value="1" min="1" max="<?= $product['stock_quantity'] ?>" 
                       <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity()">+</button>
              </div>
            </div>
            <div class="col-md-8 col-12">
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-lg flex-fill" 
                        <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                  <i class="fas fa-cart-plus me-2"></i>
                  <?= $product['stock_quantity'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                </button>
                <button type="button" class="btn btn-primary btn-lg buy-now-btn" 
                        data-product-id="<?= $product['id'] ?>" 
                        data-quantity="1"
                        <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                  <i class="fas fa-shopping-cart me-2"></i>
                  Buy Now
                </button>
              </div>
            </div>
          </div>
        </form>
        
        <!-- Additional Actions -->
        <div class="product-actions d-flex gap-2 mb-4">
          <button type="button" class="btn btn-outline-secondary flex-fill">
            <i class="fas fa-heart me-2"></i>
            <span class="d-none d-sm-inline">Add to </span>Wishlist
          </button>
          <button type="button" class="btn btn-outline-secondary flex-fill">
            <i class="fas fa-share-alt me-2"></i>
            Share
          </button>
        </div>
        
        <!-- Product Meta -->
        <div class="product-meta">
          <div class="row text-muted small">
            <div class="col-6 col-sm-6">
              <strong>SKU:</strong> <?= e($product['sku']) ?>
            </div>
            <div class="col-6 col-sm-6">
              <strong>Category:</strong> 
              <?php if ($product['category_name']): ?>
              <a href="<?= url('products/category/' . generateSlug($product['category_name'])) ?>" class="text-decoration-none">
                <?= e($product['category_name']) ?>
              </a>
              <?php else: ?>
              N/A
              <?php endif; ?>
            </div>
            <?php if ($product['brand']): ?>
            <div class="col-6 col-sm-6 mt-2">
              <strong>Brand:</strong> <?= e($product['brand']) ?>
            </div>
            <?php endif; ?>
            <?php if ($product['weight']): ?>
            <div class="col-6 col-sm-6 mt-2">
              <strong>Weight:</strong> <?= e($product['weight']) ?> lbs
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Product Tabs -->
  <div class="product-tabs mt-4 mt-md-5">
    <ul class="nav nav-tabs" id="productTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
          Description
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
          Specifications
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
          Reviews (<?= $rating['count'] ?? 0 ?>)
        </button>
      </li>
    </ul>
    
    <div class="tab-content" id="productTabsContent">
      <!-- Description Tab -->
      <div class="tab-pane fade show active" id="description" role="tabpanel">
        <div class="p-3 p-md-4">
          <?php if ($product['description']): ?>
          <div class="product-description">
            <?= $product['description'] ?>
          </div>
          <?php else: ?>
          <p class="text-muted">No detailed description available.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Specifications Tab -->
      <div class="tab-pane fade" id="specifications" role="tabpanel">
        <div class="p-3 p-md-4">
          <?php if (!empty($product['attributes'])): ?>
          <div class="table-responsive">
            <table class="table table-striped">
              <tbody>
                <?php foreach ($product['attributes'] as $attribute): ?>
                <tr>
                  <td class="fw-bold" style="width: 30%;"><?= e($attribute['name']) ?></td>
                  <td><?= e($attribute['value']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
          <p class="text-muted">No specifications available.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Reviews Tab -->
      <div class="tab-pane fade" id="reviews" role="tabpanel">
        <div class="p-3 p-md-4">
          <!-- Review Summary -->
          <div class="review-summary mb-4">
            <div class="row align-items-center">
              <div class="col-md-6 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                  <div class="rating-average me-3">
                    <span class="display-4 fw-bold">4.0</span>
                  </div>
                  <div>
                    <div class="stars text-warning mb-1">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="far fa-star"></i>
                    </div>
                    <p class="text-muted mb-0">Based on 24 reviews</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <button class="btn btn-primary w-100 w-md-auto">Write a Review</button>
              </div>
            </div>
          </div>
          
          <!-- Sample Reviews -->
          <div class="reviews-list">
            <div class="review-item border-bottom pb-3 mb-3">
              <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap">
                <div class="mb-2 mb-sm-0">
                  <h6 class="mb-1">John Doe</h6>
                  <div class="stars text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                  </div>
                </div>
                <small class="text-muted">2 days ago</small>
              </div>
              <p class="mb-0">Excellent product! Great quality and fast shipping. Highly recommended.</p>
            </div>
            
            <div class="review-item border-bottom pb-3 mb-3">
              <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap">
                <div class="mb-2 mb-sm-0">
                  <h6 class="mb-1">Jane Smith</h6>
                  <div class="stars text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                  </div>
                </div>
                <small class="text-muted">1 week ago</small>
              </div>
              <p class="mb-0">Good value for money. Product works as expected. Delivery was prompt.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Related Products -->
  <?php if (!empty($relatedProducts)): ?>
  <section class="related-products mt-4 mt-md-5">
    <h3 class="fw-bold mb-4">Related Products</h3>
    <div class="row g-3 g-md-4">
      <?php foreach ($relatedProducts as $relatedProduct): ?>
      <div class="col-lg-3 col-md-6 col-6">
        <div class="product-card bg-white rounded-3 shadow-sm overflow-hidden h-100">
          <div class="product-image position-relative">
            <?php
            $productModel = new App\Models\Product();
            $primaryImage = $productModel->getPrimaryImage($relatedProduct['id']);
            $imageUrl = $primaryImage ? asset($primaryImage['image_path']) : asset('images/placeholder-product.jpg');
            ?>
            <img src="<?= $imageUrl ?>" alt="<?= e($relatedProduct['name']) ?>" 
                 class="img-fluid w-100" style="height: 200px; object-fit: cover;">
            
            <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
              <a href="<?= url('products/' . $relatedProduct['slug']) ?>" class="btn btn-primary btn-sm">
                View Details
              </a>
            </div>
          </div>
          
          <div class="product-info p-2 p-md-3">
            <h6 class="product-title mb-2 small">
              <a href="<?= url('products/' . $relatedProduct['slug']) ?>" class="text-decoration-none text-dark">
                <?= e($relatedProduct['name']) ?>
              </a>
            </h6>
            
            <div class="product-price">
              <span class="current-price fw-bold text-primary small">
                <?= formatPrice($relatedProduct['price']) ?>
              </span>
              <?php if ($relatedProduct['compare_price'] > $relatedProduct['price']): ?>
              <span class="original-price text-muted text-decoration-line-through ms-1 small">
                <?= formatPrice($relatedProduct['compare_price']) ?>
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</div>

<script>
  console.log('Product page JavaScript loaded');
  
  // Image gallery functionality
  function changeMainImage(src, thumbnail) {
    document.getElementById('main-product-image').src = src;
  
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-image').forEach(img => {
      img.classList.remove('active');
    });
    thumbnail.classList.add('active');
  }

  // Quantity controls
  function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const maxValue = parseInt(quantityInput.max);
    
    if (currentValue < maxValue) {
      quantityInput.value = currentValue + 1;
    }
  }

  function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    
    if (currentValue > 1) {
      quantityInput.value = currentValue - 1;
    }
  }

  // Add to cart functionality
  console.log('Setting up cart form listener');

  document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('Form submitted');
  
    const formData = new FormData(this);
    const button = this.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    // Debug form data
    for (let [key, value] of formData.entries()) {
      console.log(key + ': ' + value);
    }
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
    button.disabled = true;
    
    console.log('Fetching URL: <?= url("cart/add") ?>');
    
    fetch('<?= url("cart/add") ?>', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      console.log('Response status:', response.status);
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success) {
        // Show success message
        showAlert('success', 'Product added to cart successfully!');
        
        // Update cart count in header
        updateCartCount();
        
        // Reset button
        button.innerHTML = originalText;
        button.disabled = false;
      } else {
        showAlert('error', data.message || 'Failed to add product to cart');
        button.innerHTML = originalText;
        button.disabled = false;
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      console.error('Error details:', error.message);
      showAlert('error', 'An error occurred. Please try again.');
      button.innerHTML = originalText;
      button.disabled = false;
    });
  });

  // Alert function
  function showAlert(type, message) {
    const alertClass = type === 'error' ? 'danger' : type;
    const alertHtml = `
      <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
           style="top: 20px; right: 20px; z-index: 9999; min-width: 250px; max-width: 90vw;">
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
  </script>

  <!-- Sticky Bottom Bar for Mobile -->
  <div class="sticky-bottom-bar d-lg-none">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-3">
          <div class="quantity-controls">
            <div class="input-group input-group-sm">
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="decreaseMobileQuantity()">-</button>
              <input type="number" id="mobile-quantity" class="form-control form-control-sm text-center" 
                     value="1" min="1" max="<?= $product['stock_quantity'] ?>" 
                     <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="increaseMobileQuantity()">+</button>
            </div>
          </div>
        </div>
        <div class="col-9">
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success flex-fill mobile-add-to-cart-btn" 
                    data-product-id="<?= $product['id'] ?>"
                    <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
              <i class="fas fa-cart-plus me-1"></i>
              Add to Cart
            </button>
            <button type="button" class="btn btn-success flex-fill mobile-buy-now-btn" 
                    data-product-id="<?= $product['id'] ?>"
                    <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
              <i class="fas fa-shopping-cart me-1"></i>
              Buy Now
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Mobile quantity controls
    function decreaseMobileQuantity() {
      const input = document.getElementById('mobile-quantity');
      const currentValue = parseInt(input.value);
      if (currentValue > 1) {
        input.value = currentValue - 1;
        // Sync with desktop quantity input
        const desktopInput = document.getElementById('quantity');
        if (desktopInput) {
          desktopInput.value = input.value;
        }
      }
    }

    function increaseMobileQuantity() {
      const input = document.getElementById('mobile-quantity');
      const currentValue = parseInt(input.value);
      const maxValue = parseInt(input.getAttribute('max'));
      if (currentValue < maxValue) {
        input.value = currentValue + 1;
        // Sync with desktop quantity input
        const desktopInput = document.getElementById('quantity');
        if (desktopInput) {
          desktopInput.value = input.value;
        }
      }
    }

    // Sync quantity inputs
    document.addEventListener('DOMContentLoaded', function() {
      const mobileQuantity = document.getElementById('mobile-quantity');
      const desktopQuantity = document.getElementById('quantity');
      
      if (mobileQuantity && desktopQuantity) {
        // Sync mobile to desktop
        mobileQuantity.addEventListener('change', function() {
          desktopQuantity.value = this.value;
        });
        
        // Sync desktop to mobile
        desktopQuantity.addEventListener('change', function() {
          mobileQuantity.value = this.value;
        });
      }
    });

    // Mobile add to cart
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('mobile-add-to-cart-btn') || e.target.closest('.mobile-add-to-cart-btn')) {
        e.preventDefault();
        const button = e.target.classList.contains('mobile-add-to-cart-btn') ? e.target : e.target.closest('.mobile-add-to-cart-btn');
        const productId = button.getAttribute('data-product-id');
        const quantity = document.getElementById('mobile-quantity').value || 1;
        
        // Show loading state
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
        button.disabled = true;
        
        // Create form data
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('<?= csrfTokenName() ?>', '<?= csrfTokenValue() ?>');
        
        fetch('<?= url("cart/add") ?>', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showAlert('success', 'Added to cart!');
            updateCartCount();
          } else {
            showAlert('error', data.message || 'Failed to add to cart');
          }
        })
        .catch(error => {
          showAlert('error', 'An error occurred');
        })
        .finally(() => {
          button.innerHTML = originalText;
          button.disabled = false;
        });
      }
    });

    // Mobile buy now
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('mobile-buy-now-btn') || e.target.closest('.mobile-buy-now-btn')) {
        e.preventDefault();
        const button = e.target.classList.contains('mobile-buy-now-btn') ? e.target : e.target.closest('.mobile-buy-now-btn');
        const productId = button.getAttribute('data-product-id');
        const quantity = document.getElementById('mobile-quantity').value || 1;
        
        window.location.href = `<?= url('checkout') ?>?product=${productId}&quantity=${quantity}`;
      }
    });
  </script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
