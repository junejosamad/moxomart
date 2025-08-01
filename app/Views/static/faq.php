<?php
/**
 * FAQ Page Template
 */
$pageTitle = 'Frequently Asked Questions';
$metaTags = generateMetaTags(
    'FAQ - Frequently Asked Questions | SadaCart',
    'Find answers to common questions about shopping, shipping, returns, and more at SadaCart.',
    asset('images/faq-og.jpg')
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
  
  <?php include APP_PATH . '/Views/layouts/header.php'; ?>
  
  <main class="main-content">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <div class="container">
        <ol class="breadcrumb-list">
          <li><a href="/">Home</a></li>
          <li class="active">FAQ</li>
        </ol>
      </div>
    </nav>

    <!-- FAQ Header -->
    <section class="faq-header">
      <div class="container">
        <div class="faq-header-content">
          <h1>Frequently Asked Questions</h1>
          <p>Find answers to common questions about shopping with SadaCart</p>
          
          <!-- Search FAQ -->
          <div class="faq-search">
            <div class="search-input-group">
              <input type="text" id="faq-search" placeholder="Search FAQ...">
              <i class="fas fa-search"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Content -->
    <section class="faq-section">
      <div class="container">
        <div class="faq-layout">
          <!-- FAQ Categories -->
          <aside class="faq-sidebar">
            <h3>Categories</h3>
            <ul class="faq-categories">
              <li><a href="#ordering" class="category-link active">Ordering</a></li>
              <li><a href="#shipping" class="category-link">Shipping</a></li>
              <li><a href="#returns" class="category-link">Returns & Exchanges</a></li>
              <li><a href="#payment" class="category-link">Payment</a></li>
              <li><a href="#account" class="category-link">Account</a></li>
              <li><a href="#technical" class="category-link">Technical Support</a></li>
            </ul>
          </aside>

          <!-- FAQ Main Content -->
          <div class="faq-main">
            <!-- Ordering -->
            <div class="faq-category" id="ordering">
              <h2>Ordering</h2>
              
              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>How do I place an order?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    Placing an order is easy! Simply browse our products, add items to your cart, 
                    and proceed to checkout. You can shop as a guest or create an account for 
                    faster future purchases. Follow these steps:
                  </p>
                  <ol>
                    <li>Browse products and click "Add to Cart"</li>
                    <li>Review your cart and click "Checkout"</li>
                    <li>Enter shipping and billing information</li>
                    <li>Select payment method and complete your order</li>
                  </ol>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Can I modify or cancel my order?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    You can modify or cancel your order within 1 hour of placing it, provided 
                    it hasn't been processed yet. To make changes:
                  </p>
                  <ul>
                    <li>Log into your account and go to "My Orders"</li>
                    <li>Find your recent order and click "Modify" or "Cancel"</li>
                    <li>If the option isn't available, contact our support team immediately</li>
                  </ul>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Do you offer bulk or wholesale pricing?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    Yes! We offer special pricing for bulk orders and wholesale customers. 
                    Contact our sales team at <a href="mailto:wholesale@sadacart.com">wholesale@sadacart.com</a> 
                    with your requirements, and we'll provide a custom quote.
                  </p>
                </div>
              </div>
            </div>

            <!-- Shipping -->
            <div class="faq-category" id="shipping">
              <h2>Shipping</h2>
              
              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>What are your shipping options and costs?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>We offer several shipping options:</p>
                  <ul>
                    <li><strong>Standard Shipping:</strong> $9.99 (5-7 business days)</li>
                    <li><strong>Express Shipping:</strong> $19.99 (2-3 business days)</li>
                    <li><strong>Overnight Shipping:</strong> $29.99 (1 business day)</li>
                    <li><strong>Free Shipping:</strong> On orders over $50</li>
                  </ul>
                  <p>International shipping is available to select countries with rates calculated at checkout.</p>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>How can I track my order?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    Once your order ships, you'll receive a tracking number via email. You can:
                  </p>
                  <ul>
                    <li>Click the tracking link in your shipping confirmation email</li>
                    <li>Log into your account and view order status</li>
                    <li>Visit our tracking page and enter your order number</li>
                  </ul>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Do you ship internationally?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    Yes, we ship to over 50 countries worldwide. International shipping 
                    costs and delivery times vary by destination. Please note that 
                    customers are responsible for any customs duties or taxes.
                  </p>
                </div>
              </div>
            </div>

            <!-- Returns & Exchanges -->
            <div class="faq-category" id="returns">
              <h2>Returns & Exchanges</h2>
              
              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>What is your return policy?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    We offer a 30-day return policy for most items. Items must be:
                  </p>
                  <ul>
                    <li>Returned within 30 days of delivery</li>
                    <li>In original condition with tags attached</li>
                    <li>Accompanied by original packaging</li>
                    <li>Include proof of purchase</li>
                  </ul>
                  <p>Some items like personalized products or perishables cannot be returned.</p>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>How do I return an item?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>To return an item:</p>
                  <ol>
                    <li>Log into your account and go to "My Orders"</li>
                    <li>Find the order and click "Return Item"</li>
                    <li>Select the item(s) and reason for return</li>
                    <li>Print the prepaid return label</li>
                    <li>Package the item and attach the label</li>
                    <li>Drop off at any authorized shipping location</li>
                  </ol>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>How long does it take to process a refund?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    Once we receive your returned item, we'll process your refund within 
                    3-5 business days. The refund will be credited to your original 
                    payment method and may take an additional 3-7 business days to 
                    appear on your statement.
                  </p>
                </div>
              </div>
            </div>

            <!-- Payment -->
            <div class="faq-category" id="payment">
              <h2>Payment</h2>
              
              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>What payment methods do you accept?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>We accept the following payment methods:</p>
                  <ul>
                    <li>Credit Cards (Visa, MasterCard, American Express, Discover)</li>
                    <li>Debit Cards</li>
                    <li>PayPal</li>
                    <li>Apple Pay</li>
                    <li>Google Pay</li>
                    <li>Shop Pay</li>
                  </ul>
                  <p>All payments are processed securely using industry-standard encryption.</p>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Is my payment information secure?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    We use SSL encryption and comply with PCI DSS standards 
                    to protect your payment information. We never store your complete 
                    credit card details on our servers.
                  </p>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Why was my payment declined?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>Payment declines can happen for several reasons:</p>
                  <ul>
                    <li>Insufficient funds</li>
                    <li>Incorrect billing information</li>
                    <li>Card expired or blocked</li>
                    <li>Bank security measures</li>
                  </ul>
                  <p>Please verify your information and try again, or contact your bank if the issue persists.</p>
                </div>
              </div>
            </div>

            <!-- Account -->
            <div class="faq-category" id="account">
              <h2>Account</h2>
              
              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Do I need an account to shop?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    No, you can shop as a guest. However, creating an account offers benefits like:
                  </p>
                  <ul>
                    <li>Faster checkout</li>
                    <li>Order history and tracking</li>
                    <li>Saved addresses and payment methods</li>
                    <li>Exclusive offers and early access to sales</li>
                    <li>Wishlist functionality</li>
                  </ul>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>How do I reset my password?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>To reset your password:</p>
                  <ol>
                    <li>Go to the login page and click "Forgot Password"</li>
                    <li>Enter your email address</li>
                    <li>Check your email for a reset link</li>
                    <li>Click the link and create a new password</li>
                  </ol>
                  <p>If you don't receive the email, check your spam folder or contact support.</p>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>How do I update my account information?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    Log into your account and go to "My Profile" to update your personal 
                    information, addresses, and communication preferences. Changes are 
                    saved automatically.
                  </p>
                </div>
              </div>
            </div>

            <!-- Technical Support -->
            <div class="faq-category" id="technical">
              <h2>Technical Support</h2>
              
              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>The website isn't working properly. What should I do?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>Try these troubleshooting steps:</p>
                  <ol>
                    <li>Clear your browser cache and cookies</li>
                    <li>Disable browser extensions temporarily</li>
                    <li>Try a different browser or device</li>
                    <li>Check your internet connection</li>
                    <li>Update your browser to the latest version</li>
                  </ol>
                  <p>If the problem persists, contact our technical support team.</p>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Which browsers do you support?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>Our website works best with the latest versions of:</p>
                  <ul>
                    <li>Google Chrome</li>
                    <li>Mozilla Firefox</li>
                    <li>Safari</li>
                    <li>Microsoft Edge</li>
                  </ul>
                  <p>We recommend keeping your browser updated for the best experience.</p>
                </div>
              </div>

              <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                  <span>Do you have a mobile app?</span>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                  <p>
                    Currently, we don't have a dedicated mobile app, but our website 
                    is fully optimized for mobile devices. You can shop seamlessly 
                    on your smartphone or tablet through your browser.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Still Have Questions -->
        <div class="faq-contact">
          <div class="faq-contact-content">
            <h3>Still Have Questions?</h3>
            <p>Can't find what you're looking for? Our customer support team is here to help!</p>
            <div class="contact-options">
              <a href="/contact" class="btn btn-primary">
                <i class="fas fa-envelope"></i>
                Contact Support
              </a>
              <div class="contact-info">
                <div class="contact-item">
                  <i class="fas fa-phone"></i>
                  <span>1-800-SADACART</span>
                </div>
                <div class="contact-item">
                  <i class="fas fa-clock"></i>
                  <span>Mon-Fri 9AM-6PM EST</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <?php include APP_PATH . '/Views/layouts/footer.php'; ?>
  
  <script src="<?= asset('js/app.min.js') ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // FAQ accordion functionality
      const faqQuestions = document.querySelectorAll('.faq-question');
      
      faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
          const isExpanded = this.getAttribute('aria-expanded') === 'true';
          const answer = this.nextElementSibling;
          
          // Close all other FAQ items
          faqQuestions.forEach(q => {
            if (q !== this) {
              q.setAttribute('aria-expanded', 'false');
              q.nextElementSibling.style.maxHeight = null;
              q.querySelector('i').style.transform = 'rotate(0deg)';
            }
          });
          
          // Toggle current FAQ item
          if (isExpanded) {
            this.setAttribute('aria-expanded', 'false');
            answer.style.maxHeight = null;
            this.querySelector('i').style.transform = 'rotate(0deg)';
          } else {
            this.setAttribute('aria-expanded', 'true');
            answer.style.maxHeight = answer.scrollHeight + 'px';
            this.querySelector('i').style.transform = 'rotate(180deg)';
          }
        });
      });

      // Category navigation
      const categoryLinks = document.querySelectorAll('.category-link');
      
      categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove active class from all links
          categoryLinks.forEach(l => l.classList.remove('active'));
          
          // Add active class to clicked link
          this.classList.add('active');
          
          // Scroll to category
          const targetId = this.getAttribute('href').substring(1);
          const targetElement = document.getElementById(targetId);
          
          if (targetElement) {
            targetElement.scrollIntoView({ behavior: 'smooth' });
          }
        });
      });

      // FAQ search functionality
      const searchInput = document.getElementById('faq-search');
      const faqItems = document.querySelectorAll('.faq-item');
      
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        faqItems.forEach(item => {
          const question = item.querySelector('.faq-question span').textContent.toLowerCase();
          const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
          
          if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
          } else {
            item.style.display = searchTerm === '' ? 'block' : 'none';
          }
        });
      });

      // Update active category on scroll
      const categories = document.querySelectorAll('.faq-category');
      
      window.addEventListener('scroll', function() {
        let current = '';
        
        categories.forEach(category => {
          const rect = category.getBoundingClientRect();
          if (rect.top <= 100) {
            current = category.id;
          }
        });
        
        categoryLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
          }
        });
      });
    });
  </script>
</body>
</html>
