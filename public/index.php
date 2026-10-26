<?php

  declare(strict_types=1);

  spl_autoload_register(function ($class) {
    require_once dirname(__DIR__) . '/src/' . lcfirst(str_replace('\\', '/', $class)) . '.php';
  });


  use Core\Router;

  $router = new Router();
  $router
    ->get('/', [Controllers\Index::class, 'index'])

    // shop user
    ->post('/api/shop/auth/register', [Controllers\Auth::class, 'register'])
    ->post('/api/shop/auth/login', [Controllers\Auth::class, 'login'])

    // dash user
    ->post('/api/dash/auth/login', [Controllers\Auth::class, 'login']);

  $router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
