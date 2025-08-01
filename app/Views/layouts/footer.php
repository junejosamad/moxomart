<?php
/**
 * Main Footer Layout
 * Includes footer content, links, and closing HTML tags
 */

$siteName = getSetting('site_name', 'Moxo Mart');
$currentYear = date('Y');
?>
  </main>
  
  <!-- Footer -->
  <footer class="bg-dark text-light mt-5">
    <!-- Main Footer Content -->
    <div class="container py-5">
      <div class="row">
        <!-- Company Info -->
        <div class="col-lg-4 col-md-6 mb-4">
          <h5 class="fw-bold text-success mb-3"><?= e($siteName) ?></h5>
          <p class="text-muted">
            <?= getSetting('site_description', 'Your trusted e-commerce partner for quality products and exceptional service. We made e-commerce easy.') ?>
          </p>
          <div class="d-flex gap-3 mt-3">
            <a href="<?= getSetting('social_facebook', '#') ?>" class="text-light" aria-label="Facebook">
              <i class="fab fa-facebook-f fa-lg"></i>
            </a>
            <a href="<?= getSetting('social_twitter', '#') ?>" class="text-light" aria-label="Twitter">
              <i class="fab fa-twitter fa-lg"></i>
            </a>
            <a href="<?= getSetting('social_instagram', '#') ?>" class="text-light" aria-label="Instagram">
              <i class="fab fa-instagram fa-lg"></i>
            </a>
            <a href="<?= getSetting('social_whatsapp', '#') ?>" class="text-light" aria-label="WhatsApp">
              <i class="fab fa-whatsapp fa-lg"></i>
            </a>
          </div>
        </div>
        
        <!-- Quick Links -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">Quick Links</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="<?= url() ?>" class="text-muted text-decoration-none">Home</a></li>
            <li class="mb-2"><a href="<?= url('products') ?>" class="text-muted text-decoration-none">Shop</a></li>
            <li class="mb-2"><a href="<?= url('how-to') ?>" class="text-muted text-decoration-none">How to</a></li>
            <li class="mb-2"><a href="<?= url('fbs') ?>" class="text-muted text-decoration-none">FBS</a></li>
            <li class="mb-2"><a href="<?= url('affiliate') ?>" class="text-muted text-decoration-none">Affiliate Program</a></li>
          </ul>
        </div>
        
        <!-- Categories -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">Categories</h6>
          <ul class="list-unstyled">
            <?php
            $categoryModel = new App\Models\Category();
            $footerCategories = $categoryModel->getMainCategories();
            foreach (array_slice($footerCategories, 0, 5) as $category):
            ?>
            <li class="mb-2">
              <a href="<?= url('products/category/' . $category['slug']) ?>" class="text-muted text-decoration-none">
                <?= e($category['name']) ?>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        
        <!-- Customer Service -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">Customer Service</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="<?= url('faq') ?>" class="text-muted text-decoration-none">FAQ</a></li>
            <li class="mb-2"><a href="<?= url('shipping-info') ?>" class="text-muted text-decoration-none">Shipping Info</a></li>
            <li class="mb-2"><a href="<?= url('returns') ?>" class="text-muted text-decoration-none">Returns</a></li>
            <li class="mb-2"><a href="<?= url('privacy-policy') ?>" class="text-muted text-decoration-none">Privacy Policy</a></li>
            <li class="mb-2"><a href="<?= url('terms') ?>" class="text-muted text-decoration-none">Terms of Service</a></li>
          </ul>
        </div>
        
        <!-- Contact Info -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">Contact Info</h6>
          <div class="text-muted">
            <div class="mb-2">
              <i class="fas fa-map-marker-alt me-2"></i>
              <small><?= getSetting('contact_address', 'Karachi, Pakistan') ?></small>
            </div>
            <div class="mb-2">
              <i class="fas fa-phone me-2"></i>
              <small><?= getSetting('contact_phone', '+92-345-9123456') ?></small>
            </div>
            <div class="mb-2">
              <i class="fas fa-envelope me-2"></i>
              <small><?= getSetting('contact_email', 'support@moxomart.com') ?></small>
            </div>
            <div class="mb-2">
              <i class="fab fa-whatsapp me-2"></i>
              <small>WhatsApp Support</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Newsletter Signup -->
    <div class="bg-success py-4">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h5 class="mb-2 mb-md-0">Stay Updated</h5>
            <p class="mb-0 text-light opacity-75">Subscribe to our newsletter for latest updates and offers</p>
          </div>
          <div class="col-md-6">
            <form class="d-flex" id="newsletter-form">
              <input type="email" class="form-control me-2" placeholder="Enter your email" required>
              <button type="submit" class="btn btn-warning">Subscribe</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Copyright -->
    <div class="py-3" style="background-color: #212529;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <p class="mb-0 text-muted">
              &copy; <?= $currentYear ?> <?= e($siteName) ?>. All rights reserved.
            </p>
          </div>
          <div class="col-md-6 text-md-end">
            <div class="d-flex justify-content-md-end gap-3">
              <img src="<?= asset('images/payment/visa.svg') ?>" alt="Visa" height="24">
              <img src="<?= asset('images/payment/mastercard.svg') ?>" alt="Mastercard" height="24">
              <img src="<?= asset('images/payment/jazzcash.png') ?>" alt="JazzCash" height="24">
              <img src="<?= asset('images/payment/easypaisa.png') ?>" alt="EasyPaisa" height="24">
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
  
  <!-- Back to Top Button -->
  <button id="back-to-top" class="btn btn-success position-fixed bottom-0 end-0 m-3 rounded-circle" style="display: none; z-index: 1000;" aria-label="Back to top">
    <i class="fas fa-chevron-up"></i>
  </button>
  
  <!-- JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- <script src="<?= asset('js/bundle.min.js') ?>"></script> -->
  
  <!-- Additional Scripts -->
  <?php if (isset($additionalScripts)): ?>
    <?= $additionalScripts ?>
  <?php endif; ?>
  
  <!-- Google Analytics (if configured) -->
  <?php if ($gaId = getSetting('google_analytics_id')): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $gaId ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= $gaId ?>');
  </script>
  <?php endif; ?>
</body>
</html>
