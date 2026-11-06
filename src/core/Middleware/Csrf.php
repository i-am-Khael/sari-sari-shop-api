<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Interface\Middleware;


class Csrf implements Middleware
{


  public function handle(): bool
  {

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $storedToken = $_SESSION['csrf_hash'] ?? null;
    $expiration = $_SESSION['csrf_expire'] ?? null;

    if (
      !is_string($token) || $token === '' ||
      !is_string($storedToken) ||$storedToken === null ||
      !is_int($expiration) ||
      $expiration <= time() ||
      !hash_equals($storedToken, hash('sha256', $token))
    ) {

      http_response_code(400);

      echo json_encode([
        'status' => 400,
        'errors' => [ 'msg' => 'Invalid or expired token.' ]
      ]);

      return false;

    }

    return true;

  }

}
