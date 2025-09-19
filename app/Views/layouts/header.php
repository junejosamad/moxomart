<?php
/**
 * Main Header Layout
 * Includes navigation, meta tags, and common HTML structure
 */

$siteName = getSetting('site_name', 'Moxo Mart');
$cartCount = getCartCount();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <!-- SEO Meta Tags -->
  <title><?= e($meta['title'] ?? 'Moxo Mart - Your Trusted E-commerce Partner') ?></title>
  <meta name="description" content="<?= e($meta['description'] ?? 'Discover quality products at Moxo Mart') ?>">
  <meta name="keywords" content="ecommerce, online shopping, electronics, clothing, home goods, moxo mart">
  <meta name="author" content="Moxo Mart">
  <link rel="canonical" href="<?= url($_SERVER['REQUEST_URI']) ?>">
  
  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="<?= e($meta['og_title'] ?? $meta['title'] ?? 'Moxo Mart') ?>">
  <meta property="og:description" content="<?= e($meta['og_description'] ?? $meta['description'] ?? '') ?>">
  <meta property="og:image" content="<?= $meta['og_image'] ?? asset('images/og-default.jpg') ?>">
  <meta property="og:url" content="<?= $meta['og_url'] ?? url($_SERVER['REQUEST_URI']) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= e($siteName) ?>">
  
  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($meta['twitter_title'] ?? $meta['title'] ?? 'Moxo Mart') ?>">
  <meta name="twitter:description" content="<?= e($meta['twitter_description'] ?? $meta['description'] ?? '') ?>">
  <meta name="twitter:image" content="<?= $meta['twitter_image'] ?? asset('images/og-default.jpg') ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
  <link rel="alternate icon" href="<?= asset('images/favicon.ico') ?>">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- CSS - Multiple fallback methods -->
  <!-- Primary: Generated URL -->
  <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet" id="css-primary">
  
  <!-- Fallback 1: Direct path -->
  <link href="/assets/css/main.min.css" rel="stylesheet" id="css-fallback1">
  
  <!-- Fallback 2: Full HTTP URL -->
  <link href="http://<?= $_SERVER['HTTP_HOST'] ?>/assets/css/main.min.css" rel="stylesheet" id="css-fallback2">
  
  <!-- External CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  
  <!-- CSS Load Detection -->
  <script>
  // Remove fallback CSS links if primary loads successfully
  window.addEventListener('load', function() {
    setTimeout(function() {
      var primaryLoaded = false;
      try {
        var sheets = document.styleSheets;
        for (var i = 0; i < sheets.length; i++) {
          if (sheets[i].href && sheets[i].href.indexOf('main.min.css') !== -1) {
            primaryLoaded = true;
            break;
          }
        }
        
        if (primaryLoaded) {
          // Remove fallback links to avoid duplicate CSS
          document.getElementById('css-fallback1').remove();
          document.getElementById('css-fallback2').remove();
        }
      } catch(e) {}
    }, 1000);
  });
  </script>
  
  <!-- Structured Data -->
  <?php if (isset($structuredData)): ?>
  <script type="application/ld+json">
    <?= $structuredData ?>
  </script>
  <?php endif; ?>
  
  <!-- Organization Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "<?= e($siteName) ?>",
    "url": "<?= url() ?>",
    "logo": "<?= asset('images/logo.jpg') ?>",
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "<?= getSetting('contact_phone', '+92-345-9123456') ?>",
      "contactType": "customer service"
    },
    "sameAs": [
      "<?= getSetting('social_facebook', '#') ?>",
      "<?= getSetting('social_twitter', '#') ?>",
      "<?= getSetting('social_instagram', '#') ?>"
    ]
  }
  </script>
</head>
<body class="<?= isset($pageClass) ? $pageClass : '' ?>">
  <!-- Skip to main content for accessibility -->
  <a href="#main-content" class="visually-hidden-focusable">Skip to main content</a>
  
  <!-- Top Bar (hidden on mobile) -->
  <div class="top-bar bg-dark text-white py-2 d-none d-lg-block">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <small>
            <i class="fas fa-phone me-2"></i>
            Support: +92-300-1234567 - +92-3306986088
          </small>
        </div>
        <div class="col-md-6 text-end">
          <small>
            <i class="fas fa-envelope me-2"></i>
            contact@moxomart.com – cheema@bytecraftsoft.com
            <span class="mx-2">|</span>
            <span>Developed and Powered by <a href="https://bytecraftsoft.com" target="_blank" class="text-white">bytecraftsoft.com</a></span>
          </small>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
      <!-- Brand Logo -->
      <a class="navbar-brand d-flex align-items-center" href="<?= url() ?>">
        <img src="<?= asset('images/logo.jpg') ?>" alt="<?= e($siteName) ?>" height="45" class="me-2">
      </a>
      
      <!-- Search Form - Center -->
      <div class="flex-grow-1 mx-4 d-none d-lg-block">
        <form class="search-form" action="<?= url('search') ?>" method="GET">
          <div class="input-group search-input-group">
            <select class="form-select search-category" name="category" style="max-width: 150px;">
              <option value="">All Categories</option>
              <?php
              $categoryModel = new App\Models\Category();
              $categories = $categoryModel->getMainCategories();
              foreach ($categories as $category):
              ?>
              <option value="<?= $category['id'] ?>" <?= ($_GET['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                <?= e($category['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
            <input class="form-control search-input" type="search" name="q" placeholder="Search for products, categories, sku..." aria-label="Search" value="<?= e($_GET['q'] ?? '') ?>">
            <button class="btn btn-success search-btn" type="submit" aria-label="Search">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
      
      <!-- Mobile User Actions (visible only on mobile) -->
      <div class="d-flex align-items-center d-lg-none mobile-user-actions">
        <!-- Mobile Wishlist -->
        <a class="nav-link me-2 position-relative mobile-action-btn" href="<?= url('wishlist') ?>" aria-label="Wishlist">
          <i class="fas fa-heart fa-lg"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success mobile-badge">
            0
          </span>
        </a>
        
        <!-- Mobile Shopping Cart -->
        <a class="nav-link me-2 position-relative mobile-action-btn" href="<?= url('cart') ?>" aria-label="Shopping Cart">
          <i class="fas fa-shopping-cart fa-lg"></i>
          <?php if ($cartCount > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success mobile-badge">
            <?= $cartCount ?>
          </span>
          <?php endif; ?>
        </a>
        
        <!-- Mobile Menu Toggle -->
        <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavMenu" aria-controls="mobileNavMenu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Desktop User Actions (hidden on mobile) -->
      <div class="d-none d-lg-flex align-items-center">
        <!-- User Account -->
        <?php if ($currentUser): ?>
        <div class="dropdown me-3">
          <a class="nav-link dropdown-toggle d-flex align-items-center text-decoration-none" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle fa-lg me-1"></i>
            <div>
              <small class="text-muted d-block">My Account</small>
              <small class="fw-bold"><?= e($currentUser['first_name']) ?></small>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="<?= url('dashboard') ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
            <li><a class="dropdown-item" href="<?= url('dashboard/orders') ?>"><i class="fas fa-box me-2"></i>My Orders</a></li>
            <li><a class="dropdown-item" href="<?= url('dashboard/profile') ?>"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <?php if (isAdmin()): ?>
            <li><a class="dropdown-item" href="<?= url('admin') ?>"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
            <li><hr class="dropdown-divider"></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
          </ul>
        </div>
        <?php else: ?>
        <a class="nav-link me-3 d-flex align-items-center text-decoration-none" href="<?= url('login') ?>">
          <i class="fas fa-user-circle fa-lg me-1"></i>
          <div>
            <small class="text-muted d-block">My Account</small>
            <small class="fw-bold">Login</small>
          </div>
        </a>
        <?php endif; ?>
        
        <!-- Wishlist -->
        <a class="nav-link me-3 d-flex align-items-center text-decoration-none position-relative" href="<?= url('wishlist') ?>">
          <i class="fas fa-heart fa-lg me-1"></i>
          <div>
            <small class="text-muted d-block">Wishlist</small>
            <small class="fw-bold">0</small>
          </div>
        </a>
        
        <!-- Shopping Cart -->
        <a class="nav-link d-flex align-items-center text-decoration-none position-relative" href="<?= url('cart') ?>">
          <i class="fas fa-shopping-cart fa-lg me-1"></i>
          <div>
            <small class="text-muted d-block">Cart</small>
            <small class="fw-bold"><?= $cartCount ?></small>
          </div>
          <?php if ($cartCount > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
            <?= $cartCount ?>
          </span>
          <?php endif; ?>
        </a>
      </div>
    </div>
    
  </nav>

  <!-- Mobile Navigation Menu (Full Screen Overlay) -->
  <div class="collapse navbar-collapse mobile-nav-menu" id="mobileNavMenu">
    <div class="mobile-nav-content">
      <!-- Mobile Header -->
      <div class="mobile-nav-header">
        <div class="d-flex justify-content-between align-items-center p-3">
          <img src="<?= asset('images/logo.jpg') ?>" alt="<?= e($siteName) ?>" height="35">
          <button type="button" class="btn-close mobile-nav-close" data-bs-toggle="collapse" data-bs-target="#mobileNavMenu" aria-label="Close"></button>
        </div>
      </div>
      
      <!-- Mobile Search -->
      <div class="mobile-search p-3">
        <form action="<?= url('search') ?>" method="GET">
          <div class="input-group">
            <select class="form-select" name="category" style="max-width: 100px;">
              <option value="">All</option>
              <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>" <?= ($_GET['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                <?= e($category['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
            <input class="form-control" type="search" name="q" placeholder="Search products..." aria-label="Search" value="<?= e($_GET['q'] ?? '') ?>">
            <button class="btn btn-success" type="submit" aria-label="Search">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>

      <!-- User Account Section -->
      <div class="mobile-user-section">
        <?php if ($currentUser): ?>
        <div class="user-info p-3 bg-light">
          <div class="d-flex align-items-center">
            <i class="fas fa-user-circle fa-2x me-3 text-success"></i>
            <div>
              <h6 class="mb-0">Welcome, <?= e($currentUser['first_name']) ?>!</h6>
              <small class="text-muted"><?= e($currentUser['email']) ?></small>
            </div>
          </div>
        </div>
        <ul class="mobile-nav-links">
          <li><a href="<?= url('dashboard') ?>"><i class="fas fa-tachometer-alt me-3"></i>Dashboard</a></li>
          <li><a href="<?= url('dashboard/orders') ?>"><i class="fas fa-box me-3"></i>My Orders</a></li>
          <li><a href="<?= url('dashboard/profile') ?>"><i class="fas fa-user-edit me-3"></i>Profile</a></li>
          <?php if (isAdmin()): ?>
          <li><a href="<?= url('admin') ?>"><i class="fas fa-cog me-3"></i>Admin Panel</a></li>
          <?php endif; ?>
          <li><a href="<?= url('logout') ?>" class="text-danger"><i class="fas fa-sign-out-alt me-3"></i>Logout</a></li>
        </ul>
        <?php else: ?>
        <div class="login-section p-3">
          <a href="<?= url('login') ?>" class="btn btn-success w-100 mb-2">
            <i class="fas fa-sign-in-alt me-2"></i>Login
          </a>
          <a href="<?= url('register') ?>" class="btn btn-outline-success w-100">
            <i class="fas fa-user-plus me-2"></i>Register
          </a>
        </div>
        <?php endif; ?>
      </div>

      <!-- Navigation Links -->
      <ul class="mobile-nav-links">
        <li><a href="<?= url() ?>" class="<?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>"><i class="fas fa-home me-3"></i>Home</a></li>
        <li><a href="<?= url('products') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], '/products') === 0 ? 'active' : '' ?>"><i class="fas fa-shopping-bag me-3"></i>Shop</a></li>
        <li>
          <a href="#" class="mobile-nav-toggle" data-target="categories">
            <i class="fas fa-list me-3"></i>Categories
            <i class="fas fa-chevron-down ms-auto"></i>
          </a>
          <ul class="mobile-nav-submenu" id="categories">
            <?php foreach ($categories as $category): ?>
            <li><a href="<?= url('products/category/' . $category['slug']) ?>"><?= e($category['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li><a href="<?= url('wishlist') ?>"><i class="fas fa-heart me-3"></i>Wishlist <span class="badge bg-success ms-auto">0</span></a></li>
        <li><a href="<?= url('cart') ?>"><i class="fas fa-shopping-cart me-3"></i>Cart <span class="badge bg-success ms-auto"><?= $cartCount ?></span></a></li>
        <li><a href="<?= url('contact') ?>"><i class="fas fa-phone me-3"></i>Contact</a></li>
      </ul>

      <!-- Mobile Footer -->
      <div class="mobile-nav-footer p-3 mt-auto">
        <div class="text-center">
          <div class="mb-2">
            <small class="text-muted">Follow us:</small>
          </div>
          <div class="d-flex justify-content-center gap-3">
            <a href="<?= getSetting('social_facebook', '#') ?>" class="text-muted">
              <i class="fab fa-facebook-f fa-lg"></i>
            </a>
            <a href="<?= getSetting('social_twitter', '#') ?>" class="text-muted">
              <i class="fab fa-twitter fa-lg"></i>
            </a>
            <a href="<?= getSetting('social_instagram', '#') ?>" class="text-muted">
              <i class="fab fa-instagram fa-lg"></i>
            </a>
            <a href="<?= getSetting('social_whatsapp', '#') ?>" class="text-muted">
              <i class="fab fa-whatsapp fa-lg"></i>
            </a>
          </div>
          <div class="mt-2">
            <small class="text-muted">
              <i class="fas fa-phone me-1"></i>+92-300-1234567
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Mobile Navigation Overlay -->
  <div class="mobile-nav-overlay d-lg-none"></div>

  <!-- Standard Navigation (keeping the category nav for desktop) -->
  
  <!-- Category Navigation Bar (hidden on mobile) -->
  <div class="category-nav bg-success d-none d-lg-block">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-3">
          <div class="dropdown">
            <button class="btn btn-dark dropdown-toggle w-100 text-start" type="button" id="categoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-bars me-2"></i>
              Shopping By Categories
            </button>
            <ul class="dropdown-menu w-100" aria-labelledby="categoriesDropdown">
              <?php foreach ($categories as $category): ?>
              <li><a class="dropdown-item" href="<?= url('products/category/' . $category['slug']) ?>">
                <i class="fas fa-angle-right me-2"></i><?= e($category['name']) ?>
              </a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="col-md-9">
          <ul class="nav nav-pills category-pills">
            <li class="nav-item">
              <a class="nav-link text-white <?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>" href="<?= url() ?>">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white <?= strpos($_SERVER['REQUEST_URI'], '/products') === 0 ? 'active' : '' ?>" href="<?= url('products') ?>">Shop</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Flash Messages -->
  <?php
  $flashTypes = ['success', 'error', 'warning', 'info'];
  foreach ($flashTypes as $type):
    $message = getFlash($type);
    if ($message):
      $alertClass = $type === 'error' ? 'danger' : $type;
  ?>
  <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show mb-0" role="alert">
    <div class="container">
      <?= e($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  <?php 
    endif;
  endforeach; 
  ?>
  
  <!-- News Ticker -->
  <div class="news-ticker bg-light border-bottom">
    <div class="container">
      <div class="d-flex align-items-center py-2">
        <span class="badge bg-danger me-3">News</span>
        <div class="ticker-content">
          <span class="ticker-item">Welcome to Moxo Mart - Your trusted e-commerce partner!</span>
          <span class="ticker-item">Free shipping on orders over PKR 2000</span>
          <span class="ticker-item">24/7 Customer support available</span>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <main id="main-content">
