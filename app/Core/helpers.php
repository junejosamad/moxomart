<?php
/**
 * Helper Functions
 */

// Prevent multiple inclusions
if (!function_exists('url')) {

// URL helpers
function url($path = '') {
    $baseUrl = $_ENV['APP_URL'] ?? null;
    
    // Auto-detect base URL if not set in environment
    if (!$baseUrl) {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Use HTTPS if the request is secure, otherwise HTTP
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        
        // Get the folder path from SCRIPT_NAME
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $folderPath = dirname($scriptName);
        
        // If we're in a subfolder, include it in the base URL
        if ($folderPath !== '/' && $folderPath !== '') {
            $baseUrl = $protocol . '://' . $host . $folderPath;
        } else {
            $baseUrl = $protocol . '://' . $host;
        }
    }
    
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function asset($path) {
    // Clean the path
    $path = ltrim($path, '/');
    
    // Get base URL from environment or auto-detect
    $baseUrl = $_ENV['APP_URL'] ?? null;
    
    // Auto-detect base URL if not set in environment
    if (!$baseUrl) {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Use HTTPS if the request is secure, otherwise HTTP
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        
        // Get the folder path from SCRIPT_NAME
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $folderPath = dirname($scriptName);
        
        // If we're in a subfolder, include it in the base URL
        if ($folderPath !== '/' && $folderPath !== '') {
            $baseUrl = $protocol . '://' . $host . $folderPath;
        } else {
            $baseUrl = $protocol . '://' . $host;
        }
    }
    
    // Ensure we don't have double /assets/ in the path
    if (strpos($path, 'assets/') === 0) {
        return rtrim($baseUrl, '/') . '/' . $path;
    } else {
        return rtrim($baseUrl, '/') . '/assets/' . $path;
    }
}

function redirect($url, $statusCode = 302) {
    header("Location: " . url($url), true, $statusCode);
    exit;
}

// Authentication helpers
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    if (!isset($_SESSION['user_data'])) {
        $userModel = new App\Models\User();
        $_SESSION['user_data'] = $userModel->find($_SESSION['user_id']);
    }
    
    return $_SESSION['user_data'];
}

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

function login($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_data'] = $user;
}

function logout() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_data']);
    session_destroy();
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

// CSRF protection
function csrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function csrfVerify() {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// Validation helpers
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $fieldRules) {
        $value = $data[$field] ?? null;
        $fieldRules = explode('|', $fieldRules);
        
        foreach ($fieldRules as $rule) {
            $ruleParts = explode(':', $rule);
            $ruleName = $ruleParts[0];
            $ruleValue = $ruleParts[1] ?? null;
            
            switch ($ruleName) {
                case 'required':
                    if (empty($value)) {
                        $errors[$field][] = ucfirst($field) . ' is required.';
                    }
                    break;
                    
                case 'email':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = ucfirst($field) . ' must be a valid email address.';
                    }
                    break;
                    
                case 'min':
                    if (!empty($value) && strlen($value) < $ruleValue) {
                        $errors[$field][] = ucfirst($field) . " must be at least {$ruleValue} characters.";
                    }
                    break;
                    
                case 'max':
                    if (!empty($value) && strlen($value) > $ruleValue) {
                        $errors[$field][] = ucfirst($field) . " must not exceed {$ruleValue} characters.";
                    }
                    break;
                    
                case 'numeric':
                    if (!empty($value) && !is_numeric($value)) {
                        $errors[$field][] = ucfirst($field) . ' must be a number.';
                    }
                    break;
                    
                case 'unique':
                    if (!empty($value)) {
                        list($table, $column) = explode(',', $ruleValue);
                        $db = App\Core\Database::getInstance();
                        $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
                        $stmt->execute([$value]);
                        if ($stmt->fetchColumn() > 0) {
                            $errors[$field][] = ucfirst($field) . ' already exists.';
                        }
                    }
                    break;
            }
        }
    }
    
    return $errors;
}

// String helpers
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function truncate($string, $length = 100, $suffix = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . $suffix;
}

function slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

function generateSlug($string) {
    return slug($string);
}

function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Formatting helpers
function formatPrice($price, $currency = 'PKR') {
    return $currency . ' ' . number_format($price, 2);
}

function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'M j, Y g:i A') {
    return date($format, strtotime($datetime));
}

// Cart helpers
function getCartCount() {
    if (isLoggedIn()) {
        $cartModel = new App\Models\Cart();
        return $cartModel->getItemCount(getCurrentUser()['id']);
    } else {
        $sessionCart = $_SESSION['cart'] ?? [];
        return array_sum(array_column($sessionCart, 'quantity'));
    }
}

function getCartTotal() {
    if (isLoggedIn()) {
        $cartModel = new App\Models\Cart();
        return $cartModel->getTotal(getCurrentUser()['id']);
    } else {
        $total = 0;
        $sessionCart = $_SESSION['cart'] ?? [];
        $productModel = new App\Models\Product();
        
        foreach ($sessionCart as $item) {
            $product = $productModel->find($item['product_id']);
            if ($product) {
                $total += $product['price'] * $item['quantity'];
            }
        }
        
        return $total;
    }
}

// Settings helpers
function getSetting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        $db = App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT key_name, value FROM settings");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key_name']] = $row['value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

function setSetting($key, $value) {
    $db = App\Core\Database::getInstance();
    $stmt = $db->prepare("INSERT INTO settings (key_name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
    return $stmt->execute([$key, $value, $value]);
}

// File upload helpers
function uploadFile($file, $directory = 'uploads') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    
    $uploadDir = PUBLIC_PATH . '/assets/' . $directory . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $directory . '/' . $filename;
    }
    
    return false;
}

// Pagination helpers
function paginate($currentPage, $totalPages, $url) {
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage - 1) . '">Previous</a></li>';
    }
    
    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage + 1) . '">Next</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

// Security helpers
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function generateMetaTags($title, $description, $image = null, $url = null) {
    $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost';
    $currentUrl = $url ?? ($_SERVER['REQUEST_URI'] ?? '');
    $fullUrl = $baseUrl . $currentUrl;
    $imageUrl = $image ? asset($image) : asset('images/og-default.jpg');
    
    return [
        'title' => $title,
        'description' => $description,
        'og_title' => $title,
        'og_description' => $description,
        'og_image' => $imageUrl,
        'og_url' => $fullUrl,
        'twitter_title' => $title,
        'twitter_description' => $description,
        'twitter_image' => $imageUrl
    ];
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Email helpers
function sendEmail($to, $subject, $body, $isHtml = true) {
    // This would integrate with PHPMailer or similar
    // For now, return true as placeholder
    return true;
}

// Image helpers
function resizeImage($source, $destination, $width, $height) {
    // This would integrate with Intervention Image or similar
    // For now, return true as placeholder
    return true;
}

// Cache helpers
function cache($key, $value = null, $ttl = 3600) {
    static $cache = [];
    
    if ($value === null) {
        return $cache[$key] ?? null;
    }
    
    $cache[$key] = $value;
    return $value;
}

// Debug helpers
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

// Activity logging helper
function logActivity($action, $description = null, $userId = null) {
    try {
        $db = App\Core\Database::getInstance();
        
        if ($userId === null && isLoggedIn()) {
            $user = getCurrentUser();
            $userId = $user['id'];
        }
        
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $userId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (\Exception $e) {
        error_log("Activity logging error: " . $e->getMessage());
        return false;
    }
}

} // End of function_exists('url') check
