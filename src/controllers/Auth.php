<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Constants as MSG;
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

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);
        if ( !Helper::isValidUsername($this->username) ) $errors['username'] = MSG::INVALID_USERNAME;
        if ( !Helper::isValidPassword($this->password) ) $errors['password'] = MSG::INVALID_PASSWORD;

        $check = AuthModel::getUser($this->username);

        if ( $check ) $errors['username'] = MSG::USERNAME_ALREADY_EXISTS;
        if ( !empty($errors) ) return Helper::error($errors);

        $result = AuthModel::createUser([ $this->username, password_hash($this->password, PASSWORD_BCRYPT) ]);

        if ( $result ) return Helper::success([ 'msg' => MSG::REGISTRATION_SUCCESS ], 201);

        return Helper::error([ 'msg' => MSG::REGISTRATION_FAILED ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function login(): string {

      $data = [];

      try {

        $result = AuthModel::getUser($this->username);

        if ( $result ) {

          $data['id'] = $result['id'];
          $data['user'] = $result['username'];

          if ( password_verify($this->password, $result['password']) ) {
            return Helper::success($data);
          }

          return Helper::error([ 'msg' => MSG::INVALID_USER ]);

        }

        return Helper::error([ 'msg' => MSG::INVALID_USER ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }

  }
