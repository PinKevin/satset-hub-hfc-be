<?php

class AuthMiddleware {
    private $jwt;
    private $user;
    
    public function __construct() {
        $this->jwt = new JWT();
    }
    
    public function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (!$authHeader) {
            $this->sendUnauthorizedResponse('Authorization header required');
            exit;
        }
        
        if (!preg_match('/Bearer\s+(.*)$/', $authHeader, $matches)) {
            $this->sendUnauthorizedResponse('Invalid authorization format');
            exit;
        }
        
        $token = $matches[1];
        $payload = $this->jwt->verifyToken($token);
        
        if (!$payload) {
            $this->sendUnauthorizedResponse('Invalid or expired token');
            exit;
        }
        
        $this->user = $payload;
        return $payload;
    }
    
    public function user() {
        return $this->user;
    }
    
    private function sendUnauthorizedResponse($message) {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }
}
?>
