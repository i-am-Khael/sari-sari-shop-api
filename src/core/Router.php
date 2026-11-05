<?php

declare(strict_types=1);

namespace Core;

class Router
{

  private array $routes = [];


  public function register(string $requestMethod, string $route, array $action, array $middleware = []): self
  {
    $this->routes[$requestMethod][$route] = [
      'action' => $action,
      'middleware' => $middleware
    ];
    return $this;
  }


  public function get(string $route, array $action, array $middleware = []): self
  {
    return $this->register('GET', $route, $action, $middleware);
  }


  public function post(string $route, array $action, array $middleware = []): self
  {
    return $this->register('POST', $route, $action, $middleware);
  }


  public function patch(string $route, array $action, array $middleware = []): self
  {
    return $this->register('PATCH', $route, $action, $middleware);
  }


  public function delete(string $route, array $action, array $middleware = []): self
  {
    return $this->register('DELETE', $route, $action, $middleware);
  }


  private function runMiddleware(array $middleware): bool
  {
    foreach ($middleware as $mw) {

      if (!class_exists($mw)) {
        throw new \RuntimeException("Middleware {$mw} not found.");
      }

      if (!(new $mw())->handle()) return false;

    }

    return true;
  }


  public function resolve(string $requestMethod, string $route): void
  {

    $route = explode('?', $route)[0];
    $destinationRoute = $this->routes[$requestMethod][$route] ?? null;

    if (!$destinationRoute) {
      http_response_code(404);
      echo json_encode([ 'status' => 404, 'msg' => 'Page not found.' ]);
      return;
    }

    if (!$this->runMiddleware($destinationRoute['middleware'])) return;

    [ $class, $method ] = $destinationRoute['action'];

    if (class_exists($class) && method_exists($class, $method)) {
      echo call_user_func_array([new $class(), $method], []);
    }

  }


}
