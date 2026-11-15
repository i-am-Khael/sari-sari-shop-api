<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
  require_once dirname(__DIR__) . '/src/' . lcfirst(str_replace('\\', '/', $class)) . '.php';
});


use Core\Bootstrap;
use Core\Router;
use Core\Middleware\Admin;
use Core\Middleware\Auth;
use Core\Middleware\Bearer;
use Core\Middleware\Csrf;


$bs = new Bootstrap();
$bs->allowedOrigin();
$bs->setSession();

$router = new Router();
$router
  ->get('/', [Controllers\Index::class, 'index'])

  // shop user
  ->post('/api/shop/auth/check', [Controllers\Auth::class, 'checkAuth'])
  ->get('/api/shop/auth/csrf', [Controllers\Auth::class, 'csrf'])
  ->post('/api/shop/auth/register', [Controllers\Auth::class, 'register'], [Csrf::class])
  ->post('/api/shop/auth/login', [Controllers\Auth::class, 'login'], [Csrf::class])
  ->post('/api/shop/auth/logout', [Controllers\Auth::class, 'logout'], [Bearer::class, Csrf::class])
  ->get('/api/shop/products', [Controllers\Shop\Products::class, 'all'])
  ->get('/api/shop/products/featured', [Controllers\Shop\Products::class, 'featured'])
  ->get('/api/shop/categories', [Controllers\Shop\Categories::class, 'all'])
  ->get('/api/shop/category', [Controllers\Shop\Categories::class, 'category'])
  ->get('/api/shop/categories/popular', [Controllers\Shop\Categories::class, 'popular'])
  ->post('/api/shop/user/profile', [Controllers\Shop\Profile::class, 'profile'], [Auth::class, Bearer::class, Csrf::class])
  ->patch('/api/shop/user/profile/save', [Controllers\Shop\Profile::class, 'update'], [Auth::class, Bearer::class, Csrf::class])
  ->post('/api/shop/user/checkout/create-order', [Controllers\Shop\Checkout::class, 'create'])
  ->post('/api/shop/user/checkout/take-order', [Controllers\Shop\Checkout::class, 'take'])


  // dash user
  ->post('/api/dash/auth/check', [Controllers\Auth::class, 'checkAuth'])
  ->get('/api/dash/auth/csrf', [Controllers\Auth::class, 'csrf'])
  ->post('/api/dash/auth/login', [Controllers\Auth::class, 'login'], [Csrf::class])
  ->post('/api/dash/auth/logout', [Controllers\Auth::class, 'logout'], [Bearer::class, Csrf::class])
  ->get('/api/dash/totals', [Controllers\Dash\Dashboard::class, 'totals'], [Admin::class, Auth::class])
  ->post('/api/dash/sales', [Controllers\Dash\Dashboard::class, 'sales'], [Admin::class, Auth::class, Bearer::class, Csrf::class])
  ->get('/api/dash/orders', [Controllers\Dash\Dashboard::class, 'orders'], [Admin::class, Auth::class, Bearer::class])
  ->get('/api/dash/categories/popular', [Controllers\Dash\Dashboard::class, 'popular'], [Admin::class, Auth::class, Bearer::class])
  ->get('/api/dash/categories/all', [Controllers\Dash\Categories::class, 'all'], [Admin::class, Auth::class, Bearer::class])
  ->post('/api/dash/categories/add', [Controllers\Dash\Categories::class, 'insert'], [Admin::class, Auth::class, Bearer::class, Csrf::class])
  ->patch('/api/dash/categories/update', [Controllers\Dash\Categories::class, 'update'], [Admin::class, Auth::class, Bearer::class, Csrf::class])
  ->get('/api/dash/products', [Controllers\Dash\Products::class, 'all'], [Admin::class, Auth::class, Bearer::class])
  ->post('/api/dash/products/add', [Controllers\Dash\Products::class, 'insert'], [Admin::class, Auth::class, Bearer::class, Csrf::class])
  ->post('/api/dash/products/update', [Controllers\Dash\Products::class, 'update'], [Admin::class, Auth::class, Bearer::class, Csrf::class])
  ->get('/api/dash/orders/all', [Controllers\Dash\Orders::class, 'all'], [Admin::class, Auth::class, Bearer::class])
  ->patch('/api/dash/orders/ship', [Controllers\Dash\Orders::class, 'ship'], [Admin::class, Auth::class, Bearer::class]);


$router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
