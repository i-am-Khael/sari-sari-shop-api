<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Interface\Middleware;


class Auth implements Middleware
{

  public function handle(): bool
  {

      if (empty($_SESSION['id'])) {

        http_response_code(401);

        echo json_encode([ 'status' => 401, 'msg' => 'Unauthorized' ]);

        return false;

      }

      return true;

  }

}
