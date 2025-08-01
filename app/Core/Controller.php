<?php

namespace App\Core;

class Controller
{
    protected $db;
    protected $view;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->view = new View();
    }

    protected function render($template, $data = [])
    {
        return $this->view->render($template, $data);
    }

    protected function redirect($url, $statusCode = 302)
    {
        header("Location: " . url($url), true, $statusCode);
        exit;
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth()
    {
        if (!isLoggedIn()) {
            setFlash('error', 'Please login to access this page.');
            $this->redirect('login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireAuth();
        
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('/');
        }
    }

    protected function validateCsrf()
    {
        if (!csrfVerify()) {
            http_response_code(403);
            die('CSRF token mismatch');
        }
    }
}
