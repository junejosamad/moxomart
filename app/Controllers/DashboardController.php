<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Order;

class DashboardController extends Controller
{
    private $userModel;
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->userModel = new User();
        $this->orderModel = new Order();
    }

    public function index()
    {
        $user = getCurrentUser();
        
        // Get recent orders
        $recentOrders = $this->userModel->getOrders($user['id'], 5);
        
        // Get user statistics
        $stats = [
            'total_orders' => $this->orderModel->count(['user_id' => $user['id']]),
            'pending_orders' => $this->orderModel->count(['user_id' => $user['id'], 'status' => 'pending']),
            'total_spent' => $this->orderModel->getTotalSpent($user['id']),
            'wishlist_count' => $this->getWishlistCount($user['id'])
        ];

        $meta = [
            'title' => 'My Dashboard - Moxo Mart',
            'description' => 'Manage your account, orders, and preferences'
        ];

        return $this->render('dashboard/index', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'stats' => $stats,
            'meta' => $meta
        ]);
    }

    public function orders()
    {
        $user = getCurrentUser();
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        
        $conditions = ['user_id' => $user['id']];
        $orders = $this->orderModel->paginate($page, $perPage, $conditions, 'created_at DESC');

        $meta = [
            'title' => 'My Orders - Moxo Mart',
            'description' => 'View and track your orders'
        ];

        return $this->render('dashboard/orders', [
            'orders' => $orders,
            'meta' => $meta
        ]);
    }

    public function orderDetail($orderId)
    {
        $user = getCurrentUser();
        $order = $this->orderModel->getOrderWithItems($orderId);

        if (!$order || $order['user_id'] != $user['id']) {
            setFlash('error', 'Order not found.');
            return $this->redirect('/dashboard/orders');
        }

        $meta = [
            'title' => "Order #{$order['order_number']} - Moxo Mart",
            'description' => 'View order details and tracking information'
        ];

        return $this->render('dashboard/order-detail', [
            'order' => $order,
            'meta' => $meta
        ]);
    }

    public function profile()
    {
        $user = getCurrentUser();

        $meta = [
            'title' => 'My Profile - Moxo Mart',
            'description' => 'Update your personal information'
        ];

        return $this->render('dashboard/profile', [
            'user' => $user,
            'meta' => $meta
        ]);
    }

    public function updateProfile()
    {
        $user = getCurrentUser();

        $errors = validate($_POST, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'max:20'
        ]);

        // Check if email is already taken by another user
        if ($_POST['email'] !== $user['email']) {
            $existingUser = $this->userModel->findByEmail($_POST['email']);
            if ($existingUser && $existingUser['id'] != $user['id']) {
                $errors['email'][] = 'Email is already taken by another user.';
            }
        }

        // Password validation if provided
        if (!empty($_POST['current_password']) || !empty($_POST['new_password'])) {
            if (empty($_POST['current_password'])) {
                $errors['current_password'][] = 'Current password is required.';
            } elseif (!verifyPassword($_POST['current_password'], $user['password'])) {
                $errors['current_password'][] = 'Current password is incorrect.';
            }

            if (empty($_POST['new_password'])) {
                $errors['new_password'][] = 'New password is required.';
            } elseif (strlen($_POST['new_password']) < 8) {
                $errors['new_password'][] = 'New password must be at least 8 characters.';
            }

            if ($_POST['new_password'] !== $_POST['new_password_confirmation']) {
                $errors['new_password_confirmation'][] = 'Password confirmation does not match.';
            }
        }

        if (!empty($errors)) {
            return $this->render('dashboard/profile', [
                'errors' => $errors,
                'old' => $_POST,
                'user' => $user
            ]);
        }

        try {
            // Update profile data
            $updateData = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'] ?? null
            ];

            // Update password if provided
            if (!empty($_POST['new_password'])) {
                $updateData['password'] = hashPassword($_POST['new_password']);
            }

            $this->userModel->update($user['id'], $updateData);

            // Update session data
            $_SESSION['user_data'] = $this->userModel->find($user['id']);

            logActivity('profile_updated', "Profile updated for user: {$user['email']}");

            setFlash('success', 'Profile updated successfully.');
            return $this->redirect('/dashboard/profile');

        } catch (\Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            setFlash('error', 'Failed to update profile. Please try again.');
            return $this->render('dashboard/profile', [
                'old' => $_POST,
                'user' => $user
            ]);
        }
    }

    public function addresses()
    {
        $user = getCurrentUser();
        $addresses = $this->getUserAddresses($user['id']);

        $meta = [
            'title' => 'My Addresses - Moxo Mart',
            'description' => 'Manage your shipping and billing addresses'
        ];

        return $this->render('dashboard/addresses', [
            'addresses' => $addresses,
            'meta' => $meta
        ]);
    }

    public function saveAddress()
    {
        $user = getCurrentUser();

        $errors = validate($_POST, [
            'type' => 'required',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'address_line_1' => 'required|max:255',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'postal_code' => 'required|max:20',
            'country' => 'required|max:100'
        ]);

        if (!in_array($_POST['type'], ['billing', 'shipping'])) {
            $errors['type'][] = 'Invalid address type.';
        }

        if (!empty($errors)) {
            return $this->render('dashboard/addresses', [
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        try {
            $addressData = [
                'user_id' => $user['id'],
                'type' => $_POST['type'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'company' => $_POST['company'] ?? null,
                'address_line_1' => $_POST['address_line_1'],
                'address_line_2' => $_POST['address_line_2'] ?? null,
                'city' => $_POST['city'],
                'state' => $_POST['state'],
                'postal_code' => $_POST['postal_code'],
                'country' => $_POST['country'],
                'phone' => $_POST['phone'] ?? null,
                'is_default' => isset($_POST['is_default']) ? 1 : 0
            ];

            $addressId = $_POST['address_id'] ?? null;

            if ($addressId) {
                // Update existing address
                $this->updateUserAddress($addressId, $addressData);
                $message = 'Address updated successfully.';
            } else {
                // Create new address
                $this->createUserAddress($addressData);
                $message = 'Address added successfully.';
            }

            logActivity('address_saved', "Address saved for user: {$user['email']}");

            setFlash('success', $message);
            return $this->redirect('/dashboard/addresses');

        } catch (\Exception $e) {
            error_log("Address save error: " . $e->getMessage());
            setFlash('error', 'Failed to save address. Please try again.');
            return $this->render('dashboard/addresses', [
                'old' => $_POST
            ]);
        }
    }

    private function getWishlistCount($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    private function getUserAddresses($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    private function createUserAddress($data)
    {
        // If this is set as default, unset other defaults for this user and type
        if ($data['is_default']) {
            $stmt = $this->db->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ? AND type = ?");
            $stmt->execute([$data['user_id'], $data['type']]);
        }

        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO user_addresses ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function updateUserAddress($addressId, $data)
    {
        $user = getCurrentUser();
        
        // Verify address belongs to user
        $stmt = $this->db->prepare("SELECT user_id FROM user_addresses WHERE id = ?");
        $stmt->execute([$addressId]);
        $address = $stmt->fetch();
        
        if (!$address || $address['user_id'] != $user['id']) {
            throw new \Exception('Address not found or access denied');
        }

        // If this is set as default, unset other defaults for this user and type
        if ($data['is_default']) {
            $stmt = $this->db->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ? AND type = ? AND id != ?");
            $stmt->execute([$data['user_id'], $data['type'], $addressId]);
        }

        unset($data['user_id']); // Don't update user_id

        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE user_addresses SET {$setClause} WHERE id = :id";
        $data['id'] = $addressId;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
} 