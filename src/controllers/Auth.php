<?php

  declare(strict_types=1);

  namespace Controllers;
  use Core\Helper;
  use Models\Auth as AuthModel;

  class Auth {

    private array $data;

    public function __construct() {
      header('Content-Type: application/json');
      $this->data = json_decode(file_get_contents('php://input'), true);
    }


    public function register(): string|false {

      $errors = [];
      $username = Helper::sanitizeInput($this->data['username']);
      $password = Helper::sanitizeInput($this->data['password']);

      $result = AuthModel::getUser([ $username ]);

      if ( isset($result['data']) ) $errors['username'] = 'Username already exists.';
      if ( $errors ) return json_encode([ 'status' => 400, 'errors' => $errors ]);
      if ( isset($result['error']) ) return json_encode([ 'status' => 500, 'errors' => $result ]);



    }


    public function login() {
    }



  }
