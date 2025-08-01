<?php

namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller
{
    public function about()
    {
        $meta = [
            'title' => 'About Us - Moxo Mart',
            'description' => 'Learn about Moxo Mart - our mission, values, and commitment to providing exceptional e-commerce experience.'
        ];
        
        return $this->render('static/about', [
            'meta' => $meta
        ]);
    }

    public function contact()
    {
        $meta = [
            'title' => 'Contact Us - Moxo Mart',
            'description' => 'Get in touch with Moxo Mart. Find our contact information, location, and send us a message.'
        ];
        
        return $this->render('static/contact', [
            'meta' => $meta
        ]);
    }

    public function submitContact()
    {
        $errors = validate($_POST, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'required|max:255',
            'message' => 'required|max:1000'
        ]);

        if (!empty($errors)) {
            return $this->render('static/contact', [
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        try {
            // Save to database
            $stmt = $this->db->prepare("INSERT INTO contact_submissions (name, email, subject, message, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['subject'],
                $_POST['message'],
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
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

            logActivity('contact_form_submitted', "Contact form submitted by {$_POST['name']} ({$_POST['email']})");

            setFlash('success', 'Thank you for your message! We\'ll get back to you soon.');
            return $this->redirect('/contact');

        } catch (\Exception $e) {
            error_log("Contact form error: " . $e->getMessage());
            setFlash('error', 'Sorry, there was an error sending your message. Please try again.');
            return $this->redirect('/contact');
        }
    }

    public function faq()
    {
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
                'answer' => 'Standard shipping takes 2-4 business days within Pakistan. Same day delivery available for major cities.'
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 7-day return policy on most items. Items must be in original condition with tags attached.'
            ],
            [
                'question' => 'How can I track my order?',
                'answer' => 'Once your order ships, you\'ll receive a tracking number via SMS and email. You can also track orders in your account dashboard.'
            ],
            [
                'question' => 'Do you offer customer support?',
                'answer' => 'Yes! We provide customer support via email, phone, and live chat during business hours.'
            ]
        ];

        $meta = [
            'title' => 'Frequently Asked Questions - Moxo Mart',
            'description' => 'Find answers to common questions about ordering, shipping, returns, and more at Moxo Mart.'
        ];
        
        return $this->render('static/faq', [
            'faqs' => $faqs,
            'meta' => $meta
        ]);
    }

    public function privacy()
    {
        $meta = [
            'title' => 'Privacy Policy - Moxo Mart',
            'description' => 'Read our privacy policy to understand how we collect, use, and protect your personal information.'
        ];
        
        return $this->render('static/privacy-policy', [
            'meta' => $meta
        ]);
    }

    public function terms()
    {
        $meta = [
            'title' => 'Terms & Conditions - Moxo Mart',
            'description' => 'Read our terms and conditions for using Moxo Mart services.'
        ];
        
        return $this->render('static/terms', [
            'meta' => $meta
        ]);
    }

    public function shipping()
    {
        $meta = [
            'title' => 'Shipping Information - Moxo Mart',
            'description' => 'Learn about our shipping policies, delivery times, and costs.'
        ];
        
        return $this->render('static/shipping-info', [
            'meta' => $meta
        ]);
    }

    public function returns()
    {
        $meta = [
            'title' => 'Returns & Exchanges - Moxo Mart',
            'description' => 'Information about our return and exchange policy.'
        ];
        
        return $this->render('static/returns', [
            'meta' => $meta
        ]);
    }

    public function howTo()
    {
        $meta = [
            'title' => 'How To Guide - Moxo Mart',
            'description' => 'Step-by-step guides for using our website and services.'
        ];
        
        return $this->render('static/how-to', [
            'meta' => $meta
        ]);
    }

    public function fbs()
    {
        $meta = [
            'title' => 'Fulfillment by Store - Moxo Mart',
            'description' => 'Learn about our FBS program for sellers.'
        ];
        
        return $this->render('static/fbs', [
            'meta' => $meta
        ]);
    }

    public function affiliate()
    {
        $meta = [
            'title' => 'Affiliate Program - Moxo Mart',
            'description' => 'Join our affiliate program and earn commission on referrals.'
        ];
        
        return $this->render('static/affiliate', [
            'meta' => $meta
        ]);
    }

    public function knowledgeBase()
    {
        $meta = [
            'title' => 'Knowledge Base - Moxo Mart',
            'description' => 'Comprehensive guides and tutorials for using Moxo Mart.'
        ];
        
        return $this->render('static/knowledge-base', [
            'meta' => $meta
        ]);
    }
} 