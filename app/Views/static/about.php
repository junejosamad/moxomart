<?php
/**
 * About Us Page Template
 */
$pageTitle = 'About Us';
$metaTags = generateMetaTags(
    'About Us - Learn More About SadaCart',
    'Discover the story behind SadaCart, our mission, values, and the team dedicated to providing you with the best online shopping experience.',
    asset('images/about-og.jpg')
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
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  
  <?php include APP_PATH . '/Views/layouts/header.php'; ?>
  
  <!-- Breadcrumb -->
  <nav class="breadcrumb">
      <div class="container">
          <ol class="breadcrumb-list">
              <li><a href="/">Home</a></li>
              <li class="active">About Us</li>
          </ol>
      </div>
  </nav>

  <!-- Hero Section -->
  <section class="bg-primary text-white py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="display-4 fw-bold mb-3">About SadaCart</h1>
          <p class="lead">We're passionate about creating exceptional e-commerce experiences that connect businesses with their customers in meaningful ways.</p>
        </div>
        <div class="col-lg-6">
          <img src="<?= asset('images/about-hero.jpg') ?>" alt="About SadaCart" class="img-fluid rounded shadow">
        </div>
      </div>
    </div>
  </section>
  
  <!-- Our Story -->
  <section class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <h2 class="text-center mb-5">Our Story</h2>
          <div class="row">
            <div class="col-md-6">
              <p>Founded in 2024, SadaCart emerged from a simple yet powerful vision: to democratize e-commerce and make online selling accessible to businesses of all sizes. Our founders, experienced in both technology and retail, recognized the growing need for a platform that combines ease of use with powerful functionality.</p>
              
              <p>What started as a small project has grown into a comprehensive e-commerce solution trusted by thousands of merchants worldwide. We've built SadaCart with the understanding that every business is unique, and our platform reflects that diversity through its flexibility and customization options.</p>
            </div>
            <div class="col-md-6">
              <p>Today, SadaCart powers online stores across various industries, from fashion and electronics to handmade crafts and digital products. Our commitment to innovation drives us to continuously improve our platform, adding new features and capabilities that help our merchants succeed in the ever-evolving digital marketplace.</p>
              
              <p>We believe that great e-commerce is about more than just transactions – it's about building relationships, creating experiences, and fostering communities around brands and products that people love.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Mission & Values -->
  <section class="bg-light py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center mb-5">
          <h2>Our Mission & Values</h2>
          <p class="lead">The principles that guide everything we do</p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
              <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="fas fa-heart fa-2x"></i>
              </div>
              <h5>Customer First</h5>
              <p class="text-muted">Every decision we make is centered around providing the best possible experience for our customers. Your satisfaction is our top priority.</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
              <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="fas fa-shield-alt fa-2x"></i>
              </div>
              <h5>Quality Assurance</h5>
              <p class="text-muted">We carefully vet every product and supplier to ensure you receive only the highest quality items that meet our strict standards.</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
              <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="fas fa-leaf fa-2x"></i>
              </div>
              <h5>Sustainability</h5>
              <p class="text-muted">We're committed to reducing our environmental impact through sustainable packaging, carbon-neutral shipping, and eco-friendly product options.</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
              <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="fas fa-users fa-2x"></i>
              </div>
              <h5>Community</h5>
              <p class="text-muted">We believe in building strong relationships with our customers, suppliers, and local communities to create positive impact wherever we operate.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Team Section -->
  <section class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center mb-5">
          <h2>Meet Our Team</h2>
          <p class="lead">The passionate people behind SadaCart</p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card border-0 text-center">
            <div class="card-body">
              <div class="member-image mx-auto mb-3" style="width: 100px; height: 100px;">
                <img src="<?= asset('images/team-ceo.jpg') ?>" alt="Sarah Johnson" class="img-fluid rounded-circle">
              </div>
              <h5>Sarah Johnson</h5>
              <p class="text-muted">CEO & Founder</p>
              <p class="small">With over 10 years of experience in e-commerce and retail, Sarah leads our vision of making online shopping better for everyone.</p>
              <div class="social-links">
                <a href="#" class="text-primary me-2" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="text-info me-2" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card border-0 text-center">
            <div class="card-body">
              <div class="member-image mx-auto mb-3" style="width: 100px; height: 100px;">
                <img src="<?= asset('images/team-cto.jpg') ?>" alt="Michael Chen" class="img-fluid rounded-circle">
              </div>
              <h5>Michael Chen</h5>
              <p class="text-muted">CTO</p>
              <p class="small">Michael oversees our technology infrastructure and ensures our platform remains secure, fast, and user-friendly.</p>
              <div class="social-links">
                <a href="#" class="text-primary me-2" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="text-secondary me-2" aria-label="GitHub"><i class="fab fa-github"></i></a>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card border-0 text-center">
            <div class="card-body">
              <div class="member-image mx-auto mb-3" style="width: 100px; height: 100px;">
                <img src="<?= asset('images/team-cmo.jpg') ?>" alt="Emily Rodriguez" class="img-fluid rounded-circle">
              </div>
              <h5>Emily Rodriguez</h5>
              <p class="text-muted">Head of Marketing</p>
              <p class="small">Emily leads our marketing efforts and customer engagement strategies to help you discover amazing products.</p>
              <div class="social-links">
                <a href="#" class="text-primary me-2" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="text-dark me-2" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card border-0 text-center">
            <div class="card-body">
              <div class="member-image mx-auto mb-3" style="width: 100px; height: 100px;">
                <img src="<?= asset('images/team-cs.jpg') ?>" alt="David Thompson" class="img-fluid rounded-circle">
              </div>
              <h5>David Thompson</h5>
              <p class="text-muted">Customer Success Manager</p>
              <p class="small">David ensures every customer has an exceptional experience and leads our customer support team.</p>
              <div class="social-links">
                <a href="#" class="text-primary me-2" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="text-info me-2" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Stats Section -->
  <section class="bg-primary text-white py-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="stat-item">
            <h2 class="display-4 fw-bold">50K+</h2>
            <p class="lead">Happy Customers</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="stat-item">
            <h2 class="display-4 fw-bold">10K+</h2>
            <p class="lead">Products</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="stat-item">
            <h2 class="display-4 fw-bold">99.9%</h2>
            <p class="lead">Uptime</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="stat-item">
            <h2 class="display-4 fw-bold">24/7</h2>
            <p class="lead">Support</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Contact CTA -->
  <section class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto text-center">
          <h2 class="mb-3">Get in Touch</h2>
          <p class="lead mb-4">Have questions about our company or want to learn more about partnership opportunities? We'd love to hear from you.</p>
          <div class="d-flex justify-content-center gap-3">
            <a href="/contact" class="btn btn-primary btn-lg">
              <i class="fas fa-envelope me-2"></i>Contact Us
            </a>
            <a href="/blog" class="btn btn-secondary btn-lg">
              <i class="fas fa-newspaper me-2"></i>Read Our Blog
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <?php include APP_PATH . '/Views/layouts/footer.php'; ?>
  
  <script src="<?= asset('js/app.min.js') ?>"></script>
  
  <style>
    .member-image {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .social-links a {
      font-size: 1.2rem;
      transition: transform 0.2s ease;
    }
    
    .social-links a:hover {
      transform: translateY(-2px);
    }
    
    .stat-item {
      padding: 2rem 0;
    }
    
    .card:hover {
      transform: translateY(-5px);
      transition: transform 0.3s ease;
    }
  </style>
</body>
</html>
