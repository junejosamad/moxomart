<?php

namespace App\Core;

class View
{
    private $viewPath;
    private $data = [];

    public function __construct()
    {
        $this->viewPath = APP_PATH . '/Views';
    }

    public function render($template, $data = [])
    {
        $this->data = array_merge($this->data, $data);
        
        $templatePath = $this->viewPath . '/' . str_replace('.', '/', $template) . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \Exception("View template not found: {$template}");
        }

        // Extract data to variables
        extract($this->data);
        
        // Start output buffering
        ob_start();
        
        // Include template
        include $templatePath;
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        return $content;
    }

    public function share($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function exists($template)
    {
        $templatePath = $this->viewPath . '/' . str_replace('.', '/', $template) . '.php';
        return file_exists($templatePath);
    }
}
