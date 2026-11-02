<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
  require_once dirname(__DIR__) . '/src/' . lcfirst(str_replace('\\', '/', $class)) . '.php';
});


use Core\Bootstrap;
use Core\Router;


$bs = new Bootstrap();
$bs->setSessionName();

$router = new Router();
$router
  ->get('/', [Controllers\Index::class, 'index'])

  // shop user
  ->post('/api/shop/auth/check', [Controllers\Auth::class, 'checkAuth'])
  ->post('/api/shop/auth/register', [Controllers\Auth::class, 'register'])
  ->post('/api/shop/auth/login', [Controllers\Auth::class, 'login'])
  ->post('/api/shop/auth/logout', [Controllers\Auth::class, 'logout'])
  ->post('/api/shop/auth/csrf', [Controllers\Auth::class, 'setCSRF'])
  ->get('/api/shop/products/all', [Controllers\Shop\Products::class, 'all'])


  // dash user
  ->post('/api/dash/auth/check', [Controllers\Auth::class, 'checkAuth'])
  ->post('/api/dash/auth/login', [Controllers\Auth::class, 'login'])
  ->post('/api/dash/auth/logout', [Controllers\Auth::class, 'logout'])
  ->post('/api/dash/auth/csrf', [Controllers\Auth::class, 'setCSRF'])
  ->get('/api/dash/totals', [Controllers\Dash\Dashboard::class, 'totals'])
  ->post('/api/dash/sales', [Controllers\Dash\Dashboard::class, 'sales'])
  ->get('/api/dash/orders', [Controllers\Dash\Dashboard::class, 'orders'])
  ->get('/api/dash/categories', [Controllers\Dash\Dashboard::class, 'categories'])
  ->post('/api/dash/categories/add', [Controllers\Dash\Categories::class, 'insert'])
  ->patch('/api/dash/categories/update', [Controllers\Dash\Categories::class, 'update'])
  ->get('/api/dash/categories/all', [Controllers\Dash\Categories::class, 'all'])
  ->post('/api/dash/products/add', [Controllers\Dash\Products::class, 'insert'])
  ->post('/api/dash/products/update', [Controllers\Dash\Products::class, 'update'])
  ->get('/api/dash/products/all', [Controllers\Dash\Products::class, 'all'])
  ->get('/api/dash/orders/all', [Controllers\Dash\Orders::class, 'all']);


$bs->allowedOrigin();
$router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
