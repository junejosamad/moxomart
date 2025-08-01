<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $currentRoute = null;
    private $currentPrefix = '';
    private $currentMiddleware = [];
    private $middlewares = [];

    public function get($path, $handler, $middleware = [])
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post($path, $handler, $middleware = [])
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put($path, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete($path, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function group($prefix, $middleware, $callback)
    {
        $oldPrefix = $this->currentPrefix ?? '';
        $oldMiddleware = $this->currentMiddleware ?? [];
        
        $this->currentPrefix = $oldPrefix . $prefix;
        $this->currentMiddleware = array_merge($oldMiddleware, $middleware);
        
        $callback($this);
        
        $this->currentPrefix = $oldPrefix;
        $this->currentMiddleware = $oldMiddleware;
    }

    private function addRoute($method, $path, $handler, $middleware = [])
    {
        $prefix = $this->currentPrefix ?? '';
        $groupMiddleware = $this->currentMiddleware ?? [];
        
        $this->routes[] = [
            'method' => $method,
            'path' => $prefix . $path,
            'handler' => $handler,
            'middleware' => array_merge($groupMiddleware, $middleware)
        ];
    }

    public function handleRequest()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove the base path if running in a subdirectory
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($basePath !== '/' && strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }
        
        // Ensure path starts with /
        if (empty($requestPath) || $requestPath[0] !== '/') {
            $requestPath = '/' . $requestPath;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $requestPath, $matches)) {
                    $this->currentRoute = $route;
                    array_shift($matches); // Remove full match
                    
                    // Run middleware
                    if (!$this->runMiddleware($route['middleware'])) {
                        return;
                    }
                    
                    return $this->callHandler($route['handler'], $matches);
                }
            }
        }

        // No route found
        http_response_code(404);
        $this->loadErrorPage('404');
    }

    private function runMiddleware($middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                switch ($middleware) {
                    case 'auth':
                        if (!isLoggedIn()) {
                            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
                            setFlash('error', 'Please login to access this page.');
                            $this->redirect('/login');
                            return false;
                        }
                        break;
                        
                    case 'admin':
                        if (!isLoggedIn()) {
                            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
                            setFlash('error', 'Please login to access this page.');
                            $this->redirect('/login');
                            return false;
                        }
                        if (!isAdmin()) {
                            setFlash('error', 'Access denied. Admin privileges required.');
                            $this->redirect('/');
                            return false;
                        }
                        break;
                        
                    case 'guest':
                        if (isLoggedIn()) {
                            $this->redirect('/dashboard');
                            return false;
                        }
                        break;
                        
                    case 'csrf':
                        if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !csrfVerify()) {
                            http_response_code(403);
                            die('CSRF token mismatch');
                        }
                        break;
                        
                    case 'api':
                        // API rate limiting would go here
                        header('Content-Type: application/json');
                        break;
                }
            } elseif (is_callable($middleware)) {
                if (!$middleware()) {
                    return false;
                }
            }
        }
        
        return true;
    }

    private function convertToRegex($path)
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function callHandler($handler, $params = [])
    {
        if (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                
                if (method_exists($controllerInstance, $method)) {
                    $result = call_user_func_array([$controllerInstance, $method], $params);
                    
                    // Output the result if it's a string (rendered view)
                    if (is_string($result)) {
                        echo $result;
                    }
                    
                    return $result;
                }
            }
        }

        throw new \Exception("Handler not found: {$handler}");
    }

    private function loadErrorPage($errorCode)
    {
        $errorPath = APP_PATH . "/Views/errors/{$errorCode}.php";
        
        if (file_exists($errorPath)) {
            include $errorPath;
        } else {
            // Fallback error display
            echo "<h1>Error {$errorCode}</h1>";
            if ($errorCode === '404') {
                echo "<p>The page you're looking for could not be found.</p>";
            } else {
                echo "<p>An unexpected error occurred.</p>";
            }
        }
    }

    private function redirect($url, $statusCode = 302)
    {
        header("Location: " . url($url), true, $statusCode);
        exit;
    }

    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }
}
