<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function showLogin()
    {
        // Redirect if already logged in
        if (isLoggedIn()) {
            return $this->redirect('/admin');
        }

        $meta = [
            'title' => 'Login - Moxo Mart',
            'description' => 'Login to your Moxo Mart account'
        ];

        return $this->render('auth/login', [
            'meta' => $meta
        ]);
    }

    public function login()
    {
        $this->validateCsrf();

        $errors = validate($_POST, [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6'
        ]);

        if (!empty($errors)) {
            return $this->render('auth/login', [
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        $user = $this->userModel->findByEmail($_POST['email']);

        if (!$user || !verifyPassword($_POST['password'], $user['password'])) {
            setFlash('error', 'Invalid email or password.');
            return $this->render('auth/login', [
                'old' => $_POST
            ]);
        }

        if (!$user['is_active']) {
            setFlash('error', 'Your account has been deactivated. Please contact support.');
            return $this->render('auth/login');
        }

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        // Login user
        login($user);

        // Log activity
        logActivity('user_login', "User logged in: {$user['email']}");

        setFlash('success', 'Welcome back!');
        
        // Redirect to intended destination or dashboard
        $redirectTo = $_SESSION['intended_url'] ?? '/dashboard';
        unset($_SESSION['intended_url']);
        
        return $this->redirect($redirectTo);
    }

    public function showRegister()
    {
        // Redirect if already logged in
        if (isLoggedIn()) {
            return $this->redirect('/dashboard');
        }

        $meta = [
            'title' => 'Register - Moxo Mart',
            'description' => 'Create a new Moxo Mart account'
        ];

        return $this->render('auth/register', [
            'meta' => $meta
        ]);
    }

    public function register()
    {
        $this->validateCsrf();

        $errors = validate($_POST, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required',
            'phone' => 'max:20'
        ]);

        // Check password confirmation
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            $errors['password_confirmation'][] = 'Password confirmation does not match.';
        }

        if (!empty($errors)) {
            return $this->render('auth/register', [
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        try {
            $userId = $this->userModel->createUser([
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'phone' => $_POST['phone'] ?? null,
                'role' => 'customer',
                'email_verification_token' => generateRandomString(64)
            ]);

            if ($userId) {
                $user = $this->userModel->find($userId);
                
                // Send verification email
                $this->sendVerificationEmail($user);
                
                // Log activity
                logActivity('user_registered', "New user registered: {$user['email']}");

                setFlash('success', 'Registration successful! Please check your email to verify your account.');
                return $this->redirect('/login');
            } else {
                setFlash('error', 'Registration failed. Please try again.');
                return $this->render('auth/register', ['old' => $_POST]);
            }
        } catch (\Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            setFlash('error', 'Registration failed. Please try again.');
            return $this->render('auth/register', ['old' => $_POST]);
        }
    }

    public function logout()
    {
        if (isLoggedIn()) {
            $user = getCurrentUser();
            logActivity('user_logout', "User logged out: {$user['email']}");
        }

        logout();
        setFlash('success', 'You have been logged out successfully.');
        return $this->redirect('/');
    }

    public function showForgotPassword()
    {
        $meta = [
            'title' => 'Forgot Password - Moxo Mart',
            'description' => 'Reset your Moxo Mart password'
        ];

        return $this->render('auth/forgot-password', [
            'meta' => $meta
        ]);
    }

    public function forgotPassword()
    {
        $this->validateCsrf();

        $errors = validate($_POST, [
            'email' => 'required|email|max:255'
        ]);

        if (!empty($errors)) {
            return $this->render('auth/forgot-password', [
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        $user = $this->userModel->findByEmail($_POST['email']);

        if ($user) {
            // Generate reset token
            $resetToken = generateRandomString(64);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->userModel->update($user['id'], [
                'password_reset_token' => $resetToken,
                'password_reset_expires' => $expiresAt
            ]);

            // Send reset email
            $this->sendPasswordResetEmail($user, $resetToken);
            
            logActivity('password_reset_requested', "Password reset requested for: {$user['email']}");
        }

        // Always show success message for security
        setFlash('success', 'If an account with that email exists, you will receive a password reset link.');
        return $this->redirect('/forgot-password');
    }

    public function showResetPassword($token)
    {
        if (empty($token)) {
            setFlash('error', 'Invalid reset token.');
            return $this->redirect('/forgot-password');
        }

        // Verify token
        $stmt = $this->db->prepare("SELECT * FROM users WHERE password_reset_token = ? AND password_reset_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            setFlash('error', 'Invalid or expired reset token.');
            return $this->redirect('/forgot-password');
        }

        $meta = [
            'title' => 'Reset Password - Moxo Mart',
            'description' => 'Create a new password for your account'
        ];

        return $this->render('auth/reset-password', [
            'token' => $token,
            'meta' => $meta
        ]);
    }

    public function resetPassword()
    {
        $this->validateCsrf();

        $errors = validate($_POST, [
            'token' => 'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required'
        ]);

        // Check password confirmation
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            $errors['password_confirmation'][] = 'Password confirmation does not match.';
        }

        if (!empty($errors)) {
            return $this->render('auth/reset-password', [
                'errors' => $errors,
                'token' => $_POST['token']
            ]);
        }

        // Verify token
        $stmt = $this->db->prepare("SELECT * FROM users WHERE password_reset_token = ? AND password_reset_expires > NOW()");
        $stmt->execute([$_POST['token']]);
        $user = $stmt->fetch();

        if (!$user) {
            setFlash('error', 'Invalid or expired reset token.');
            return $this->redirect('/forgot-password');
        }

        // Update password and clear reset token
        $this->userModel->update($user['id'], [
            'password' => hashPassword($_POST['password']),
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);

        logActivity('password_reset_completed', "Password reset completed for: {$user['email']}");

        setFlash('success', 'Your password has been reset successfully. Please login with your new password.');
        return $this->redirect('/login');
    }

    private function sendVerificationEmail($user)
    {
        $verificationUrl = url("/verify-email/{$user['email_verification_token']}");
        $subject = 'Verify Your Moxo Mart Account';
        $body = "
            <h2>Welcome to Moxo Mart!</h2>
            <p>Hi {$user['first_name']},</p>
            <p>Thank you for registering with Moxo Mart. Please click the link below to verify your email address:</p>
            <p><a href='{$verificationUrl}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email</a></p>
            <p>If you didn't create this account, please ignore this email.</p>
            <p>Best regards,<br>The Moxo Mart Team</p>
        ";

        sendEmail($user['email'], $subject, $body);
    }

    private function sendPasswordResetEmail($user, $token)
    {
        $resetUrl = url("/reset-password/{$token}");
        $subject = 'Reset Your Moxo Mart Password';
        $body = "
            <h2>Password Reset Request</h2>
            <p>Hi {$user['first_name']},</p>
            <p>You requested to reset your password for your Moxo Mart account. Click the link below to create a new password:</p>
            <p><a href='{$resetUrl}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
            <p>This link will expire in 1 hour. If you didn't request this reset, please ignore this email.</p>
            <p>Best regards,<br>The Moxo Mart Team</p>
        ";

        sendEmail($user['email'], $subject, $body);
    }
} 