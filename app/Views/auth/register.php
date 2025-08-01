<?php
/**
 * Registration Page Template
 * User registration form
 */

include APP_PATH . '/Views/layouts/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-5">
          <!-- Header -->
          <div class="text-center mb-4">
            <h2 class="fw-bold">Create Account</h2>
            <p class="text-muted">Join SadaCart and start shopping</p>
          </div>
          
          <!-- Registration Form -->
          <form method="POST" action="<?= url('register') ?>" id="register-form">
            <?= csrfField() ?>
            
            <div class="row">
              <!-- First Name -->
              <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" 
                       class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                       id="first_name" 
                       name="first_name" 
                       value="<?= e($old['first_name'] ?? '') ?>" 
                       required 
                       autofocus>
                <?php if (isset($errors['first_name'])): ?>
                <div class="invalid-feedback">
                  <?= e($errors['first_name']) ?>
                </div>
                <?php endif; ?>
              </div>
              
              <!-- Last Name -->
              <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" 
                       class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                       id="last_name" 
                       name="last_name" 
                       value="<?= e($old['last_name'] ?? '') ?>" 
                       required>
                <?php if (isset($errors['last_name'])): ?>
                <div class="invalid-feedback">
                  <?= e($errors['last_name']) ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
            
            <!-- Email Field -->
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" 
                     class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                     id="email" 
                     name="email" 
                     value="<?= e($old['email'] ?? '') ?>" 
                     required>
              <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback">
                <?= e($errors['email']) ?>
              </div>
              <?php endif; ?>
            </div>
            
            <!-- Phone Field -->
            <div class="mb-3">
              <label for="phone" class="form-label">Phone Number <span class="text-muted">(Optional)</span></label>
              <input type="tel" 
                     class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                     id="phone" 
                     name="phone" 
                     value="<?= e($old['phone'] ?? '') ?>">
              <?php if (isset($errors['phone'])): ?>
              <div class="invalid-feedback">
                <?= e($errors['phone']) ?>
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
              <div class="form-text">
                Password must be at least 8 characters long
              </div>
            </div>
            
            <!-- Confirm Password Field -->
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <input type="password" 
                     class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>" 
                     id="password_confirmation" 
                     name="password_confirmation" 
                     required>
              <?php if (isset($errors['password_confirmation'])): ?>
              <div class="invalid-feedback">
                <?= e($errors['password_confirmation']) ?>
              </div>
              <?php endif; ?>
            </div>
            
            <!-- Terms and Conditions -->
            <div class="mb-3 form-check">
              <input type="checkbox" 
                     class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>" 
                     id="terms" 
                     name="terms" 
                     value="1" 
                     required>
              <label class="form-check-label" for="terms">
                I agree to the <a href="<?= url('terms') ?>" target="_blank">Terms of Service</a> 
                and <a href="<?= url('privacy-policy') ?>" target="_blank">Privacy Policy</a>
              </label>
              <?php if (isset($errors['terms'])): ?>
              <div class="invalid-feedback">
                <?= e($errors['terms']) ?>
              </div>
              <?php endif; ?>
            </div>
            
            <!-- Newsletter Subscription -->
            <div class="mb-4 form-check">
              <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter" value="1" checked>
              <label class="form-check-label" for="newsletter">
                Subscribe to our newsletter for updates and special offers
              </label>
            </div>
            
            <!-- Submit Button -->
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus me-2"></i>
                Create Account
              </button>
            </div>
          </form>
          
          <!-- Divider -->
          <hr class="my-4">
          
          <!-- Login Link -->
          <div class="text-center">
            <p class="mb-0">Already have an account?</p>
            <a href="<?= url('login') ?>" class="btn btn-outline-primary mt-2">
              Sign In
            </a>
          </div>
        </div>
      </div>
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
  
  // Password strength indicator
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('password_confirmation');
  
  password.addEventListener('input', function() {
    const strength = checkPasswordStrength(this.value);
    updatePasswordStrength(strength);
  });
  
  confirmPassword.addEventListener('input', function() {
    checkPasswordMatch();
  });
  
  password.addEventListener('input', checkPasswordMatch);
  
  function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
  }
  
  function updatePasswordStrength(strength) {
    // Remove existing strength indicator
    const existingIndicator = document.querySelector('.password-strength');
    if (existingIndicator) {
      existingIndicator.remove();
    }
    
    if (password.value.length === 0) return;
    
    const colors = ['danger', 'danger', 'warning', 'info', 'success'];
    const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    
    const indicator = document.createElement('div');
    indicator.className = `password-strength text-${colors[strength - 1]} small mt-1`;
    indicator.innerHTML = `<i class="fas fa-shield-alt me-1"></i>Password strength: ${texts[strength - 1]}`;
    
    password.parentNode.appendChild(indicator);
  }
  
  function checkPasswordMatch() {
    const match = password.value === confirmPassword.value;
    
    if (confirmPassword.value.length > 0) {
      if (match) {
        confirmPassword.classList.remove('is-invalid');
        confirmPassword.classList.add('is-valid');
      } else {
        confirmPassword.classList.remove('is-valid');
        confirmPassword.classList.add('is-invalid');
      }
    }
  }
  
  // Form validation
  const registerForm = document.getElementById('register-form');
  registerForm.addEventListener('submit', function(e) {
    const firstName = document.getElementById('first_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    const email = document.getElementById('email').value.trim();
    const passwordValue = password.value;
    const confirmPasswordValue = confirmPassword.value;
    const terms = document.getElementById('terms').checked;
    
    let isValid = true;
    
    if (!firstName || !lastName || !email || !passwordValue || !confirmPasswordValue) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Please fill in all required fields');
      isValid = false;
    }
    
    if (!isValidEmail(email)) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Please enter a valid email address');
      isValid = false;
    }
    
    if (passwordValue.length < 8) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Password must be at least 8 characters long');
      isValid = false;
    }
    
    if (passwordValue !== confirmPasswordValue) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Passwords do not match');
      isValid = false;
    }
    
    if (!terms) {
      e.preventDefault();
      SadaCart.showNotification('error', 'Please accept the Terms of Service');
      isValid = false;
    }
    
    if (!isValid) {
      return false;
    }
  });
  
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
});
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
