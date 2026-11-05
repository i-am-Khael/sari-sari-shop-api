<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Constants as MSG;
use Core\Interface\Middleware;


class Token implements Middleware
{

  public function handle(): bool
  {

    $config = parse_ini_file(MSG::FILE_INI);
    $storedToken = $config['TOKEN'] ?? '';
    $token = $_SERVER['HTTP_X_API_TOKEN'] ?? '';


  }

}
