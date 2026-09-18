<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Helper;
  use Models\Auth as AuthModel;


  class Auth {

    private array $data = [];
    private string $key = '';
    private string $username = '';
    private string $password = '';

    public function __construct() {
      header('Content-Type: application/json');
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
      $this->username = Helper::sanitize($this->data['username'] ?? '');
      $this->password = Helper::sanitize($this->data['password'] ?? '');
    }


    public function register(): string {

      $errors = [];

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);
        if ( !Helper::isValidUsername($this->username) ) $errors['username'] = USERNAME_ERROR;
        if ( !Helper::isValidPassword($this->password) ) $errors['password'] = PASSWORD_ERROR;

        $check = AuthModel::getUser($this->username);

        if ( $check ) $errors['username'] = USER_EXISTS;
        if ( !empty($errors) ) return Helper::error(400, $errors);

        $result = AuthModel::createUser([ $this->username, password_hash($this->password, PASSWORD_BCRYPT) ]);

        if ( $result ) return Helper::success(200, [ 'msg' => 'Registration Success!' ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function login(): string {

      $errors = [];

      try {

        $result = AuthModel::getUser($this->username);

        if ( $result ) {

          $data = [
            'id' => $result['id'],
            'user' => $result['username']
          ];

          return password_verify($this->password, $result['password']) ? Helper::success(200, $data) : Helper::error(400,[ 'user' => INVALID_USER ]);

        }

        return Helper::error(400, [ 'user' => INVALID_USER ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }

  }
