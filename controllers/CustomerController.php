<?php

class CustomerController extends BaseController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthMiddleware();
    }
    
    public function index() {
        $payload = $this->auth->authenticate();
        
        try {
            $customers = Customer::all();
            
            return $this->success($customers);
            
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch customers: ' . $e->getMessage());
        }
    }
    
    public function show($id) {
        $payload = $this->auth->authenticate();
        
        try {
            $customer = Customer::find($id);
            
            if (!$customer) {
                return $this->notFound('Customer not found');
            }
            
            return $this->success($customer);
            
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch customer: ' . $e->getMessage());
        }
    }
}