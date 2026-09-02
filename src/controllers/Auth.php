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


    public function register(): string {

      $errors = [];
      $username = Helper::sanitize($this->data['username']);
      $password = Helper::sanitize($this->data['password']);

      try {

        $result = AuthModel::getUser([ $username ]);

        if ( is_array($result) && count($result) > 0 ) {
          $errors['username'] = 'Username already exists';
          return Helper::_error(400, $errors);
        }

        if ( !preg_match('/^[a-zA-Z\d]+$/', $username) ) {
          $errors['username'] = USERNAME_ERROR;
          return Helper::_error(400, $errors);
        }

        if ( !preg_match('/^[a-zA-Z\d]{8,255}+$/', $password) ) {
          $errors['password'] = 'password must be minimum of 8 characters';
          return Helper::_error(400, $errors);
        }

        if ( empty($errors) ) {
          $result = AuthModel::createUser([ $username, password_hash($password, PASSWORD_BCRYPT) ]);
          if ( $result ) return json_encode([ 'status' => 200, 'msg' => 'Register User Successfully!' ]);
        }

      } catch (\PDOException $e) {

        $errors['server'] = SERVER_500;
        return Helper::_error(500, $errors);

      }

    }


    public function login() {
    }



  }
