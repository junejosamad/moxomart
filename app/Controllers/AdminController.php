<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class AdminController extends Controller
{
    private $userModel;
    private $productModel;
    private $categoryModel;
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
    }

    public function dashboard()
    {
        // Get dashboard statistics
        $stats = [
            'total_users' => $this->userModel->count(['role' => 'customer']),
            'total_products' => $this->productModel->count(['status' => 'active']),
            'total_orders' => $this->orderModel->count(),
            'pending_orders' => $this->orderModel->count(['status' => 'pending']),
            'total_revenue' => $this->getTotalRevenue(),
            'monthly_revenue' => $this->getMonthlyRevenue()
        ];

        // Get recent orders
        $recentOrders = $this->getRecentOrders(10);
        
        // Get low stock products
        $lowStockProducts = $this->getLowStockProducts();

        $meta = [
            'title' => 'Admin Dashboard - Moxo Mart',
            'description' => 'Manage your e-commerce store'
        ];

        return $this->render('admin/dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'meta' => $meta
        ]);
    }

    // Product Management
    public function products()
    {
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';

        $conditions = [];
        if ($search) {
            $conditions['search'] = $search;
        }
        if ($category) {
            $conditions['category_id'] = $category;
        }
        if ($status) {
            $conditions['status'] = $status;
        }

        $products = $this->productModel->paginateWithDetails($page, 20, $conditions, 'created_at DESC');
        $categories = $this->categoryModel->all('name ASC');

        return $this->render('admin/products/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'selectedStatus' => $status
        ]);
    }

    public function createProduct()
    {
        $categories = $this->categoryModel->all('name ASC');
        
        return $this->render('admin/products/form', [
            'categories' => $categories,
            'isEdit' => false
        ]);
    }

    public function storeProduct()
    {
        $errors = validate($_POST, [
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:products,slug',
            'description' => 'required',
            'short_description' => 'required|max:500',
            'sku' => 'required|max:100|unique:products,sku',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'status' => 'required'
        ]);

        if (!empty($errors)) {
            $categories = $this->categoryModel->all('name ASC');
            return $this->render('admin/products/form', [
                'errors' => $errors,
                'old' => $_POST,
                'categories' => $categories,
                'isEdit' => false
            ]);
        }

        try {
            $productData = [
                'name' => $_POST['name'],
                'slug' => $_POST['slug'],
                'description' => $_POST['description'],
                'short_description' => $_POST['short_description'],
                'sku' => $_POST['sku'],
                'price' => $_POST['price'],
                'compare_price' => $_POST['compare_price'] ?? null,
                'cost_price' => $_POST['cost_price'] ?? null,
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'track_quantity' => isset($_POST['track_quantity']) ? 1 : 0,
                'weight' => $_POST['weight'] ?? null,
                'category_id' => $_POST['category_id'],
                'brand' => $_POST['brand'] ?? null,
                'status' => $_POST['status'],
                'featured' => isset($_POST['featured']) ? 1 : 0,
                'meta_title' => $_POST['meta_title'] ?? null,
                'meta_description' => $_POST['meta_description'] ?? null
            ];

            $productId = $this->productModel->create($productData);

            logActivity('product_created', "Product created: {$_POST['name']}");

            setFlash('success', 'Product created successfully.');
            return $this->redirect('/admin/products');

        } catch (\Exception $e) {
            error_log("Product creation error: " . $e->getMessage());
            setFlash('error', 'Failed to create product. Please try again.');
            return $this->redirect('/admin/products/create');
        }
    }

    public function editProduct($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found.');
            return $this->redirect('/admin/products');
        }

        $categories = $this->categoryModel->all('name ASC');
        
        return $this->render('admin/products/form', [
            'product' => $product,
            'categories' => $categories,
            'isEdit' => true
        ]);
    }

    public function updateProduct($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found.');
            return $this->redirect('/admin/products');
        }

        $errors = validate($_POST, [
            'name' => 'required|max:255',
            'slug' => 'required|max:255',
            'description' => 'required',
            'short_description' => 'required|max:500',
            'sku' => 'required|max:100',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'status' => 'required'
        ]);

        // Check unique constraints excluding current product
        if ($_POST['slug'] !== $product['slug']) {
            $existing = $this->productModel->findBy('slug', $_POST['slug']);
            if ($existing && $existing['id'] != $id) {
                $errors['slug'][] = 'Slug already exists.';
            }
        }

        if ($_POST['sku'] !== $product['sku']) {
            $existing = $this->productModel->findBy('sku', $_POST['sku']);
            if ($existing && $existing['id'] != $id) {
                $errors['sku'][] = 'SKU already exists.';
            }
        }

        if (!empty($errors)) {
            $categories = $this->categoryModel->all('name ASC');
            return $this->render('admin/products/form', [
                'errors' => $errors,
                'old' => $_POST,
                'product' => $product,
                'categories' => $categories,
                'isEdit' => true
            ]);
        }

        try {
            $productData = [
                'name' => $_POST['name'],
                'slug' => $_POST['slug'],
                'description' => $_POST['description'],
                'short_description' => $_POST['short_description'],
                'sku' => $_POST['sku'],
                'price' => $_POST['price'],
                'compare_price' => $_POST['compare_price'] ?? null,
                'cost_price' => $_POST['cost_price'] ?? null,
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'track_quantity' => isset($_POST['track_quantity']) ? 1 : 0,
                'weight' => $_POST['weight'] ?? null,
                'category_id' => $_POST['category_id'],
                'brand' => $_POST['brand'] ?? null,
                'status' => $_POST['status'],
                'featured' => isset($_POST['featured']) ? 1 : 0,
                'meta_title' => $_POST['meta_title'] ?? null,
                'meta_description' => $_POST['meta_description'] ?? null
            ];

            $this->productModel->update($id, $productData);

            logActivity('product_updated', "Product updated: {$_POST['name']}");

            setFlash('success', 'Product updated successfully.');
            return $this->redirect('/admin/products');

        } catch (\Exception $e) {
            error_log("Product update error: " . $e->getMessage());
            setFlash('error', 'Failed to update product. Please try again.');
            return $this->redirect("/admin/products/{$id}/edit");
        }
    }

    public function deleteProduct($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        try {
            $this->productModel->delete($id);
            
            logActivity('product_deleted', "Product deleted: {$product['name']}");
            
            return $this->json(['success' => true, 'message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            error_log("Product deletion error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to delete product'], 500);
        }
    }

    // Order Management
    public function orders()
    {
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';

        $conditions = [];
        if ($status) {
            $conditions['status'] = $status;
        }

        $orders = $this->orderModel->paginate($page, 20, $conditions, 'created_at DESC');

        // Get order statistics
        $stats = [
            'total_orders' => $this->orderModel->count(),
            'total_revenue' => $this->getTotalRevenue(),
            'pending_orders' => $this->orderModel->count(['status' => 'pending']),
            'processing_orders' => $this->orderModel->count(['status' => 'processing'])
        ];

        return $this->render('admin/orders/index', [
            'orders' => $orders,
            'selectedStatus' => $status,
            'stats' => $stats
        ]);
    }

    public function orderDetail($id)
    {
        $order = $this->orderModel->getOrderWithItems($id);
        if (!$order) {
            setFlash('error', 'Order not found.');
            return $this->redirect('/admin/orders');
        }

        return $this->render('admin/orders/detail', [
            'order' => $order
        ]);
    }

    public function updateOrderStatus($id)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $newStatus = $_POST['status'] ?? '';
        $notes = $_POST['notes'] ?? null;

        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        if (!in_array($newStatus, $allowedStatuses)) {
            return $this->json(['success' => false, 'message' => 'Invalid status'], 400);
        }

        try {
            $this->orderModel->updateStatus($id, $newStatus, $notes);
            
            logActivity('order_status_updated', "Order #{$order['order_number']} status updated to: {$newStatus}");
            
            return $this->json(['success' => true, 'message' => 'Order status updated successfully']);
        } catch (\Exception $e) {
            error_log("Order status update error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to update order status'], 500);
        }
    }

    // User Management
    public function users()
    {
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        $verified = $_GET['verified'] ?? '';

        $conditions = [];
        if ($search) {
            $conditions['search'] = $search;
        }
        if ($role) {
            $conditions['role'] = $role;
        } else {
            // Default to showing customers only
            $conditions['role'] = 'customer';
        }
        if ($status) {
            $conditions['is_active'] = ($status === 'active') ? 1 : 0;
        }
        if ($verified !== '') {
            $conditions['email_verified'] = (int)$verified;
        }

        $users = $this->userModel->paginate($page, 20, $conditions, 'created_at DESC');

        // Get user statistics (always for customers only)
        $stats = [
            'total_users' => $this->userModel->count(['role' => 'customer']),
            'active_users' => $this->userModel->count(['role' => 'customer', 'is_active' => 1]),
            'new_users_today' => $this->getNewUsersToday(),
            'verified_users' => $this->userModel->count(['role' => 'customer', 'email_verified' => 1])
        ];

        return $this->render('admin/users/index', [
            'users' => $users,
            'search' => $search,
            'filters' => [
                'role' => $role,
                'status' => $status,
                'verified' => $verified
            ],
            'stats' => $stats
        ]);
    }

    public function userDetail($id)
    {
        $user = $this->userModel->find($id);
        if (!$user || $user['role'] === 'admin') {
            setFlash('error', 'User not found.');
            return $this->redirect('/admin/users');
        }

        $orders = $this->userModel->getOrders($id);
        
        return $this->render('admin/users/detail', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    // Settings
    public function settings()
    {
        $settings = $this->getSettings();
        
        return $this->render('admin/settings', [
            'settings' => $settings
        ]);
    }

    public function updateSettings()
    {
        try {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'setting_') === 0) {
                    $settingKey = substr($key, 8); // Remove 'setting_' prefix
                    setSetting($settingKey, $value);
                }
            }

            logActivity('settings_updated', 'Site settings updated');

            setFlash('success', 'Settings updated successfully.');
            return $this->redirect('/admin/settings');

        } catch (\Exception $e) {
            error_log("Settings update error: " . $e->getMessage());
            setFlash('error', 'Failed to update settings. Please try again.');
            return $this->redirect('/admin/settings');
        }
    }

    // Helper methods
    private function getTotalRevenue()
    {
        $stmt = $this->db->prepare("SELECT SUM(total_amount) FROM orders WHERE status NOT IN ('cancelled', 'refunded')");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    }

    private function getMonthlyRevenue()
    {
        $stmt = $this->db->prepare("SELECT SUM(total_amount) FROM orders WHERE status NOT IN ('cancelled', 'refunded') AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    }

    private function getRecentOrders($limit = 10)
    {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getLowStockProducts($threshold = 10)
    {
        $sql = "SELECT * FROM products 
                WHERE track_quantity = 1 
                AND stock_quantity <= ? 
                AND status = 'active' 
                ORDER BY stock_quantity ASC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    private function getNewUsersToday()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = 'customer' AND DATE(created_at) = CURDATE()");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    }

    private function getSettings()
    {
        $stmt = $this->db->prepare("SELECT key_name, value FROM settings");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key_name']] = $row['value'];
        }
        
        return $settings;
    }
// } 


// <?php

// class AdminController extends Controller
// {
//     private $userModel;
//     private $productModel;
//     private $categoryModel;
//     private $orderModel;
//     private $reviewModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
        $this->reviewModel = new Review();
        
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            redirect('/auth/login');
        }
    }

    /**
     * Category management
     */
    public function categories()
    {
        $categories = $this->categoryModel->getAllWithProductCount();
        
        $data = [
            'categories' => $categories,
            'page_title' => 'Manage Categories'
        ];
        
        $this->view('admin/categories/index', $data);
    }

    /**
     * Create category form
     */
    public function createCategory()
    {
        $parentCategories = $this->categoryModel->getParentCategories();
        
        $data = [
            'parent_categories' => $parentCategories,
            'page_title' => 'Create Category'
        ];
        
        $this->view('admin/categories/create', $data);
    }

    /**
     * Store new category
     */
    public function storeCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/categories/create');
        }
        
        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDescription = trim($_POST['meta_description'] ?? '');
        
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Category name is required';
        }
        
        // Check if category name already exists
        if ($this->categoryModel->findByName($name)) {
            $errors[] = 'Category name already exists';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            redirect('/admin/categories/create');
        }
        
        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'public/assets/images/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = 'category_' . time() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = '/assets/images/categories/' . $fileName;
            }
        }
        
        // Generate slug
        $slug = $this->generateSlug($name);
        
        $categoryData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'parent_id' => $parentId ?: null,
            'image' => $imagePath,
            'is_active' => $isActive,
            'meta_title' => $metaTitle ?: $name,
            'meta_description' => $metaDescription,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->categoryModel->create($categoryData)) {
            $_SESSION['success'] = 'Category created successfully';
            redirect('/admin/categories');
        } else {
            $_SESSION['error'] = 'Failed to create category';
            redirect('/admin/categories/create');
        }
    }

    /**
     * Edit category form
     */
    public function editCategory($categoryId)
    {
        $category = $this->categoryModel->find($categoryId);
        
        if (!$category) {
            $_SESSION['error'] = 'Category not found';
            redirect('/admin/categories');
        }
        
        $parentCategories = $this->categoryModel->getParentCategories($categoryId);
        
        $data = [
            'category' => $category,
            'parent_categories' => $parentCategories,
            'page_title' => 'Edit Category'
        ];
        
        $this->view('admin/categories/edit', $data);
    }

    /**
     * Update category
     */
    public function updateCategory($categoryId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/categories/' . $categoryId . '/edit');
        }
        
        $category = $this->categoryModel->find($categoryId);
        
        if (!$category) {
            $_SESSION['error'] = 'Category not found';
            redirect('/admin/categories');
        }
        
        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDescription = trim($_POST['meta_description'] ?? '');
        
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Category name is required';
        }
        
        // Check if category name already exists (excluding current category)
        $existingCategory = $this->categoryModel->findByName($name);
        if ($existingCategory && $existingCategory['id'] != $categoryId) {
            $errors[] = 'Category name already exists';
        }
        
        // Prevent setting parent as itself or its child
        if ($parentId == $categoryId) {
            $errors[] = 'Category cannot be its own parent';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            redirect('/admin/categories/' . $categoryId . '/edit');
        }
        
        // Handle image upload
        $imagePath = $category['image']; // Keep existing image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'public/assets/images/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = 'category_' . time() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Delete old image if exists
                if ($category['image'] && file_exists('public' . $category['image'])) {
                    unlink('public' . $category['image']);
                }
                $imagePath = '/assets/images/categories/' . $fileName;
            }
        }
        
        // Generate new slug if name changed
        $slug = $category['slug'];
        if ($name !== $category['name']) {
            $slug = $this->generateSlug($name, $categoryId);
        }
        
        $updateData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'parent_id' => $parentId ?: null,
            'image' => $imagePath,
            'is_active' => $isActive,
            'meta_title' => $metaTitle ?: $name,
            'meta_description' => $metaDescription,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->categoryModel->update($categoryId, $updateData)) {
            $_SESSION['success'] = 'Category updated successfully';
            redirect('/admin/categories');
        } else {
            $_SESSION['error'] = 'Failed to update category';
            redirect('/admin/categories/' . $categoryId . '/edit');
        }
    }

    /**
     * Delete category
     */
    public function deleteCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/categories');
        }
        
        $categoryId = $_POST['category_id'] ?? null;
        
        if (!$categoryId) {
            $_SESSION['error'] = 'Invalid category';
            redirect('/admin/categories');
        }
        
        $category = $this->categoryModel->find($categoryId);
        
        if (!$category) {
            $_SESSION['error'] = 'Category not found';
            redirect('/admin/categories');
        }
        
        // Check if category has products
        $productCount = $this->productModel->getCategoryProductCount($categoryId);
        if ($productCount > 0) {
            $_SESSION['error'] = 'Cannot delete category with existing products';
            redirect('/admin/categories');
        }
        
        // Check if category has subcategories
        $subcategoryCount = $this->categoryModel->getSubcategoryCount($categoryId);
        if ($subcategoryCount > 0) {
            $_SESSION['error'] = 'Cannot delete category with subcategories';
            redirect('/admin/categories');
        }
        
        // Delete category image if exists
        if ($category['image'] && file_exists('public' . $category['image'])) {
            unlink('public' . $category['image']);
        }
        
        if ($this->categoryModel->delete($categoryId)) {
            $_SESSION['success'] = 'Category deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete category';
        }
        
        redirect('/admin/categories');
    }

    /**
     * Toggle category status
     */
    public function toggleCategoryStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/categories');
        }
        
        $categoryId = $_POST['category_id'] ?? null;
        
        if (!$categoryId) {
            $_SESSION['error'] = 'Invalid category';
            redirect('/admin/categories');
        }
        
        $category = $this->categoryModel->find($categoryId);
        
        if (!$category) {
            $_SESSION['error'] = 'Category not found';
            redirect('/admin/categories');
        }
        
        $newStatus = $category['is_active'] ? 0 : 1;
        
        if ($this->categoryModel->update($categoryId, ['is_active' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')])) {
            $statusText = $newStatus ? 'activated' : 'deactivated';
            $_SESSION['success'] = "Category {$statusText} successfully";
        } else {
            $_SESSION['error'] = 'Failed to update category status';
        }
        
        redirect('/admin/categories');
    }

    /**
     * Generate unique slug
     */
    private function generateSlug($name, $excludeId = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $originalSlug = $slug;
        $counter = 1;

        while ($this->categoryModel->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Bulk category actions
     */
    public function bulkCategoryActions()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/categories');
        }
        
        $action = $_POST['bulk_action'] ?? '';
        $categoryIds = $_POST['category_ids'] ?? [];
        
        if (empty($action) || empty($categoryIds)) {
            $_SESSION['error'] = 'Please select categories and action';
            redirect('/admin/categories');
        }
        
        $successCount = 0;
        
        foreach ($categoryIds as $categoryId) {
            switch ($action) {
                case 'activate':
                    if ($this->categoryModel->update($categoryId, ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')])) {
                        $successCount++;
                    }
                    break;
                    
                case 'deactivate':
                    if ($this->categoryModel->update($categoryId, ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')])) {
                        $successCount++;
                    }
                    break;
                    
                case 'delete':
                    $category = $this->categoryModel->find($categoryId);
                    if ($category) {
                        // Check constraints before deleting
                        $productCount = $this->productModel->getCategoryProductCount($categoryId);
                        $subcategoryCount = $this->categoryModel->getSubcategoryCount($categoryId);
                        
                        if ($productCount == 0 && $subcategoryCount == 0) {
                            // Delete image if exists
                            if ($category['image'] && file_exists('public' . $category['image'])) {
                                unlink('public' . $category['image']);
                            }
                            
                            if ($this->categoryModel->delete($categoryId)) {
                                $successCount++;
                            }
                        }
                    }
                    break;
            }
        }
        
        if ($successCount > 0) {
            $_SESSION['success'] = "{$successCount} categories processed successfully";
        } else {
            $_SESSION['error'] = 'No categories were processed';
        }
        
        redirect('/admin/categories');
    }
}
