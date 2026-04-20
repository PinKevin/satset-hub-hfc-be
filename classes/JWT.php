<?php

class JWT {
    private $secret_key = 'your-secret-key-change-this-in-production';
    private $algorithm = 'HS256';
    
    public function generateToken($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => $this->algorithm]);
        $payload = json_encode($payload);
        
        $header_encoded = $this->base64UrlEncode($header);
        $payload_encoded = $this->base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $this->secret_key, true);
        $signature_encoded = $this->base64UrlEncode($signature);
        
        return "$header_encoded.$payload_encoded.$signature_encoded";
    }
    
    public function verifyToken($token) {
        if (empty($token)) {
            return false;
        }
        
        $token_parts = explode('.', $token);
        if (count($token_parts) !== 3) {
            return false;
        }
        
        $header = base64_decode($token_parts[0]);
        $payload = base64_decode($token_parts[1]);
        $signature = $token_parts[2];
        
        $header_encoded = $this->base64UrlEncode($header);
        $payload_encoded = $this->base64UrlEncode($payload);
        
        $expected_signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $this->secret_key, true);
        $expected_signature_encoded = $this->base64UrlEncode($expected_signature);
        
        if ($signature !== $expected_signature_encoded) {
            return false;
        }
        
        $payload_data = json_decode($payload, true);
        
        if (isset($payload_data['exp']) && $payload_data['exp'] < time()) {
            return false;
        }
        
        return $payload_data;
    }
    
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}