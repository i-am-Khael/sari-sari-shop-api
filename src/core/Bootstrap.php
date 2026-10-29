<?php

declare(strict_types=1);

namespace Core;

class Bootstrap
{


  public function setSessionName(): void {

    $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

    if (str_starts_with($route, '/api/dash/')) {

      session_name('DASHSESSID');

    } elseif (str_starts_with($route, '/api/shop/')) {

      session_name('SHOPSESSID');

    } else {

      return;

    }

    session_start();

  }

}
