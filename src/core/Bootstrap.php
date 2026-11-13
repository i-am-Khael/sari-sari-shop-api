<?php

declare(strict_types=1);

namespace Core;

use Core\Constants as MSG;

class Bootstrap
{


  public function setSession(): void {

    $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

    session_set_cookie_params([
      'lifetime' => 86400, // 24 hours
      'path' => '/',
      'domain' => '',
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);

    if (str_starts_with($route, '/api/dash/')) {

      $name = 'SESSID_c7e6fa1771';
      $path = '/api/dash/';

    } elseif (str_starts_with($route, '/api/shop/')) {

      $name = 'SESSID_495d181273';
      $path = '/api/shop/';

    } else {

      return;

    }

    session_name($name);

    session_set_cookie_params([
      'lifetime' => 86400, // 24 hours
      'path' => $path,
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);

    session_start();

  }


  public function allowedOrigin(): void
  {

    $config = parse_ini_file(MSG::FILE_INI);
    $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

    $allowed = match (true) {
      str_starts_with($route, '/api/dash/') => [ $config['DASH_ORIGIN'] ],
      str_starts_with($route, '/api/shop/') => [ $config['SHOP_ORIGIN'] ],
      default => []
    };

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = $origin !== '' && in_array($origin, $allowed, true);

    if ($origin !== '' && !$allowed) {
      http_response_code(403);
      exit('Forbidden');
    }

    if ($allowed) {
      header("Access-Control-Allow-Origin: $origin");
      header("Access-Control-Allow-Credentials: true");
      header("Vary: Origin");
      header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
      header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-API-Token, Authorization');
    }

    if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
      http_response_code(204);
      exit;
    }

  }


}
