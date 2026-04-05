<?php

class ResponseHelper {
    public static function success($data = null, $message = 'Success', $statusCode = 200) {
        http_response_code($statusCode);
        
        $response = [
            'status' => 'success',
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return $response;
    }
    
    public static function error($message = 'Error', $statusCode = 400, $errors = null) {
        http_response_code($statusCode);
        
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        return $response;
    }
    
    public static function created($data = null, $message = 'Created successfully') {
        return self::success($data, $message, 201);
    }
    
    public static function notFound($message = 'Resource not found') {
        return self::error($message, 404);
    }
    
    public static function unauthorized($message = 'Unauthorized') {
        return self::error($message, 401);
    }
    
    public static function forbidden($message = 'Forbidden') {
        return self::error($message, 403);
    }
    
    public static function validationError($message = 'Validation failed', $errors = null) {
        return self::error($message, 422, $errors);
    }
    
    public static function serverError($message = 'Internal server error') {
        return self::error($message, 500);
    }
    
    public static function conflict($message = 'Conflict') {
        return self::error($message, 409);
    }
}
?>
