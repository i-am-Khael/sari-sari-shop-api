<?php

  declare(strict_types=1);

  spl_autoload_register(function ($class) {
    require_once dirname(__DIR__) . '/src/' . lcfirst(str_replace('\\', '/', $class)) . '.php';
  });

  use Core\Router;

  $router = new Router();
  $router
    ->post('/api/auth/register', [Controllers\Auth::class, 'register'])
    ->post('/api/auth/login', [Controllers\Auth::class, 'login'])

    ->get('/api/dashboard/totals', [Controllers\Dashboard::class, 'totals'])
    ->post('/api/dashboard/sales', [Controllers\Dashboard::class, 'sales'])
    ->get('/api/dashboard/popular-categories', [Controllers\Dashboard::class, 'popularCategories'])

    // Categories
    ->get('/api/categories/all', [Controllers\Categories::class, 'getCategories'])
    ->post('/api/categories/add', [Controllers\Categories::class, 'add'])
    ->patch('/api/categories/update', [Controllers\Categories::class, 'update'])
    ->delete('/api/categories/delete', [Controllers\Categories::class, 'delete'])

    // Products
    ->get('/api/products/all', [Controllers\Products::class, 'getAll'])
    ->get('/api/products/featured', [Controllers\Products::class, 'featured'])
    ->post('/api/products/add', [Controllers\Products::class, 'add'])
    ->post('/api/products/update', [Controllers\Products::class, 'update'])
    ->delete('/api/products/delete', [Controllers\Products::class, 'delete'])

    // Orders
    ->get('/api/orders/all', [Controllers\Orders::class, 'getAll'])

    // Checkout
    ->post('/api/checkout/create-order', [Controllers\Checkout::class, 'createOrder'])
    ->post('/api/checkout/take-order', [Controllers\Checkout::class, 'captureOrder']);

  $router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
