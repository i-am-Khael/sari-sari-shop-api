<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Interface\Middleware;


class Guest implements Middleware
{

  public function handle(): bool
  {

      if (!empty($_SESSION['id'])) {

        http_response_code(400);

        echo json_encode([
          'status' => 400,
          'errors' => [ 'msg' => 'Already authenticated.' ]
        ]);

        return false;

      }

      return true;

  }

}
