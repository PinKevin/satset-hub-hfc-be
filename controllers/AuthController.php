<?php

class AuthController extends BaseController {
    private $jwt;
    
    public function __construct() {
        $this->jwt = new JWT();
    }
    
    public function register() {
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, ['name', 'email', 'password']);
        if ($validation) return $validation;
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->validationError('Invalid email format');
        }
        
        try {
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                return $this->conflict('Email already registered');
            }
            
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->save();
            
            $token = $this->jwt->generateToken([
                'user_id' => $user->id,
                'email' => $user->email,
                'exp' => time() + 86400
            ]);
            
            return $this->created([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at
                ],
                'token' => $token
            ], 'User registered successfully');
            
        } catch (Exception $e) {
            return $this->serverError('Registration failed: ' . $e->getMessage());
        }
    }
    
    public function login() {
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, ['email', 'password']);
        if ($validation) return $validation;
        
        try {
            $user = User::where('email', $data['email'])->first();
            
            if (!$user || !$user->verifyPassword($data['password'])) {
                return $this->unauthorized('Invalid email or password');
            }
            
            $token = $this->jwt->generateToken([
                'user_id' => $user->id,
                'email' => $user->email,
                'exp' => time() + 86400
            ]);
            
            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at
                ],
                'token' => $token
            ], 'Login successful');
            
        } catch (Exception $e) {
            return $this->serverError('Login failed: ' . $e->getMessage());
        }
    }
}
?>
