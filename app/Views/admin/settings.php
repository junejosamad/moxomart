<?php
/**
 * Admin Settings Template
 */
$pageTitle = 'Site Settings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> - Admin | SadaCart</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">
  
  <div class="d-flex">
    <!-- Sidebar -->
    <?php include APP_PATH . '/Views/layouts/admin-sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px;">
      <!-- Top Navigation -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
          <h4 class="mb-0">Site Settings</h4>
          
          <div class="navbar-nav ms-auto">
            <button type="submit" form="settings-form" class="btn btn-primary">
              <i class="fas fa-save me-2"></i>Save Settings
            </button>
          </div>
        </div>
      </nav>
      
      <!-- Settings Content -->
      <div class="container-fluid p-4">
        <form id="settings-form" method="POST" action="<?= url('admin/settings') ?>" enctype="multipart/form-data">
          <?= csrfField() ?>
          
          <div class="settings-tabs">
            <div class="tab-nav">
              <button type="button" class="tab-btn active" data-tab="general">
                <i class="fas fa-cog"></i>
                General
              </button>
              <button type="button" class="tab-btn" data-tab="store">
                <i class="fas fa-store"></i>
                Store
              </button>
              <button type="button" class="tab-btn" data-tab="email">
                <i class="fas fa-envelope"></i>
                Email
              </button>
              <button type="button" class="tab-btn" data-tab="payment">
                <i class="fas fa-credit-card"></i>
                Payment
              </button>
              <button type="button" class="tab-btn" data-tab="shipping">
                <i class="fas fa-truck"></i>
                Shipping
              </button>
              <button type="button" class="tab-btn" data-tab="seo">
                <i class="fas fa-search"></i>
                SEO
              </button>
            </div>

            <!-- General Settings -->
            <div class="tab-content active" id="general">
              <h3>General Settings</h3>
              
              <div class="mb-3">
                <label for="site_name" class="form-label">Site Name *</label>
                <input type="text" 
                       class="form-control" 
                       id="site_name" 
                       name="site_name" 
                       value="<?= e($settings['site_name'] ?? 'SadaCart') ?>" 
                       required 
                       maxlength="100">
              </div>

              <div class="mb-3">
                <label for="site_tagline" class="form-label">Site Tagline</label>
                <input type="text" 
                       class="form-control" 
                       id="site_tagline" 
                       name="site_tagline" 
                       value="<?= e($settings['site_tagline'] ?? '') ?>" 
                       maxlength="200">
              </div>

              <div class="mb-3">
                <label for="site_description" class="form-label">Site Description</label>
                <textarea class="form-control" 
                          id="site_description" 
                          name="site_description" 
                          rows="4" 
                          maxlength="500"><?= e($settings['site_description'] ?? '') ?></textarea>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="admin_email" class="form-label">Admin Email *</label>
                    <input type="email" 
                           class="form-control" 
                           id="admin_email" 
                           name="admin_email" 
                           value="<?= e($settings['admin_email'] ?? '') ?>" 
                           required>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="contact_phone" class="form-label">Contact Phone</label>
                    <input type="tel" 
                           class="form-control" 
                           id="contact_phone" 
                           name="contact_phone" 
                           value="<?= e($settings['contact_phone'] ?? '') ?>">
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="site_logo" class="form-label">Site Logo</label>
                <input type="file" 
                       class="form-control" 
                       id="site_logo" 
                       name="site_logo" 
                       accept="image/*">
                <?php if (!empty($settings['site_logo'])): ?>
                    <div class="current-logo">
                        <img src="<?= asset('uploads/' . $settings['site_logo']) ?>" 
                             alt="Current Logo" 
                             style="max-height: 60px;">
                        <small>Current logo</small>
                    </div>
                <?php endif; ?>
              </div>

              <div class="mb-3">
                <label for="timezone" class="form-label">Timezone</label>
                <select class="form-select" id="timezone" name="timezone">
                  <option value="America/New_York" <?= ($settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                  <option value="America/Chicago" <?= ($settings['timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                  <option value="America/Denver" <?= ($settings['timezone'] ?? '') == 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                  <option value="America/Los_Angeles" <?= ($settings['timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
                </select>
              </div>
            </div>

            <!-- Store Settings -->
            <div class="tab-content" id="store">
              <h3>Store Settings</h3>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="currency" class="form-label">Currency</label>
                    <select class="form-select" id="currency" name="currency">
                      <option value="USD" <?= ($settings['currency'] ?? 'USD') == 'USD' ? 'selected' : '' ?>>USD ($)</option>
                      <option value="EUR" <?= ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                      <option value="GBP" <?= ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                      <option value="CAD" <?= ($settings['currency'] ?? '') == 'CAD' ? 'selected' : '' ?>>CAD (C$)</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                    <input type="number" 
                           class="form-control" 
                           id="tax_rate" 
                           name="tax_rate" 
                           value="<?= $settings['tax_rate'] ?? '8.00' ?>" 
                           step="0.01" 
                           min="0" 
                           max="100">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="free_shipping_threshold" class="form-label">Free Shipping Threshold</label>
                    <input type="number" 
                           class="form-control" 
                           id="free_shipping_threshold" 
                           name="free_shipping_threshold" 
                           value="<?= $settings['free_shipping_threshold'] ?? '50.00' ?>" 
                           step="0.01" 
                           min="0">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="default_shipping_cost" class="form-label">Default Shipping Cost</label>
                    <input type="number" 
                           class="form-control" 
                           id="default_shipping_cost" 
                           name="default_shipping_cost" 
                           value="<?= $settings['default_shipping_cost'] ?? '9.99' ?>" 
                           step="0.01" 
                           min="0">
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="inventory_tracking" 
                         value="1" 
                         <?= ($settings['inventory_tracking'] ?? true) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Enable Inventory Tracking
                </label>
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="guest_checkout" 
                         value="1" 
                         <?= ($settings['guest_checkout'] ?? true) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Allow Guest Checkout
                </label>
              </div>
            </div>

            <!-- Email Settings -->
            <div class="tab-content" id="email">
              <h3>Email Settings</h3>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="mail_from_name" class="form-label">From Name</label>
                    <input type="text" 
                           class="form-control" 
                           id="mail_from_name" 
                           name="mail_from_name" 
                           value="<?= e($settings['mail_from_name'] ?? 'SadaCart Support') ?>">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="mail_from_email" class="form-label">From Email</label>
                    <input type="email" 
                           class="form-control" 
                           id="mail_from_email" 
                           name="mail_from_email" 
                           value="<?= e($settings['mail_from_email'] ?? 'noreply@sadacart.com') ?>">
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="order_confirmation_email" 
                         value="1" 
                         <?= ($settings['order_confirmation_email'] ?? true) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Send Order Confirmation Emails
                </label>
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="shipping_notification_email" 
                         value="1" 
                         <?= ($settings['shipping_notification_email'] ?? true) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Send Shipping Notification Emails
                </label>
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="low_stock_alerts" 
                         value="1" 
                         <?= ($settings['low_stock_alerts'] ?? true) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Send Low Stock Alerts to Admin
                </label>
              </div>
            </div>

            <!-- Payment Settings -->
            <div class="tab-content" id="payment">
              <h3>Payment Settings</h3>
              
              <div class="payment-method">
                <h4>PayPal</h4>
                <div class="mb-3">
                  <label class="checkbox-label">
                    <input type="checkbox" 
                           class="form-check-input" 
                           name="paypal_enabled" 
                           value="1" 
                           <?= ($settings['paypal_enabled'] ?? false) ? 'checked' : '' ?>>
                    <span class="checkmark"></span>
                    Enable PayPal
                  </label>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="paypal_client_id" class="form-label">PayPal Client ID</label>
                      <input type="text" 
                             class="form-control" 
                             id="paypal_client_id" 
                             name="paypal_client_id" 
                             value="<?= e($settings['paypal_client_id'] ?? '') ?>">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="paypal_client_secret" class="form-label">PayPal Client Secret</label>
                      <input type="password" 
                             class="form-control" 
                             id="paypal_client_secret" 
                             name="paypal_client_secret" 
                             value="<?= e($settings['paypal_client_secret'] ?? '') ?>">
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="paypal_mode" class="form-label">PayPal Mode</label>
                  <select class="form-select" id="paypal_mode" name="paypal_mode">
                    <option value="sandbox" <?= ($settings['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' ?>>Sandbox</option>
                    <option value="live" <?= ($settings['paypal_mode'] ?? '') == 'live' ? 'selected' : '' ?>>Live</option>
                  </select>
                </div>
              </div>

              <div class="payment-method">
                <h4>Stripe</h4>
                <div class="mb-3">
                  <label class="checkbox-label">
                    <input type="checkbox" 
                           class="form-check-input" 
                           name="stripe_enabled" 
                           value="1" 
                           <?= ($settings['stripe_enabled'] ?? false) ? 'checked' : '' ?>>
                    <span class="checkmark"></span>
                    Enable Stripe
                  </label>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="stripe_publishable_key" class="form-label">Stripe Publishable Key</label>
                      <input type="text" 
                             class="form-control" 
                             id="stripe_publishable_key" 
                             name="stripe_publishable_key" 
                             value="<?= e($settings['stripe_publishable_key'] ?? '') ?>">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="stripe_secret_key" class="form-label">Stripe Secret Key</label>
                      <input type="password" 
                             class="form-control" 
                             id="stripe_secret_key" 
                             name="stripe_secret_key" 
                             value="<?= e($settings['stripe_secret_key'] ?? '') ?>">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Shipping Settings -->
            <div class="tab-content" id="shipping">
              <h3>Shipping Settings</h3>
              
              <div class="mb-3">
                <label for="shipping_origin_address" class="form-label">Origin Address</label>
                <textarea class="form-control" 
                          id="shipping_origin_address" 
                          name="shipping_origin_address" 
                          rows="3" 
                          placeholder="123 Main St, City, State 12345"><?= e($settings['shipping_origin_address'] ?? '') ?></textarea>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="domestic_shipping_rate" class="form-label">Domestic Shipping Rate</label>
                    <input type="number" 
                           class="form-control" 
                           id="domestic_shipping_rate" 
                           name="domestic_shipping_rate" 
                           value="<?= $settings['domestic_shipping_rate'] ?? '9.99' ?>" 
                           step="0.01" 
                           min="0">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="international_shipping_rate" class="form-label">International Shipping Rate</label>
                    <input type="number" 
                           class="form-control" 
                           id="international_shipping_rate" 
                           name="international_shipping_rate" 
                           value="<?= $settings['international_shipping_rate'] ?? '24.99' ?>" 
                           step="0.01" 
                           min="0">
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="international_shipping_enabled" 
                         value="1" 
                         <?= ($settings['international_shipping_enabled'] ?? false) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Enable International Shipping
                </label>
              </div>

              <div class="mb-3">
                <label for="shipping_calculation" class="form-label">Shipping Calculation Method</label>
                <select class="form-select" id="shipping_calculation" name="shipping_calculation">
                  <option value="flat_rate" <?= ($settings['shipping_calculation'] ?? 'flat_rate') == 'flat_rate' ? 'selected' : '' ?>>Flat Rate</option>
                  <option value="weight_based" <?= ($settings['shipping_calculation'] ?? '') == 'weight_based' ? 'selected' : '' ?>>Weight Based</option>
                  <option value="free" <?= ($settings['shipping_calculation'] ?? '') == 'free' ? 'selected' : '' ?>>Free Shipping</option>
                </select>
              </div>
            </div>

            <!-- SEO Settings -->
            <div class="tab-content" id="seo">
              <h3>SEO Settings</h3>
              
              <div class="mb-3">
                <label for="meta_title" class="form-label">Default Meta Title</label>
                <input type="text" 
                       class="form-control" 
                       id="meta_title" 
                       name="meta_title" 
                       value="<?= e($settings['meta_title'] ?? '') ?>" 
                       maxlength="60">
                <small>Used when page doesn't have specific meta title</small>
              </div>

              <div class="mb-3">
                <label for="meta_description" class="form-label">Default Meta Description</label>
                <textarea class="form-control" 
                          id="meta_description" 
                          name="meta_description" 
                          rows="3" 
                          maxlength="160"><?= e($settings['meta_description'] ?? '') ?></textarea>
                <small>Used when page doesn't have specific meta description</small>
              </div>

              <div class="mb-3">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <input type="text" 
                       class="form-control" 
                       id="meta_keywords" 
                       name="meta_keywords" 
                       value="<?= e($settings['meta_keywords'] ?? '') ?>" 
                       placeholder="ecommerce, online store, shopping">
                <small>Comma-separated keywords</small>
              </div>

              <div class="mb-3">
                <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                <input type="text" 
                       class="form-control" 
                       id="google_analytics_id" 
                       name="google_analytics_id" 
                       value="<?= e($settings['google_analytics_id'] ?? '') ?>" 
                       placeholder="G-XXXXXXXXXX">
              </div>

              <div class="mb-3">
                <label for="facebook_pixel_id" class="form-label">Facebook Pixel ID</label>
                <input type="text" 
                       class="form-control" 
                       id="facebook_pixel_id" 
                       name="facebook_pixel_id" 
                       value="<?= e($settings['facebook_pixel_id'] ?? '') ?>" 
                       placeholder="123456789012345">
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="sitemap_enabled" 
                         value="1" 
                         <?= ($settings['sitemap_enabled'] ?? true) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Enable XML Sitemap Generation
                </label>
              </div>

              <div class="mb-3">
                <label class="checkbox-label">
                  <input type="checkbox" 
                         class="form-check-input" 
                         name="robots_txt_enabled" 
                         value="1" 
                         <?= ($settings['robots_txt_enabled'] ?? true) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  Enable robots.txt
                </label>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i>
              Save Settings
            </button>
            
            <button type="button" class="btn btn-secondary" id="reset-settings">
              <i class="fas fa-undo"></i>
              Reset to Defaults
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <script src="<?= asset('js/app.min.js') ?>"></script>
  <script>
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const tabId = btn.dataset.tab;
        
        // Remove active class from all tabs and contents
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        btn.classList.add('active');
        document.getElementById(tabId).classList.add('active');
      });
    });

    // Reset settings confirmation
    document.getElementById('reset-settings').addEventListener('click', () => {
      if (confirm('Are you sure you want to reset all settings to their default values? This action cannot be undone.')) {
        // Create form to reset settings
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/settings/reset';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= generateCsrfToken() ?>';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
      }
    });
  </script>
</body>
</html>
