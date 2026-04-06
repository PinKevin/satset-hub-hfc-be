<?php

class BaseController {
    protected function success($data = null, $message = 'Success', $statusCode = 200) {
        return ResponseHelper::success($data, $message, $statusCode);
    }
    
    protected function error($message = 'Error', $statusCode = 400, $errors = null) {
        return ResponseHelper::error($message, $statusCode, $errors);
    }
    
    protected function created($data = null, $message = 'Created successfully') {
        return ResponseHelper::created($data, $message);
    }
    
    protected function notFound($message = 'Resource not found') {
        return ResponseHelper::notFound($message);
    }
    
    protected function unauthorized($message = 'Unauthorized') {
        return ResponseHelper::unauthorized($message);
    }
    
    protected function forbidden($message = 'Forbidden') {
        return ResponseHelper::forbidden($message);
    }
    
    protected function validationError($message = 'Validation failed', $errors = null) {
        return ResponseHelper::validationError($message, $errors);
    }
    
    protected function serverError($message = 'Internal server error') {
        return ResponseHelper::serverError($message);
    }
    
    protected function conflict($message = 'Conflict') {
        return ResponseHelper::conflict($message);
    }
    
    protected function getRequestData() {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
    
    protected function validateRequired($data, $fields) {
        $missing = [];
        
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            return $this->validationError('Required fields missing: ' . implode(', ', $missing));
        }
        
        return null;
    }

    protected function parseJsonField($value) {
        if (is_array($value)) {
            $json = json_encode($value);
            return $json === false ? false : $json;
        }

        if (!is_string($value)) {
            return false;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $value;
        }

        return false;
    }
}
?>
