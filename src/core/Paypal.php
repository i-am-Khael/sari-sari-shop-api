<?php

  declare(strict_types=1);

  namespace Core;

  class Paypal {

    public static function paypalAccessToken(): string {

      $uri = 'https://api-m.sandbox.paypal.com';
      $client = '';
      $key = '';

      $channel = curl_init($uri . '/v1/oauth2/token');

      curl_setopt_array($channel, [
        CURLOPT_USERPWD => $client . ':' . $key,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
          'Accept: application/json',
          'Accept-Language: en_US',
          'Content-Type: application/x-www-form-urlencoded',
        ],
        CURLOPT_TIMEOUT => 30
      ]);

      $response = curl_exec($channel);
      $status = curl_getinfo($channel, CURLINFO_HTTP_CODE);


      if ( $response === false ) throw new \Exception(curl_error($channel));

      curl_close($channel);

      $data = json_decode($response, true);

      if ( $status < 200 || $status >= 300 || !is_array($data) || empty($data['access_token']) ) {
        throw new \Exception('Could not obtain PayPal access token.');
      }

      return $data['access_token'];

    }


    public static function paypalRequest( string $method, string $endpoint, ?array $body = null): array {

      $uri = 'https://api-m.sandbox.paypal.com';
      $url = $uri . '/' . ltrim($endpoint, '/');

      $token = self::paypalAccessToken();

      $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
      ];

      $channel = curl_init($url);

      $options = [
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
      ];


      if ( $body !== null ) {
        $jsonBody = empty($body) ? '{}' : json_encode($body, JSON_THROW_ON_ERROR);
        $options[CURLOPT_POSTFIELDS] = $jsonBody;
      }

      curl_setopt_array($channel, $options);

      $response = curl_exec($channel);
      $status = curl_getinfo($channel, CURLINFO_HTTP_CODE);

      if ( $response === false ) throw new \Exception(curl_error($channel));

      curl_close($channel);

      $data = json_decode($response, true);

      if ( !is_array($data) ) throw new \Exception('Invalid PayPal response');

      if ( $status < 200 || $status >= 300 ) throw new \Exception($data['message'] ?? 'PayPal API request failed.');

      return $data;

    }


    public static function createOrder(array $payload): array {
      return self::paypalRequest(
        'POST',
        '/v2/checkout/orders',
        $payload
      );
    }


    public static function captureOrder(string $orderId): array {
      return self::paypalRequest(
        'POST',
        '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture',
        null
      );
    }


    public static function getOrder(string $orderId): array {
      return self::paypalRequest('GET', '/v2/checkout/orders/' . rawurlencode($orderId));
    }


  }
