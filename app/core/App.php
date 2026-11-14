<?php
/**
 * Core App / Router Class
 * Project: Teknisi Rumah Tangga
 * 
 * URL Format: /controller/method/param1/param2
 * Custom Route Aliases: /login, /register, /logout, /admin, /customer, /technician, /profile, /categories
 */

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    /**
     * Named route aliases for clean URLs
     */
    protected $routes = [
        'login' => ['controller' => 'AuthController', 'method' => 'login'],
        'register' => ['controller' => 'AuthController', 'method' => 'register'],
        'verify-otp' => ['controller' => 'AuthController', 'method' => 'verifyOtp'],
        'resend-otp' => ['controller' => 'AuthController', 'method' => 'resendOtp'],
        'cancel-register' => ['controller' => 'AuthController', 'method' => 'cancelRegister'],
        'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
        'admin' => ['controller' => 'AdminController', 'method' => 'index'],
        'customer' => ['controller' => 'CustomerController', 'method' => 'index'],
        'technician' => ['controller' => 'TechnicianController', 'method' => 'index'],
        'profile' => ['controller' => 'ProfileController', 'method' => 'index'],
        'categories' => ['controller' => 'CategoryController', 'method' => 'index'],
        'posts' => ['controller' => 'PostController', 'method' => 'index'],
        'chat' => ['controller' => 'ChatController', 'method' => 'index'],
        'chatbot' => ['controller' => 'ChatbotController', 'method' => 'message'],
        'notification' => ['controller' => 'NotificationController', 'method' => 'list'],
        'history' => ['controller' => 'HistoryController', 'method' => 'index'],
        'home' => ['controller' => 'HomeController', 'method' => 'index'],
    ];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Check for Route Aliases (e.g. /login, /register, /admin, /profile, /categories)
        if (!empty($url[0]) && isset($this->routes[strtolower($url[0])])) {
            $route = $this->routes[strtolower($url[0])];
            $this->controller = $route['controller'];
            $this->method = $route['method'];
            unset($url[0]);

            // If a secondary sub-action was passed, e.g. /categories/create or /categories/edit
            if (isset($url[1])) {
                $customMethod = $url[1];
                $camelMethod = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $url[1]))));
                $controllerFile = '../app/controllers/' . $this->controller . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    if (method_exists($this->controller, $customMethod)) {
                        $this->method = $customMethod;
                        unset($url[1]);
                    } elseif (method_exists($this->controller, $camelMethod)) {
                        $this->method = $camelMethod;
                        unset($url[1]);
                    }
                }
            }
        } 
        // 2. Standard MVC Controller Detection (e.g. /category/index, /home/index)
        elseif (!empty($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = '../app/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            } else {
                $this->controller = 'NotFoundController';
            }
        }

        // Require Controller file
        $controllerPath = '../app/controllers/' . $this->controller . '.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
        } else {
            require_once '../app/controllers/NotFoundController.php';
            $this->controller = 'NotFoundController';
        }

        $this->controller = new $this->controller;

        // 3. Check Method if not already set by route alias
        if (isset($url[1])) {
            $customMethod = $url[1];
            $camelMethod = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $url[1]))));
            if (method_exists($this->controller, $customMethod)) {
                $this->method = $customMethod;
                unset($url[1]);
            } elseif (method_exists($this->controller, $camelMethod)) {
                $this->method = $camelMethod;
                unset($url[1]);
            } else {
                if (get_class($this->controller) !== 'NotFoundController') {
                    require_once '../app/controllers/NotFoundController.php';
                    $this->controller = new NotFoundController();
                    $this->method = 'index';
                }
            }
        }

        // 4. Set Remaining Parameters
        $this->params = $url ? array_values($url) : [];

        // 5. Execute Action
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse and sanitize current URL from GET parameter
     *
     * @return array
     */
    public function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
