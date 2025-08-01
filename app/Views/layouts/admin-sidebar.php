<?php
/**
 * Admin Sidebar Layout
 * Navigation sidebar for admin panel
 */

$currentPath = $_SERVER['REQUEST_URI'];
?>
<div class="admin-sidebar bg-dark text-light">
  <div class="sidebar-header p-3 border-bottom border-secondary">
    <h5 class="mb-0">
      <i class="fas fa-cog me-2"></i>
      Admin Panel
    </h5>
  </div>
  
  <nav class="sidebar-nav">
    <ul class="nav flex-column">
      <!-- Dashboard -->
      <li class="nav-item">
        <a class="nav-link text-light <?= $currentPath === '/admin' ? 'active bg-primary' : '' ?>" href="<?= url('admin') ?>">
          <i class="fas fa-tachometer-alt me-2"></i>
          Dashboard
        </a>
      </li>
      
      <!-- Products -->
      <li class="nav-item">
        <a class="nav-link text-light <?= strpos($currentPath, '/admin/products') === 0 ? 'active bg-primary' : '' ?>" 
           href="#" data-bs-toggle="collapse" data-bs-target="#products-menu" aria-expanded="false">
          <i class="fas fa-box me-2"></i>
          Products
          <i class="fas fa-chevron-down ms-auto"></i>
        </a>
        <div class="collapse <?= strpos($currentPath, '/admin/products') === 0 ? 'show' : '' ?>" id="products-menu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a class="nav-link text-light" href="<?= url('admin/products') ?>">
                <i class="fas fa-list me-2"></i>All Products
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-light" href="<?= url('admin/products/create') ?>">
                <i class="fas fa-plus me-2"></i>Add Product
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-light" href="<?= url('admin/categories') ?>">
                <i class="fas fa-tags me-2"></i>Categories
              </a>
            </li>
          </ul>
        </div>
      </li>
      
      <!-- Orders -->
      <li class="nav-item">
        <a class="nav-link text-light <?= strpos($currentPath, '/admin/orders') === 0 ? 'active bg-primary' : '' ?>" href="<?= url('admin/orders') ?>">
          <i class="fas fa-shopping-cart me-2"></i>
          Orders
          <?php
          $orderModel = new App\Models\Order();
          $pendingCount = $orderModel->count(['status' => 'pending']);
          if ($pendingCount > 0):
          ?>
          <span class="badge bg-danger ms-auto"><?= $pendingCount ?></span>
          <?php endif; ?>
        </a>
      </li>
      
      <!-- Users -->
      <li class="nav-item">
        <a class="nav-link text-light <?= strpos($currentPath, '/admin/users') === 0 ? 'active bg-primary' : '' ?>" href="<?= url('admin/users') ?>">
          <i class="fas fa-users me-2"></i>
          Users
        </a>
      </li>
      
      <!-- Blog -->
      <li class="nav-item">
        <a class="nav-link text-light <?= strpos($currentPath, '/admin/blog') === 0 ? 'active bg-primary' : '' ?>" 
           href="#" data-bs-toggle="collapse" data-bs-target="#blog-menu" aria-expanded="false">
          <i class="fas fa-blog me-2"></i>
          Blog
          <i class="fas fa-chevron-down ms-auto"></i>
        </a>
        <div class="collapse <?= strpos($currentPath, '/admin/blog') === 0 ? 'show' : '' ?>" id="blog-menu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a class="nav-link text-light" href="<?= url('admin/blog') ?>">
                <i class="fas fa-list me-2"></i>All Posts
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-light" href="<?= url('admin/blog/create') ?>">
                <i class="fas fa-plus me-2"></i>New Post
              </a>
            </li>
          </ul>
        </div>
      </li>
      
      <!-- Analytics -->
      <li class="nav-item">
        <a class="nav-link text-light <?= $currentPath === '/admin/analytics' ? 'active bg-primary' : '' ?>" href="<?= url('admin/analytics') ?>">
          <i class="fas fa-chart-bar me-2"></i>
          Analytics
        </a>
      </li>
      
      <!-- Settings -->
      <li class="nav-item">
        <a class="nav-link text-light <?= $currentPath === '/admin/settings' ? 'active bg-primary' : '' ?>" href="<?= url('admin/settings') ?>">
          <i class="fas fa-cog me-2"></i>
          Settings
        </a>
      </li>
      
      <!-- Activity Log -->
      <li class="nav-item">
        <a class="nav-link text-light <?= $currentPath === '/admin/activity' ? 'active bg-primary' : '' ?>" href="<?= url('admin/activity') ?>">
          <i class="fas fa-history me-2"></i>
          Activity Log
        </a>
      </li>
    </ul>
  </nav>
  
  <!-- Sidebar Footer -->
  <div class="sidebar-footer p-3 border-top border-secondary mt-auto">
    <div class="d-flex align-items-center">
      <div class="flex-grow-1">
        <small class="text-muted">Logged in as:</small><br>
        <strong><?= e(getCurrentUser()['first_name'] ?? 'Admin') ?></strong>
      </div>
      <a href="<?= url('logout') ?>" class="btn btn-outline-light btn-sm" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </div>
</div>

<style>
.admin-sidebar {
  width: 250px;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  overflow-y: auto;
  z-index: 1000;
}

.admin-sidebar .nav-link {
  padding: 0.75rem 1rem;
  border-radius: 0;
  transition: all 0.3s ease;
}

.admin-sidebar .nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.admin-sidebar .nav-link.active {
  background-color: var(--bs-primary);
}

.sidebar-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
}

@media (max-width: 768px) {
  .admin-sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .admin-sidebar.show {
    transform: translateX(0);
  }
}
</style>
