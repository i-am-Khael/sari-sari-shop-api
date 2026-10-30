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


  // dash user
  ->post('/api/dash/auth/check', [Controllers\Auth::class, 'checkAuth'])
  ->post('/api/dash/auth/login', [Controllers\Auth::class, 'login'])
  ->post('/api/dash/auth/logout', [Controllers\Auth::class, 'logout'])
  ->post('/api/dash/auth/csrf', [Controllers\Auth::class, 'setCSRF']);


$bs->allowedOrigin();
$router->resolve($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
