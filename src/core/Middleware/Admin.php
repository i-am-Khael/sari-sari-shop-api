<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Interface\Middleware;


class Admin implements Middleware
{

  public function handle(): bool
  {

    if (empty($_SESSION) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
      http_response_code(401);
      echo json_encode([ 'status' => 401, 'errors' => [ 'msg' => 'Unauthorized.' ] ]);
      return false;
    }

    return true;

  }

}
