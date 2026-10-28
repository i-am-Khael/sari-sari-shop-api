<?php

declare(strict_types=1);

namespace Core;

class Router
{

  private array $routes = [];


  public function register(string $requestMethod, string $route, array $action): self
  {
    $this->routes[$requestMethod][$route] = $action;
    return $this;
  }


  public function get(string $route, array $action): self
  {
    return $this->register('GET', $route, $action);
  }


  public function post(string $route, array $action): self
  {
    return $this->register('POST', $route, $action);
  }


  public function patch(string $route, array $action): self
  {
    return $this->register('PATCH', $route, $action);
  }


  public function delete(string $route, array $action): self
  {
    return $this->register('DELETE', $route, $action);
  }


  public function resolve(string $requestMethod, string $route): void
  {

    $route = explode('?', $route)[0];
    $action = $this->routes[$requestMethod][$route] ?? null;

    if ( !$action ) {
      http_response_code(404);
      echo json_encode([ 'status' => 404, 'msg' => 'Page not found.' ]);
    }

    if ( is_array($action) ) {

      [ $class, $method ] = $action;

      if ( class_exists($class) ) {

        $class = new $class();

        if ( method_exists($class, $method) ) echo call_user_func_array([$class, $method], []);

      }

    }

  }


}
