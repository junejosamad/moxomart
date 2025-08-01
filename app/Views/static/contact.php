<?php
/**
 * Contact Us Page
 * Contact form and company information
 */

$pageTitle = 'Contact Us';
$metaTags = generateMetaTags(
    'Contact Us - Get in Touch | SadaCart',
    'Have questions or need help? Contact the SadaCart team. We\'re here to help with your shopping experience.',
    asset('images/contact-og.jpg')
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTags['title']) ?></title>
    <meta name="description" content="<?= e($metaTags['description']) ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($metaTags['og_title']) ?>">
    <meta property="og:description" content="<?= e($metaTags['og_description']) ?>">
    <meta property="og:image" content="<?= $metaTags['og_image'] ?>">
    <meta property="og:url" content="<?= $metaTags['og_url'] ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:title" content="<?= e($metaTags['twitter_title']) ?>">
    <meta name="twitter:description" content="<?= e($metaTags['twitter_description']) ?>">
    <meta name="twitter:image" content="<?= $metaTags['twitter_image'] ?>">
    
    <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../app/Views/layouts/header.php'; ?>
    
    <main class="main-content">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <div class="container">
                <ol class="breadcrumb-list">
                    <li><a href="/">Home</a></li>
                    <li class="active">Contact Us</li>
                </ol>
            </div>
        </nav>

        <!-- Contact Header -->
        <section class="contact-header">
            <div class="container">
                <div class="contact-header-content">
                    <h1>Get in Touch</h1>
                    <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                </div>
            </div>
        </section>

        <!-- Contact Content -->
        <section class="contact-section">
            <div class="container">
                <div class="contact-layout">
                    <!-- Contact Form -->
                    <div class="contact-form-container">
                        <h2>Send us a Message</h2>
                        
                        <?php if ($flash = getFlash('success')): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <?= e($flash) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($flash = getFlash('error')): ?>
                            <div class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= e($flash) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/contact" class="contact-form">
                            <?= csrfField() ?>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" 
                                           value="<?= e($_POST['first_name'] ?? '') ?>" 
                                           required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" 
                                           value="<?= e($_POST['last_name'] ?? '') ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" 
                                           value="<?= e($_POST['email'] ?? '') ?>" 
                                           required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" 
                                           value="<?= e($_POST['phone'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="general" <?= ($_POST['subject'] ?? '') == 'general' ? 'selected' : '' ?>>General Inquiry</option>
                                    <option value="order" <?= ($_POST['subject'] ?? '') == 'order' ? 'selected' : '' ?>>Order Support</option>
                                    <option value="shipping" <?= ($_POST['subject'] ?? '') == 'shipping' ? 'selected' : '' ?>>Shipping Question</option>
                                    <option value="returns" <?= ($_POST['subject'] ?? '') == 'returns' ? 'selected' : '' ?>>Returns & Exchanges</option>
                                    <option value="technical" <?= ($_POST['subject'] ?? '') == 'technical' ? 'selected' : '' ?>>Technical Support</option>
                                    <option value="partnership" <?= ($_POST['subject'] ?? '') == 'partnership' ? 'selected' : '' ?>>Partnership Inquiry</option>
                                    <option value="other" <?= ($_POST['subject'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="order_number">Order Number (if applicable)</label>
                                <input type="text" id="order_number" name="order_number" 
                                       value="<?= e($_POST['order_number'] ?? '') ?>" 
                                       placeholder="SC-2024-123456">
                            </div>

                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" rows="6" 
                                          placeholder="Please provide as much detail as possible..." 
                                          required><?= e($_POST['message'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="newsletter" value="1" 
                                           <?= ($_POST['newsletter'] ?? false) ? 'checked' : '' ?>>
                                    <span class="checkmark"></span>
                                    Subscribe to our newsletter for updates and exclusive offers
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Send Message
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-info">
                        <h2>Contact Information</h2>
                        
                        <div class="contact-methods">
                            <div class="contact-method">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h3>Address</h3>
                                    <p>
                                        123 Commerce Street<br>
                                        Suite 100<br>
                                        New York, NY 10001<br>
                                        United States
                                    </p>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-details">
                                    <h3>Phone</h3>
                                    <p>
                                        <a href="tel:+18005232278">1-800-SADACART</a><br>
                                        <small>Mon-Fri 9AM-6PM EST</small>
                                    </p>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <h3>Email</h3>
                                    <p>
                                        <a href="mailto:support@sadacart.com">support@sadacart.com</a><br>
                                        <small>We respond within 24 hours</small>
                                    </p>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="contact-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div class="contact-details">
                                    <h3>Live Chat</h3>
                                    <p>
                                        Available on our website<br>
                                        <small>Mon-Fri 9AM-6PM EST</small>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <div class="business-hours">
                            <h3>Business Hours</h3>
                            <div class="hours-list">
                                <div class="hours-item">
                                    <span class="day">Monday - Friday</span>
                                    <span class="time">9:00 AM - 6:00 PM EST</span>
                                </div>
                                <div class="hours-item">
                                    <span class="day">Saturday</span>
                                    <span class="time">10:00 AM - 4:00 PM EST</span>
                                </div>
                                <div class="hours-item">
                                    <span class="day">Sunday</span>
                                    <span class="time">Closed</span>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="social-media">
                            <h3>Follow Us</h3>
                            <div class="social-links">
                                <a href="#" class="social-link facebook" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link twitter" aria-label="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link instagram" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link linkedin" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link youtube" aria-label="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="contact-faq">
            <div class="container">
                <div class="faq-header">
                    <h2>Frequently Asked Questions</h2>
                    <p>Quick answers to common questions</p>
                </div>
                
                <div class="faq-grid">
                    <div class="faq-card">
                        <h3>How can I track my order?</h3>
                        <p>
                            You can track your order using the tracking number sent to your email, 
                            or by logging into your account and viewing your order history.
                        </p>
                        <a href="/faq#shipping" class="faq-link">Learn more</a>
                    </div>

                    <div class="faq-card">
                        <h3>What is your return policy?</h3>
                        <p>
                            We offer a 30-day return policy for most items. Items must be in 
                            original condition with tags attached.
                        </p>
                        <a href="/faq#returns" class="faq-link">Learn more</a>
                    </div>

                    <div class="faq-card">
                        <h3>How long does shipping take?</h3>
                        <p>
                            Standard shipping takes 5-7 business days, express shipping takes 2-3 days, 
                            and overnight shipping is available for next-day delivery.
                        </p>
                        <a href="/faq#shipping" class="faq-link">Learn more</a>
                    </div>

                    <div class="faq-card">
                        <h3>Do you offer international shipping?</h3>
                        <p>
                            Yes, we ship to over 50 countries worldwide. Shipping costs and 
                            delivery times vary by destination.
                        </p>
                        <a href="/faq#shipping" class="faq-link">Learn more</a>
                    </div>
                </div>

                <div class="faq-cta">
                    <p>Can't find what you're looking for?</p>
                    <a href="/faq" class="btn btn-secondary">
                        <i class="fas fa-question-circle"></i>
                        View All FAQs
                    </a>
                </div>
            </div>
        </section>
    </main>
    
    <?php include '../app/Views/layouts/footer.php'; ?>
    
    <script src="<?= asset('js/app.min.js') ?>"></script>
    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.contact-form');
            const subjectSelect = document.getElementById('subject');
            const orderNumberField = document.getElementById('order_number');
            
            // Show/hide order number field based on subject
            subjectSelect.addEventListener('change', function() {
                if (this.value === 'order' || this.value === 'shipping' || this.value === 'returns') {
                    orderNumberField.parentElement.style.display = 'block';
                    orderNumberField.setAttribute('required', 'required');
                } else {
                    orderNumberField.parentElement.style.display = 'none';
                    orderNumberField.removeAttribute('required');
                    orderNumberField.value = '';
                }
            });
            
            // Trigger change event on page load
            subjectSelect.dispatchEvent(new Event('change'));
            
            // Form submission handling
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                submitBtn.disabled = true;
                
                // Re-enable button after 5 seconds (in case of slow response)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        });
    </script>
</body>
</html>
