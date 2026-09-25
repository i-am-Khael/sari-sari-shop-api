<?php

  declare(strict_types=1);

  namespace Controllers;

  class Paypal {

    public static function paypalAccessToken(): string {

      $uri = 'https://api-m.sandbox.paypal.com';
      $client = '';
      $key = '';

      $channel = curl_init($uri . '/v1/oauth2/token');

      curl_setopt_array($channel, [
        CURLOPT_PORT => true,
        CURLOPT_USERPWD => $client . ':' . $key,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
          'Accept: application/json',
          'Accept-Language: en_US',
          'Content-Type: application/x-www-form-urlencoded',
        ],
      ]);


      $response = curl_exec($channel);
      $status = curl_getinfo($channel, CURLINFO_HTTP_CODE);

      if ( $response === false ) throw new \Exception(curl_error($channel));

      curl_close($channel);

      $data = json_decode($response, true);

      if ( $status < 200 || $status >= 300 || empty($data['access_token'])) throw new \Exception('Could not obtain PayPal access token.');

      return $data['access_token'];

    }


    public static function paypalRequest( string $method, string $endpoint, ?array $body = null): array {

      $uri = 'https://api-m.sandbox.paypal.com';

      $token = self::paypalAccessToken();

      $channel = curl_init($uri, $endpoint);

      $headers = [
        'Content-type: application/json',
        'Authorization: Bearer ' . $token,
      ];

      curl_setopt_array($channel, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
      ]);

      if ( $body !== null ) {
        curl_setopt(
          $channel,
          CURLOPT_POSTFIELDS,
          json_encode($body, JSON_THROW_ON_ERROR)
        );
      }

      $response = curl_exec($channel);
      $status = curl_getinfo($channel, CURLINFO_HTTP_CODE);

      if ( $response === false ) throw new \Exception(curl_error($channel));

      curl_close($channel);

      $data = json_decode($response, true);

      if ( $status < 200 || $status >= 300 ) throw new \Exception($data['message'] ?? 'PayPal API request failed.');

      return $data;

    }



  }
