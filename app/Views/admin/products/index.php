<?php
/**
 * Admin Products List Template
 * Manage all products with CRUD operations
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Admin - SadaCart</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  
  <div class="d-flex">
    <!-- Sidebar -->
    <?php include APP_PATH . '/Views/layouts/admin-sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px;">
      <!-- Top Navigation -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
          <h4 class="mb-0">Products</h4>
          
          <div class="navbar-nav ms-auto">
            <a href="<?= url('admin/products/create') ?>" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>Add Product
            </a>
          </div>
        </div>
      </nav>
      
      <!-- Products Content -->
      <div class="container-fluid p-4">
        <!-- Filters -->
        <div class="card mb-4">
          <div class="card-body">
            <form method="GET" class="row g-3">
              <div class="col-md-3">
                <input type="text" class="form-control" name="search" 
                       placeholder="Search products..." 
                       value="<?= e($_GET['search'] ?? '') ?>">
              </div>
              <div class="col-md-2">
                <select class="form-select" name="category">
                  <option value="">All Categories</option>
                  <?php foreach ($categories as $category): ?>
                  <option value="<?= $category['id'] ?>" <?= ($_GET['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <select class="form-select" name="status">
                  <option value="">All Status</option>
                  <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                  <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
              </div>
              <div class="col-md-2">
                <select class="form-select" name="sort">
                  <option value="name" <?= ($_GET['sort'] ?? '') === 'name' ? 'selected' : '' ?>>Name</option>
                  <option value="created_at" <?= ($_GET['sort'] ?? '') === 'created_at' ? 'selected' : '' ?>>Date Created</option>
                  <option value="price" <?= ($_GET['sort'] ?? '') === 'price' ? 'selected' : '' ?>>Price</option>
                  <option value="stock" <?= ($_GET['sort'] ?? '') === 'stock' ? 'selected' : '' ?>>Stock</option>
                </select>
              </div>
              <div class="col-md-3">
                <div class="btn-group w-100">
                  <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i>Filter
                  </button>
                  <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Products Table -->
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
              Products (<?= $products['total'] ?> total)
            </h6>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-outline-secondary" onclick="bulkAction('activate')">
                <i class="fas fa-check me-1"></i>Activate Selected
              </button>
              <button type="button" class="btn btn-outline-secondary" onclick="bulkAction('deactivate')">
                <i class="fas fa-times me-1"></i>Deactivate Selected
              </button>
              <button type="button" class="btn btn-outline-danger" onclick="bulkAction('delete')">
                <i class="fas fa-trash me-1"></i>Delete Selected
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            <?php if (!empty($products['data'])): ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th width="50">
                      <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="120">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($products['data'] as $product): ?>
                  <tr>
                    <td>
                      <input type="checkbox" class="form-check-input product-checkbox" 
                             value="<?= $product['id'] ?>">
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="me-3">
                          <?php
                          $productModel = new App\Models\Product();
                          $primaryImage = $productModel->getPrimaryImage($product['id']);
                          $imageUrl = $primaryImage ? asset($primaryImage['image_path']) : asset('images/placeholder-product.jpg');
                          ?>
                          <img src="<?= $imageUrl ?>" alt="<?= e($product['name']) ?>" 
                               class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                        <div>
                          <h6 class="mb-1">
                            <a href="<?= url('admin/products/' . $product['id'] . '/edit') ?>" 
                               class="text-decoration-none">
                              <?= e($product['name']) ?>
                            </a>
                          </h6>
                          <small class="text-muted">SKU: <?= e($product['sku']) ?></small>
                          <?php if ($product['featured']): ?>
                          <span class="badge bg-primary ms-2">Featured</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?php if ($product['category_name']): ?>
                      <span class="badge bg-light text-dark"><?= e($product['category_name']) ?></span>
                      <?php else: ?>
                      <span class="text-muted">Uncategorized</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <strong><?= formatPrice($product['price']) ?></strong>
                      <?php if ($product['compare_price'] > $product['price']): ?>
                      <br><small class="text-muted text-decoration-line-through">
                        <?= formatPrice($product['compare_price']) ?>
                      </small>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($product['stock_quantity'] > 0): ?>
                      <span class="text-success"><?= $product['stock_quantity'] ?></span>
                      <?php else: ?>
                      <span class="text-danger">Out of Stock</span>
                      <?php endif; ?>
                      
                      <?php if ($product['stock_quantity'] <= $product['low_stock_threshold']): ?>
                      <br><small class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                      </small>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-<?= getProductStatusColor($product['status']) ?>">
                        <?= ucfirst($product['status']) ?>
                      </span>
                    </td>
                    <td>
                      <small><?= formatDate($product['created_at']) ?></small>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <a href="<?= url('products/' . $product['slug']) ?>" 
                           class="btn btn-outline-info" 
                           title="View Product" 
                           target="_blank">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= url('admin/products/' . $product['id'] . '/edit') ?>" 
                           class="btn btn-outline-primary" 
                           title="Edit Product">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                title="Delete Product"
                                onclick="deleteProduct(<?= $product['id'] ?>, '<?= e($product['name']) ?>')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($products['last_page'] > 1): ?>
            <div class="card-footer">
              <nav aria-label="Products pagination">
                <ul class="pagination justify-content-center mb-0">
                  <?php if ($products['current_page'] > 1): ?>
                  <li class="page-item">
                    <a class="page-link" href="?page=<?= $products['current_page'] - 1 ?><?= http_build_query(array_filter($_GET)) ? '&' . http_build_query(array_filter($_GET)) : '' ?>">
                      Previous
                    </a>
                  </li>
                  <?php endif; ?>
                  
                  <?php
                  $start = max(1, $products['current_page'] - 2);
                  $end = min($products['last_page'], $products['current_page'] + 2);
                  
                  for ($i = $start; $i <= $end; $i++):
                  ?>
                  <li class="page-item <?= $i === $products['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= http_build_query(array_filter($_GET)) ? '&' . http_build_query(array_filter($_GET)) : '' ?>">
                      <?= $i ?>
                    </a>
                  </li>
                  <?php endfor; ?>
                  
                  <?php if ($products['current_page'] < $products['last_page']): ?>
                  <li class="page-item">
                    <a class="page-link" href="?page=<?= $products['current_page'] + 1 ?><?= http_build_query(array_filter($_GET)) ? '&' . http_build_query(array_filter($_GET)) : '' ?>">
                      Next
                    </a>
                  </li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-5">
              <i class="fas fa-box fa-3x text-muted mb-3"></i>
              <h5>No products found</h5>
              <p class="text-muted">Get started by adding your first product.</p>
              <a href="<?= url('admin/products/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Product
              </a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Select all checkbox functionality
    document.getElementById('select-all').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.product-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });
    
    // Delete product
    function deleteProduct(id, name) {
      if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/${id}/delete`;
        
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
      }
    }
    
    // Bulk actions
    function bulkAction(action) {
      const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                                   .map(checkbox => checkbox.value);
      
      if (selectedProducts.length === 0) {
        alert('Please select at least one product.');
        return;
      }
      
      let confirmMessage = '';
      switch (action) {
        case 'activate':
          confirmMessage = `Activate ${selectedProducts.length} selected products?`;
          break;
        case 'deactivate':
          confirmMessage = `Deactivate ${selectedProducts.length} selected products?`;
          break;
        case 'delete':
          confirmMessage = `Delete ${selectedProducts.length} selected products? This action cannot be undone.`;
          break;
      }
      
      if (confirm(confirmMessage)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/products/bulk-action';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= generateCsrfToken() ?>';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        
        selectedProducts.forEach(id => {
          const productInput = document.createElement('input');
          productInput.type = 'hidden';
          productInput.name = 'products[]';
          productInput.value = id;
          form.appendChild(productInput);
        });
        
        form.appendChild(csrfInput);
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
</body>
</html>

<?php
function getProductStatusColor($status) {
  $colors = [
    'active' => 'success',
    'inactive' => 'secondary',
    'draft' => 'warning'
  ];
  return $colors[$status] ?? 'secondary';
}
?>
