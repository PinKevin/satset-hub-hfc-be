<?php

class ProxyController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function search() {
        $this->auth->authenticate();
        $q = $_GET['q'] ?? '';

        if (empty($q)) {
            return $this->badRequest('Query parameter q is required');
        }

        $url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
            'format' => 'json',
            'q' => $q,
            'addressdetails' => 1,
            'limit' => 5
        ]);

        return $this->proxyRequest($url);
    }

    public function reverse() {
        $this->auth->authenticate();
        $lat = $_GET['lat'] ?? '';
        $lon = $_GET['lon'] ?? '';

        if (empty($lat) || empty($lon)) {
            return $this->badRequest('lat and lon parameters are required');
        }

        $url = "https://nominatim.openstreetmap.org/reverse?" . http_build_query([
            'format' => 'json',
            'lat' => $lat,
            'lon' => $lon,
            'addressdetails' => 1
        ]);

        return $this->proxyRequest($url);
    }

    private function proxyRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SatSet-App/1.0 (contact@satset.id)'); // Nominatim requires a User-Agent
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return $this->serverError('Proxy request failed: ' . $error);
        }

        $data = json_decode($response, true);
        
        // Return raw data as requested by FE
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
