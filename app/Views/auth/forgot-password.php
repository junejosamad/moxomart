<?php
/**
 * Forgot Password Page Template
 * Password reset request form
 */

include APP_PATH . '/Views/layouts/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body p-5">
          <!-- Header -->
          <div class="text-center mb-4">
            <div class="mb-3">
              <i class="fas fa-key fa-3x text-primary"></i>
            </div>
            <h2 class="fw-bold">Forgot Password?</h2>
            <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
          </div>
          
          <!-- Forgot Password Form -->
          <form method="POST" action="<?= url('forgot-password') ?>" id="forgot-password-form">
            <?= csrfField() ?>
            
            <!-- Email Field -->
            <div class="mb-4">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" 
                     class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                     id="email" 
                     name="email" 
                     value="<?= e($old['email'] ?? '') ?>" 
                     required 
                     autofocus 
                     placeholder="Enter your email address">
              <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback">
                <?= e($errors['email']) ?>
              </div>
              <?php endif; ?>
            </div>
            
            <!-- Submit Button -->
            <div class="d-grid mb-4">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane me-2"></i>
                Send Reset Link
              </button>
            </div>
          </form>
          
          <!-- Back to Login -->
          <div class="text-center">
            <a href="<?= url('login') ?>" class="text-decoration-none">
              <i class="fas fa-arrow-left me-2"></i>
              Back to Login
            </a>
          </div>
        </div>
      </div>
      
      <!-- Help Text -->
      <div class="card mt-3 border-info">
        <div class="card-body">
          <h6 class="card-title text-info">
            <i class="fas fa-info-circle me-2"></i>
            Need Help?
          </h6>
          <p class="card-text small mb-0">
            If you don't receive the reset email within a few minutes, please check your spam folder. 
            For further assistance, contact our support team at 
            <a href="mailto:<?= getSetting('contact_email', 'support@sadacart.com') ?>">
              <?= getSetting('contact_email', 'support@sadacart.com') ?>
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const forgotPasswordForm = document.getElementById('forgot-password-form');
  
  forgotPasswordForm.addEventListener('submit', function(e) {
    const email = document.getElementById('email').value.trim();
    
    if (!email) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Please enter your email address');
      return;
    }
    
    if (!isValidEmail(email)) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Please enter a valid email address');
      return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitBtn.disabled = true;
    
    // Re-enable button after 3 seconds (in case of slow response)
    setTimeout(() => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }, 3000);
  });
  
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
});
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
