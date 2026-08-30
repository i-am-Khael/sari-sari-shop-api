<?php

  declare(strict_types=1);

  spl_autoload_register(function ($class) {
    require_once dirname(__DIR__) . '/src/' . lcfirst(str_replace('\\', '/', $class)) . '.php';
  });

  use Core\Router;

  $router = new Router();
  $router
    ->get('/api/categories', [Controllers\Categories::class, 'index']);

  $router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
