<?php

declare(strict_types=1);

namespace Controllers;

use Core\Helper;
use Core\Constants as MSG;
use Models\User;


class Auth
{


  private array $data = [];
  private string $username = '';
  private string $password = '';


  public function __construct()
  {

    $this->data = Helper::initializeRequest();
    $this->username = Helper::sanitize($this->data['username'] ?? '');
    $this->password = $this->data['password'];

  }


  public function register(): string
  {

    $username = $this->username;
    $password = $this->password;

    if (User::verifyUser('username', $this->username)) {
      return Helper::error([ 'username' => MSG::USERNAME_ALREADY_EXISTS ]);
    }

    if (!Helper::isValidUsername($username)) {
      return Helper::error([ 'username' => MSG::INVALID_USERNAME ]);
    }

    if (!Helper::isValidPassword($password)) {
      return Helper::error([ 'password' => MSG::INVALID_PASSWORD ]);
    }

    $id = User::createUser([
      'username' => $username,
      'password' => password_hash($password, PASSWORD_DEFAULT)
    ]);

    if ($id === 0) return Helper::error([ 'msg' => MSG::REGISTRATION_FAILED ]);

    return Helper::success([
      'id' => $id,
      'username' => $username
    ]);

  }


  public function login(): string
  {

    $username = $this->username;
    $password = $this->password;

    $result = User::getUser('username', $username);

    if (is_array($result) && !empty($result)) {

      $verified = password_verify($password, $result['password']);

      if ($verified) return Helper::success([
        'id' => $result['id'],
        'username' => $result['username']
      ]);

    }

    return Helper::error([ 'msg' => MSG::INVALID_USER ]);

  }

}
