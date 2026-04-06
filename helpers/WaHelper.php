<?php

class WaHelper {
    private static $apiUrl = 'http://depowawa.com/api/v1/send-message';

    /**
     * Send WhatsApp message
     * @param string $destination Phone number (format: 08123456789)
     * @param string $message Message content
     * @param string $apiKey WhatsApp API key
     * @return array Response array with status and data
     */
    public static function sendMessage($destination, $message, $apiKey) {
        try {
            if (empty($destination) || empty($message) || empty($apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Destination, message, and API key are required',
                    'data' => null
                ];
            }

            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => self::$apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode(array(
                    "api_key" => $apiKey,
                    "destination" => $destination,
                    "message" => $message
                )),
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            
            curl_close($curl);

            if ($error) {
                return [
                    'success' => false,
                    'message' => 'Failed to send message: ' . $error,
                    'data' => null
                ];
            }

            $decodedResponse = json_decode($response, true);

            return [
                'success' => $httpCode >= 200 && $httpCode < 300,
                'message' => $decodedResponse['message'] ?? 'Message sent',
                'data' => $decodedResponse,
                'http_code' => $httpCode
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

}