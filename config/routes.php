<?php
/**
 * Routes Configuration
 */

function loadRoutes($router) {
    // Home routes
    $router->get('/', 'HomeController@index');
    
    // Product routes
    $router->get('/products', 'ProductController@index');
    $router->get('/products/category/{slug}', 'ProductController@category');
    $router->get('/products/{slug}', 'ProductController@show');
    $router->get('/search', 'ProductController@search');
    
    // Cart routes (require CSRF for modifications)
    $router->get('/cart', 'CartController@index');
    $router->post('/cart/add', 'CartController@add', ['csrf']);
$router->post('/cart/update', 'CartController@update', ['csrf']);
$router->post('/cart/remove', 'CartController@remove', ['csrf']);
$router->post('/cart/clear', 'CartController@clear', ['csrf']);
$router->post('/cart/apply-coupon', 'CartController@applyCoupon', ['csrf']);
$router->get('/cart/mini', 'CartController@mini');
$router->get('/cart/count', 'CartController@count');
    
    // Checkout routes (require auth and CSRF)
    $router->get('/checkout', 'CheckoutController@index', ['auth']);
    $router->post('/checkout/process', 'CheckoutController@process', ['auth', 'csrf']);
    $router->get('/checkout/success', 'CheckoutController@success', ['auth']);
    $router->get('/checkout/cancel', 'CheckoutController@cancel', ['auth']);
    
    // Auth routes (guest only for login/register)
    $router->get('/login', 'AuthController@showLogin', ['guest']);
    $router->post('/login', 'AuthController@login', ['guest', 'csrf']);
    $router->get('/register', 'AuthController@showRegister', ['guest']);
    $router->post('/register', 'AuthController@register', ['guest', 'csrf']);
    $router->get('/logout', 'AuthController@logout', ['auth']);
    $router->get('/forgot-password', 'AuthController@showForgotPassword', ['guest']);
    $router->post('/forgot-password', 'AuthController@forgotPassword', ['guest', 'csrf']);
    $router->get('/reset-password/{token}', 'AuthController@showResetPassword', ['guest']);
    $router->post('/reset-password', 'AuthController@resetPassword', ['guest', 'csrf']);
    
    // User dashboard routes (require authentication)
    $router->get('/dashboard', 'DashboardController@index', ['auth']);
    $router->get('/dashboard/orders', 'DashboardController@orders', ['auth']);
    $router->get('/dashboard/orders/{id}', 'DashboardController@orderDetail', ['auth']);
    $router->get('/dashboard/profile', 'DashboardController@profile', ['auth']);
    $router->post('/dashboard/profile', 'DashboardController@updateProfile', ['auth', 'csrf']);
    $router->get('/dashboard/addresses', 'DashboardController@addresses', ['auth']);
    $router->post('/dashboard/addresses', 'DashboardController@saveAddress', ['auth', 'csrf']);
    
    // Wishlist routes (require authentication)
    $router->get('/wishlist', 'WishlistController@index', ['auth']);
    $router->post('/wishlist/add', 'WishlistController@add', ['auth', 'csrf']);
    $router->post('/wishlist/remove', 'WishlistController@remove', ['auth', 'csrf']);
    
    // Blog routes
    $router->get('/blog', 'BlogController@index');
    $router->get('/blog/{slug}', 'BlogController@show');
    
    // Static pages
    $router->get('/about', 'PageController@about');
    $router->get('/contact', 'PageController@contact');
    $router->post('/contact', 'PageController@submitContact', ['csrf']);
    $router->get('/faq', 'PageController@faq');
    $router->get('/privacy-policy', 'PageController@privacy');
    $router->get('/terms', 'PageController@terms');
    $router->get('/shipping-info', 'PageController@shipping');
    $router->get('/returns', 'PageController@returns');
    $router->get('/how-to', 'PageController@howTo');
    $router->get('/fbs', 'PageController@fbs');
    $router->get('/affiliate', 'PageController@affiliate');
    $router->get('/knowledge-base', 'PageController@knowledgeBase');
    
    // Admin routes group - all require admin privileges
    $router->group('/admin', ['admin'], function($router) {
        $router->get('', 'AdminController@dashboard');
        
        // Product management
        $router->get('/products', 'AdminController@products');
        $router->get('/products/create', 'AdminController@createProduct');
        $router->post('/products', 'AdminController@storeProduct', ['csrf']);
        $router->get('/products/{id}/edit', 'AdminController@editProduct');
        $router->post('/products/{id}', 'AdminController@updateProduct', ['csrf']);
        $router->post('/products/{id}/delete', 'AdminController@deleteProduct', ['csrf']);
        
        // Category management
        $router->get('/categories', 'AdminController@categories');
        $router->get('/categories/create', 'AdminController@createCategory');
        $router->post('/categories', 'AdminController@storeCategory', ['csrf']);
        $router->get('/categories/{id}/edit', 'AdminController@editCategory');
        $router->post('/categories/{id}', 'AdminController@updateCategory', ['csrf']);
        
        // Order management
        $router->get('/orders', 'AdminController@orders');
        $router->get('/orders/{id}', 'AdminController@orderDetail');
        $router->post('/orders/{id}/status', 'AdminController@updateOrderStatus', ['csrf']);
        
        // User management
        $router->get('/users', 'AdminController@users');
        $router->get('/users/{id}', 'AdminController@userDetail');
        
        // Settings
        $router->get('/settings', 'AdminController@settings');
        $router->post('/settings', 'AdminController@updateSettings', ['csrf']);
        
        // Blog management
        $router->get('/blog', 'AdminController@blogPosts');
        $router->get('/blog/create', 'AdminController@createBlogPost');
        $router->post('/blog', 'AdminController@storeBlogPost', ['csrf']);
        $router->get('/blog/{id}/edit', 'AdminController@editBlogPost');
        $router->post('/blog/{id}', 'AdminController@updateBlogPost', ['csrf']);
    });
    
    // API routes (with api middleware for proper headers)
    $router->post('/api/newsletter/subscribe', 'ApiController@subscribeNewsletter', ['api', 'csrf']);
    $router->get('/api/products/search', 'ApiController@searchProducts', ['api']);
    $router->post('/api/reviews', 'ApiController@submitReview', ['api', 'auth', 'csrf']);
}
