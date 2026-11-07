<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
  require_once dirname(__DIR__) . '/src/' . lcfirst(str_replace('\\', '/', $class)) . '.php';
});


use Core\Bootstrap;
use Core\Router;
use Core\Middleware\Auth;
use Core\Middleware\Bearer;
use Core\Middleware\Csrf;


$bs = new Bootstrap();
$bs->setSession();

$router = new Router();
$router
  ->get('/', [Controllers\Index::class, 'index'])

  // shop user
  ->post('/api/shop/auth/check', [Controllers\Auth::class, 'checkAuth'])
  ->get('/api/shop/auth/csrf', [Controllers\Auth::class, 'csrf'])
  ->post('/api/shop/auth/register', [Controllers\Auth::class, 'register'], [Csrf::class])
  ->post('/api/shop/auth/login', [Controllers\Auth::class, 'login'], [Csrf::class])
  ->post('/api/shop/auth/logout', [Controllers\Auth::class, 'logout'], [Csrf::class, Bearer::class])
  ->get('/api/shop/products', [Controllers\Shop\Products::class, 'all'])
  ->get('/api/shop/products/featured', [Controllers\Shop\Products::class, 'featured'])
  ->get('/api/shop/categories', [Controllers\Shop\Categories::class, 'all'])
  ->get('/api/shop/category', [Controllers\Shop\Categories::class, 'category'])
  ->get('/api/shop/categories/popular', [Controllers\Shop\Categories::class, 'popular'])
  ->get('/api/shop/profile', [Controllers\Shop\Profile::class, 'profile'], [Auth::class, Bearer::class])
  ->patch('/api/shop/profile/update', [Controllers\Shop\Profile::class, 'profile'], [Auth::class, Bearer::class, Csrf::class])



  // dash user
  ->post('/api/dash/auth/check', [Controllers\Auth::class, 'checkAuth'])
  ->get('/api/dash/auth/csrf', [Controllers\Auth::class, 'csrf'])
  ->post('/api/dash/auth/login', [Controllers\Auth::class, 'login'], [Csrf::class])
  ->post('/api/dash/auth/logout', [Controllers\Auth::class, 'logout'], [Csrf::class])
  ->get('/api/dash/totals', [Controllers\Dash\Dashboard::class, 'totals'], [Auth::class, Bearer::class])
  ->post('/api/dash/sales', [Controllers\Dash\Dashboard::class, 'sales'], [Auth::class, Bearer::class])
  ->get('/api/dash/orders', [Controllers\Dash\Dashboard::class, 'orders'], [Auth::class, Bearer::class])
  ->get('/api/dash/categories/popular', [Controllers\Dash\Dashboard::class, 'popular'], [Auth::class, Bearer::class])
  ->post('/api/dash/categories/add', [Controllers\Dash\Categories::class, 'insert'], [Auth::class, Bearer::class, Csrf::class])
  ->patch('/api/dash/categories/update', [Controllers\Dash\Categories::class, 'update'], [Auth::class, Bearer::class, Csrf::class])
  ->get('/api/dash/categories/all', [Controllers\Dash\Categories::class, 'all'], [Auth::class, Bearer::class])
  ->post('/api/dash/products/add', [Controllers\Dash\Products::class, 'insert'], [Auth::class, Bearer::class, Csrf::class])
  ->post('/api/dash/products/update', [Controllers\Dash\Products::class, 'update'], [Auth::class, Bearer::class, Csrf::class])
  ->get('/api/dash/products/all', [Controllers\Dash\Products::class, 'all'], [Auth::class, Bearer::class])
  ->get('/api/dash/orders/all', [Controllers\Dash\Orders::class, 'all'], [Auth::class, Bearer::class]);


$bs->allowedOrigin();
$router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
