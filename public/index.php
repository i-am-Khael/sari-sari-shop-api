<?php

  declare(strict_types=1);

  spl_autoload_register(function ($class) {
    require_once dirname(__DIR__) . '/src/' . lcfirst(str_replace('\\', '/', $class)) . '.php';
  });

  require_once dirname(__DIR__) . '/src/core/constant.php';

  use Core\Router;

  $router = new Router();
  $router
    ->post('/api/auth/register', [Controllers\Auth::class, 'register'])
    ->post('/api/auth/login', [Controllers\Auth::class, 'login'])

    // Categories
    ->post('/api/categories/add', [Controllers\Categories::class, 'add'])
    ->get('/api/categories/all', [Controllers\Categories::class, 'getCategories'])
    ->patch('/api/categories/update', [Controllers\Categories::class, 'update'])
    ->delete('/api/categories/delete', [Controllers\Categories::class, 'delete'])

    // Products
    ->post('/api/products/add', [Controllers\Products::class, 'add']);

  $router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
