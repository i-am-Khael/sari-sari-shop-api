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
    $this->password = $this->data['password'] ?? '';

  }


  public function checkAuth(): string
  {

    $role = Helper::checkPath();

    if ($role === 'error') return Helper::error([ 'msg' => 'Invalid Request.' ]);

    $auth = !empty($_SESSION['auth']) && isset($_SESSION['id'], $_SESSION['role']) && $_SESSION['role'] === $role;

    return Helper::success([ 'authenticated' => $auth ]);

  }


  public function register(): string
  {
    try {

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

      session_regenerate_id(true);
      $_SESSION['id'] = $id;
      $_SESSION['username'] = $username;
      $_SESSION['role'] = 'common';
      $_SESSION['auth'] = true;

      return Helper::success([
        'id' => $id,
        'authenticated' => true,
      ]);

    } catch (\PDOException $e) {

      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


  public function login(): string
  {
    try {


      $username = $this->username;
      $password = $this->password;

      $role = Helper::checkPath();

      if ($role === 'error') return Helper::error([ 'msg' => 'Invalid Request.' ]);

      $result = User::getUser('username', $username);

      if (is_array($result) && !empty($result)) {

        if ($result['role'] !== $role) return Helper::error([ 'msg' => MSG::INVALID_USER ]);

        $verified = password_verify($password, $result['password']);

        if ($verified) {

          session_regenerate_id(true);
          $_SESSION['id'] = $result['id'];
          $_SESSION['username'] = $result['username'];
          $_SESSION['role'] = $result['role'];
          $_SESSION['auth'] = true;

          return Helper::success([
            'profile' => [
              'id' => $result['id'],
              'username' => $result['username'],
              'firstName' => $result['firstName'],
              'lastName' => $result['lastName'],
              'email' => $result['email'],
              'phone' => $result['phone'],
              'region' => $result['region'],
              'province' => $result['province'],
              'municipality' => $result['municipality'],
              'brgy' => $result['brgy'],
              'street' => $result['street']
            ]
          ]);

        }

      }

      return Helper::error([ 'msg' => MSG::INVALID_USER ]);

    } catch (\PDOException $e) {

      return Helper::error(MSG::SERVER_ERROR_500);

    }
  }


  public function logout(): string
  {

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

      $params = session_get_cookie_params();

      setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax'
      ]);

    }

    session_destroy();

    return Helper::success([ 'authenticated' => false ]);

  }


}
