<?php

class Route {
    private static $routes = [];
    private static $currentMethod = '';
    private static $currentPath = '';
    
    public static function get($path, $handler) {
        self::addRoute('GET', $path, $handler);
    }
    
    public static function post($path, $handler) {
        self::addRoute('POST', $path, $handler);
    }
    
    public static function put($path, $handler) {
        self::addRoute('PUT', $path, $handler);
    }
    
    public static function patch($path, $handler) {
        self::addRoute('PATCH', $path, $handler);
    }
    
    public static function delete($path, $handler) {
        self::addRoute('DELETE', $path, $handler);
    }
    
    private static function addRoute($method, $path, $handler) {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public static function dispatch($requestMethod, $requestPath) {
        error_log("Request: $requestMethod $requestPath");
        error_log("Registered routes: " . json_encode(self::$routes));
        
        foreach (self::$routes as $route) {
            error_log("Checking route: {$route['method']} {$route['path']}");
            if ($route['method'] === $requestMethod && self::matchPath($route['path'], $requestPath, $params)) {
                error_log("Route matched! Calling handler");
                $result = self::callHandler($route['handler'], $params);
                
                if (!http_response_code()) {
                    http_response_code(200);
                }
                
                return $result;
            }
        }
        
        error_log("No route found for: $requestMethod $requestPath");
        http_response_code(404);
        return [
            'status' => 'error',
            'message' => 'Endpoint not found'
        ];
    }
    
    private static function matchPath($routePath, $requestPath, &$params) {
        $requestPath = '/' . ltrim($requestPath, '/');
        
        $routePath = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $routePath . '$#';
        
        error_log("Matching pattern: $pattern against path: $requestPath");
        
        if (preg_match($pattern, $requestPath, $matches)) {
            array_shift($matches);
            $params = $matches;
            return true;
        }
        
        return false;
    }
    
    private static function callHandler($handler, $params) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_string($handler)) {
            list($controllerName, $methodName) = explode('@', $handler);
            
            $controllerClass = $controllerName;
            if (!class_exists($controllerClass)) {
                $controllerClass = $controllerName . 'Controller';
                if (!class_exists($controllerClass)) {
                    throw new Exception("Controller class not found: $controllerName or $controllerClass");
                }
            }
            
            $controller = new $controllerClass();
            if (method_exists($controller, $methodName)) {
                return call_user_func_array([$controller, $methodName], $params);
            } else {
                throw new Exception("Method $methodName not found in controller $controllerClass");
            }
        }
        
        throw new Exception("Handler not found: $handler");
    }
    
    public static function loadRoutes($file) {
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
?>
