<?php
/**
 * Home Controller
 * Handles homepage and static pages
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\BlogPost;

class HomeController extends Controller {
  
  /**
   * Homepage
   */
  public function index() {
    $productModel = new Product();
    $categoryModel = new Category();
    
    // Get featured products
    $featuredProducts = $productModel->getFeatured(8);
    
    // Get main categories
    $categories = $categoryModel->getMainCategories();
    
    // Get recent blog posts
    $recentPosts = [];
    if (class_exists('App\Models\BlogPost')) {
        $blogModel = new BlogPost();
        $recentPosts = $blogModel->getRecent(3);
    }
    
    // SEO meta data
    $meta = [
        'title' => 'Moxo Mart - Your Trusted E-commerce Partner',
        'description' => 'Discover quality products at Moxo Mart. Shop electronics, clothing, home goods and more with fast shipping and 24/7 support.',
        'og_title' => 'Moxo Mart - Pakistan\'s Leading E-commerce Platform',
        'og_description' => 'Shop with confidence at Moxo Mart. Quality products, competitive prices, and exceptional service.',
        'og_image' => asset('images/og-home.jpg')
    ];
    
    return $this->render('home/index', [
        'featuredProducts' => $featuredProducts,
        'categories' => $categories,
        'recentPosts' => $recentPosts,
        'meta' => $meta
    ]);
  }
  
  /**
   * About page
   */
  public function about() {
    $meta = generateMetaTags(
      'About Us',
      'Learn about Moxo Mart - our mission, values, and commitment to providing exceptional e-commerce experience.',
      asset('images/og-about.jpg')
    );
    
    $this->view('home.about', [
      'meta' => $meta
    ]);
  }
  
  /**
   * Contact page
   */
  public function contact() {
    $meta = generateMetaTags(
      'Contact Us',
      'Get in touch with Moxo Mart. Find our contact information, location, and send us a message.',
      asset('images/og-contact.jpg')
    );
    
    $this->view('home.contact', [
      'meta' => $meta
    ]);
  }
  
  /**
   * Handle contact form submission
   */
  public function contactSubmit() {
    $this->validateCsrf();
    
    $errors = $this->validate($_POST, [
      'name' => 'required|max:100',
      'email' => 'required|email|max:255',
      'subject' => 'required|max:255',
      'message' => 'required|max:1000'
    ]);
    
    if (!empty($errors)) {
      $this->flash('error', 'Please correct the errors below.');
      $this->view('home.contact', [
        'errors' => $errors,
        'old' => $_POST
      ]);
      return;
    }
    
    try {
      // Save to database
      $db = db();
      $stmt = $db->prepare("INSERT INTO contact_submissions (name, email, subject, message, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
      $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['subject'],
        $_POST['message'],
        $_SERVER['REMOTE_ADDR']
      ]);
      
      // Send email notification
      $emailBody = "
        <h2>New Contact Form Submission</h2>
        <p><strong>Name:</strong> {$_POST['name']}</p>
        <p><strong>Email:</strong> {$_POST['email']}</p>
        <p><strong>Subject:</strong> {$_POST['subject']}</p>
        <p><strong>Message:</strong></p>
        <p>" . nl2br(e($_POST['message'])) . "</p>
      ";
      
      sendEmail(
        getSetting('contact_email', 'admin@moxomart.com'),
        'New Contact Form Submission: ' . $_POST['subject'],
        $emailBody
      );
      
      // Log activity
      logActivity('contact_form_submitted', "Contact form submitted by {$_POST['name']} ({$_POST['email']})");
      
      $this->flash('success', 'Thank you for your message! We\'ll get back to you soon.');
      $this->redirect('/contact');
      
    } catch (\Exception $e) {
      error_log("Contact form error: " . $e->getMessage());
      $this->flash('error', 'Sorry, there was an error sending your message. Please try again.');
      $this->redirect('/contact');
    }
  }
  
  /**
   * FAQ page
   */
  public function faq() {
    $faqs = [
      [
        'question' => 'How do I place an order?',
        'answer' => 'Browse our products, add items to your cart, and proceed to checkout. You can order as a guest or create an account for faster future purchases.'
      ],
      [
        'question' => 'What payment methods do you accept?',
        'answer' => 'We accept major credit cards, PayPal, JazzCash, EasyPaisa, and Cash on Delivery (COD) for eligible locations.'
      ],
      [
        'question' => 'How long does shipping take?',
        'answer' => 'Standard shipping takes 2-4 business days within Pakistan. Same day booking available for major cities.'
      ],
      [
        'question' => 'What is your return policy?',
        'answer' => 'We offer a 7-day return policy on most items. Items must be in original condition with tags attached.'
      ],
      [
        'question' => 'How can I track my order?',
        'answer' => 'Once your order ships, you\'ll receive a tracking number via SMS and email. You can also track orders in your account dashboard with live tracking.'
      ],
      [
        'question' => 'Do you offer WhatsApp support?',
        'answer' => 'Yes! We provide 24/7 WhatsApp support for all your queries and order assistance.'
      ]
    ];
    
    $meta = generateMetaTags(
      'Frequently Asked Questions',
      'Find answers to common questions about ordering, shipping, returns, and more at Moxo Mart.',
      asset('images/og-faq.jpg')
    );
    
    $this->view('home.faq', [
      'faqs' => $faqs,
      'meta' => $meta
    ]);
  }
}
?>
