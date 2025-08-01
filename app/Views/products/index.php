<?php
/**
 * Products Listing Page
 * Shows all products with filtering and pagination
 */

include APP_PATH . '/Views/layouts/header.php';

$selectedCategory = $selectedCategory ?? null;
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
  
  .display-6 {
    font-size: 1.75rem !important;
  }
  
  .filters-sidebar {
    position: sticky;
    top: 20px;
  }
  
  .filters-sidebar .card {
    margin-bottom: 1rem;
  }
  
  .filter-toggle {
    display: block;
    width: 100%;
    margin-bottom: 1rem;
  }
  
  .filters-sidebar .card-body {
    display: none;
  }
  
  .filters-sidebar .card-body.show {
    display: block;
  }
  
  .product-card {
    margin-bottom: 1rem;
  }
  
  .product-card img {
    height: 200px !important;
  }
  
  .product-info {
    padding: 1rem !important;
  }
  
  .product-title {
    font-size: 0.95rem;
    line-height: 1.3;
  }
  
  .product-description {
    font-size: 0.8rem;
  }
  
  .product-price .current-price {
    font-size: 1.1rem !important;
  }
  
  .btn-group .btn {
    padding: 0.5rem;
    font-size: 0.875rem;
  }
  
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }
  
  .view-toggle {
    margin-top: 1rem;
  }
  
  .results-info {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 1rem;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 10px;
    padding-right: 10px;
  }
  
  .display-6 {
    font-size: 1.5rem !important;
  }
  
  .col-md-8, .col-md-4 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .sort-controls {
    margin-top: 1rem;
    text-align: left !important;
  }
  
  .sort-controls .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .sort-controls label {
    margin-bottom: 0.5rem;
  }
  
  .form-select {
    width: 100% !important;
  }
  
  .product-item {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .product-card img {
    height: 180px !important;
  }
  
  .product-overlay .btn-group {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .product-overlay .btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
  
  .stock-info, .product-rating {
    font-size: 0.8rem;
  }
  
  .pagination .page-item {
    margin: 0.1rem;
  }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
  .btn {
    min-height: 44px;
  }
  
  .form-select, .form-control {
    min-height: 44px;
  }
  
  .list-group-item {
    min-height: 44px;
    display: flex;
    align-items: center;
  }
  
  .page-link {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

/* Collapsible filters for mobile */
.filter-toggle {
  display: none;
}

@media (max-width: 991px) {
  .filter-toggle {
    display: block;
  }
  
  .filters-sidebar .card-body {
    display: none;
  }
  
  .filters-sidebar .card-body.show {
    display: block;
  }
}
</style>

<div class="container py-3 py-md-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3 mb-md-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= $selectedCategory ? e($selectedCategory['name']) : 'All Products' ?>
      </li>
    </ol>
  </nav>
  
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-md-8 mb-3 mb-md-0">
      <h1 class="display-6 fw-bold">
        <?= $selectedCategory ? e($selectedCategory['name']) : 'All Products' ?>
      </h1>
      <?php if ($selectedCategory && $selectedCategory['description']): ?>
      <p class="lead text-muted"><?= e($selectedCategory['description']) ?></p>
      <?php endif; ?>
    </div>
    <div class="col-md-4 sort-controls">
      <div class="d-flex align-items-center justify-content-md-end">
        <label for="sort-select" class="form-label me-2 mb-0">Sort by:</label>
        <select id="sort-select" class="form-select" style="width: auto; min-width: 150px;">
          <option value="name" <?= $currentSort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
          <option value="price_low" <?= $currentSort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price_high" <?= $currentSort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="newest" <?= $currentSort === 'newest' ? 'selected' : '' ?>>Newest First</option>
          <option value="featured" <?= $currentSort === 'featured' ? 'selected' : '' ?>>Featured</option>
        </select>
      </div>
    </div>
  </div>
  
  <div class="row">
    <!-- Sidebar Filters -->
    <div class="col-lg-3 mb-4">
      <div class="filters-sidebar">
        <div class="card">
          <div class="card-header">
            <button class="btn btn-link p-0 w-100 text-start filter-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
              <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filters
                <i class="fas fa-chevron-down float-end mt-1"></i>
              </h6>
            </button>
            <h6 class="mb-0 d-none d-lg-block">
              <i class="fas fa-filter me-2"></i>
              Filters
            </h6>
          </div>
          <div class="card-body collapse d-lg-block" id="filtersCollapse">
            <!-- Categories Filter -->
            <div class="filter-group mb-4">
              <h6 class="fw-bold mb-3">Categories</h6>
              <div class="list-group list-group-flush">
                <a href="<?= url('products') ?>" 
                   class="list-group-item list-group-item-action border-0 px-0 <?= !$selectedCategory ? 'active' : '' ?>">
                  All Products
                </a>
                <?php foreach ($categories as $category): ?>
                <a href="<?= url('products/category/' . $category['slug']) ?>" 
                   class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center <?= $selectedCategory && $selectedCategory['id'] == $category['id'] ? 'active' : '' ?>">
                  <span><?= e($category['name']) ?></span>
                  <span class="badge bg-secondary"><?= $category['product_count'] ?></span>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Price Range Filter -->
            <div class="filter-group mb-4">
              <h6 class="fw-bold mb-3">Price Range</h6>
              <div class="row g-2">
                <div class="col-6">
                  <input type="number" class="form-control form-control-sm" placeholder="Min" id="price-min">
                </div>
                <div class="col-6">
                  <input type="number" class="form-control form-control-sm" placeholder="Max" id="price-max">
                </div>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100" id="apply-price-filter">
                Apply
              </button>
            </div>
            
            <!-- Availability Filter -->
            <div class="filter-group">
              <h6 class="fw-bold mb-3">Availability</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="in-stock" checked>
                <label class="form-check-label" for="in-stock">
                  In Stock Only
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Products Grid -->
    <div class="col-lg-9">
      <!-- Results Info -->
      <div class="d-flex justify-content-between align-items-center mb-4 results-info">
        <p class="text-muted mb-0">
          Showing <?= $products['from'] ?>-<?= $products['to'] ?> of <?= $products['total'] ?> products
        </p>
        <div class="view-toggle">
          <div class="btn-group" role="group" aria-label="View toggle">
            <button type="button" class="btn btn-outline-secondary active" id="grid-view">
              <i class="fas fa-th"></i>
              <span class="d-none d-sm-inline ms-1">Grid</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" id="list-view">
              <i class="fas fa-list"></i>
              <span class="d-none d-sm-inline ms-1">List</span>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Products -->
      <?php if (!empty($products['data'])): ?>
      <div class="products-grid row g-3 g-md-4" id="products-container">
        <?php foreach ($products['data'] as $product): ?>
        <div class="col-lg-4 col-md-6 col-sm-6 product-item">
          <div class="product-card bg-white rounded-3 shadow-sm overflow-hidden h-100">
            <div class="product-image position-relative">
              <?php
              $productModel = new App\Models\Product();
              $primaryImage = $productModel->getPrimaryImage($product['id']);
              $imageUrl = $primaryImage ? asset($primaryImage['image_path']) : asset('images/placeholder-product.jpg');
              ?>
              <img src="<?= $imageUrl ?>" alt="<?= e($product['name']) ?>" class="img-fluid w-100" style="height: 250px; object-fit: cover;" loading="lazy">
              
              <?php if ($product['compare_price'] > $product['price']): ?>
              <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                Sale
              </span>
              <?php endif; ?>
              
              <?php if ($product['featured']): ?>
              <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                Featured
              </span>
              <?php endif; ?>
              
              <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                <div class="btn-group">
                  <a href="<?= url('products/' . $product['slug']) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i>
                    <span class="d-none d-sm-inline ms-1">View</span>
                  </a>
                  <button type="button" class="btn btn-outline-primary btn-sm add-to-cart-btn" 
                          data-product-id="<?= $product['id'] ?>" 
                          <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                    <i class="fas fa-cart-plus"></i>
                    <span class="d-none d-sm-inline ms-1">Cart</span>
                  </button>
                </div>
              </div>
            </div>
            
            <div class="product-info p-3">
              <h6 class="product-title mb-2">
                <a href="<?= url('products/' . $product['slug']) ?>" class="text-decoration-none text-dark">
                  <?= e($product['name']) ?>
                </a>
              </h6>
              
              <p class="product-description text-muted small mb-3">
                <?= e(truncate($product['short_description'], 80)) ?>
              </p>
              
              <div class="product-price mb-3">
                <span class="current-price fw-bold text-primary fs-5">
                  <?= formatPrice($product['price']) ?>
                </span>
                <?php if ($product['compare_price'] > $product['price']): ?>
                <span class="original-price text-muted text-decoration-line-through ms-2">
                  <?= formatPrice($product['compare_price']) ?>
                </span>
                <?php endif; ?>
              </div>
              
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="stock-info">
                  <?php if ($product['stock_quantity'] > 0): ?>
                  <small class="text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    In Stock <span class="d-none d-sm-inline">(<?= $product['stock_quantity'] ?>)</span>
                  </small>
                  <?php else: ?>
                  <small class="text-danger">
                    <i class="fas fa-times-circle me-1"></i>
                    Out of Stock
                  </small>
                  <?php endif; ?>
                </div>
                
                <div class="product-rating">
                  <div class="stars text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                  </div>
                </div>
              </div>
              
              <form class="add-to-cart-form" data-product-id="<?= $product['id'] ?>">
                <?= csrfField() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary w-100" <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                  <i class="fas fa-cart-plus me-2"></i>
                  <?= $product['stock_quantity'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <!-- Pagination -->
      <?php if ($products['last_page'] > 1): ?>
      <nav aria-label="Products pagination" class="mt-4 mt-md-5">
        <ul class="pagination justify-content-center">
          <!-- Previous Page -->
          <?php if ($products['current_page'] > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $products['current_page'] - 1 ?>&sort=<?= $currentSort ?>" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          <?php endif; ?>
          
          <!-- Page Numbers -->
          <?php
          $start = max(1, $products['current_page'] - 2);
          $end = min($products['last_page'], $products['current_page'] + 2);
          
          for ($i = $start; $i <= $end; $i++):
          ?>
          <li class="page-item <?= $i === $products['current_page'] ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&sort=<?= $currentSort ?>"><?= $i ?></a>
          </li>
          <?php endfor; ?>
          
          <!-- Next Page -->
          <?php if ($products['current_page'] < $products['last_page']): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $products['current_page'] + 1 ?>&sort=<?= $currentSort ?>" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
      <?php endif; ?>
      
      <?php else: ?>
      <!-- No Products Found -->
      <div class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4>No products found</h4>
        <p class="text-muted">Try adjusting your filters or search criteria.</p>
        <a href="<?= url('products') ?>" class="btn btn-primary">View All Products</a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Sort functionality
  document.getElementById('sort-select').addEventListener('change', function() {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('sort', this.value);
    currentUrl.searchParams.delete('page'); // Reset to first page
    window.location.href = currentUrl.toString();
  });
  
  // View toggle functionality
  const gridView = document.getElementById('grid-view');
  const listView = document.getElementById('list-view');
  const productsContainer = document.getElementById('products-container');
  
  gridView.addEventListener('click', function() {
    gridView.classList.add('active');
    listView.classList.remove('active');
    productsContainer.className = 'products-grid row g-3 g-md-4';
    document.querySelectorAll('.product-item').forEach(item => {
      item.className = 'col-lg-4 col-md-6 col-sm-6 product-item';
    });
  });
  
  listView.addEventListener('click', function() {
    listView.classList.add('active');
    gridView.classList.remove('active');
    productsContainer.className = 'products-list';
    document.querySelectorAll('.product-item').forEach(item => {
      item.className = 'col-12 product-item mb-3';
    });
  });
  
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
  
  // Auto-collapse filters on mobile after selection
  if (window.innerWidth <= 991) {
    document.querySelectorAll('.list-group-item').forEach(item => {
      item.addEventListener('click', function() {
        const collapse = document.getElementById('filtersCollapse');
        if (collapse && collapse.classList.contains('show')) {
          const bsCollapse = new bootstrap.Collapse(collapse);
          bsCollapse.hide();
        }
      });
    });
  }
});
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
