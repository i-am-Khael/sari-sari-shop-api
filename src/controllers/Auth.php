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
      $username = $this->data['username'];
      $password = $this->data['password'];
      $key = (string) $_GET['key'] ?? '';

      try {

        if ( Helper::validate($key, 'key') === 'error' ) {
          $errors['api_key'] = INVALID_KEY;
          return Helper::error(400, $errors);
        }

        if ( Helper::validate($username, 'user') === 'error' ) $errors['username'] = USER_EXISTS;
        if ( Helper::validate($username, 'username') === 'error' ) $errors['username'] = USERNAME_ERROR;
        if ( Helper::validate($password, 'password') === 'error' ) $errors['password'] = PASSWORD_ERROR;

        if ( !empty($errors) ) return Helper::error(400, $errors);

        $result = AuthModel::createUser([ $username, password_hash($password, PASSWORD_BCRYPT) ]);

        if ( $result ) return Helper::success(200, [ 'msg' => 'Registration Success!' ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function login(): string {

      $errors = [];
      $username = Helper::sanitize($this->data['username']);
      $password = Helper::sanitize($this->data['password']);
      $key = (string) $_GET['key'] ?? '';

      try {

        if ( Helper::validate($key, 'key') === 'error' ) {
          $errors['api_key'] = INVALID_KEY;
          return Helper::error(400, $errors);
        }

        $result = AuthModel::getUser($username);

        if ( count($result) > 0 ) {

          $data = [
            'id' => $result['id'],
            'user' => $result['username'],
            'is_auth' => true
          ];

          return password_verify($password, $result['password']) ? Helper::success(200, $data) : Helper::error(400,[ 'user' => INVALID_USER ]);

        }

        return Helper::error(400, [ 'user' => INVALID_USER ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }




  }
