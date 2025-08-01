<?php
/**
 * Login Page Template
 * User authentication form
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
            <h2 class="fw-bold">Welcome Back</h2>
            <p class="text-muted">Sign in to your account</p>
          </div>
          
          <!-- Login Form -->
          <form method="POST" action="<?= url('login') ?>" id="login-form">
            <?= csrfField() ?>
            
            <!-- Email Field -->
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" 
                     class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                     id="email" 
                     name="email" 
                     value="<?= e($old['email'] ?? '') ?>" 
                     required 
                     autofocus>
              <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback">
                <?= e($errors['email']) ?>
              </div>
              <?php endif; ?>
            </div>
            
            <!-- Password Field -->
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" 
                       class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                       id="password" 
                       name="password" 
                       required>
                <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                  <i class="fas fa-eye"></i>
                </button>
                <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback">
                  <?= e($errors['password']) ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
            
            <!-- Remember Me -->
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
              <label class="form-check-label" for="remember">
                Remember me
              </label>
            </div>
            
            <!-- Submit Button -->
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>
                Sign In
              </button>
            </div>
            
            <!-- Forgot Password Link -->
            <div class="text-center">
              <a href="<?= url('forgot-password') ?>" class="text-decoration-none">
                Forgot your password?
              </a>
            </div>
          </form>
          
          <!-- Divider -->
          <hr class="my-4">
          
          <!-- Register Link -->
          <div class="text-center">
            <p class="mb-0">Don't have an account?</p>
            <a href="<?= url('register') ?>" class="btn btn-outline-primary mt-2">
              Create Account
            </a>
          </div>
        </div>
      </div>
      
      <!-- Demo Credentials (Development Only) -->
      <?php if ($_ENV['APP_ENV'] !== 'production'): ?>
      <div class="card mt-3 border-info">
        <div class="card-body">
          <h6 class="card-title text-info">
            <i class="fas fa-info-circle me-2"></i>
            Demo Credentials
          </h6>
          <div class="row">
            <div class="col-6">
              <strong>Admin:</strong><br>
              <small>admin@sadacart.com</small><br>
              <small>admin123</small>
            </div>
            <div class="col-6">
              <strong>Customer:</strong><br>
              <small>john@example.com</small><br>
              <small>password</small>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password toggle functionality
  const togglePassword = document.getElementById('toggle-password');
  const passwordField = document.getElementById('password');
  
  togglePassword.addEventListener('click', function() {
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
  });
  
  // Form validation
  const loginForm = document.getElementById('login-form');
  loginForm.addEventListener('submit', function(e) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Please fill in all required fields');
      return;
    }
    
    if (!isValidEmail(email)) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Please enter a valid email address');
      return;
    }
  });
  
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
});
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
