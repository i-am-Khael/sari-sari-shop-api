<?php

declare(strict_types=1);

namespace Controllers;

use Core\Helper;
use Core\Constants as MSG;
use Core\Auth as Token;
use Models\User;


class Auth
{

  private array $data = [];
  private string $username = '';
  private string $password = '';
  private string $role = '';
  private array $config = [];

  public function __construct()
  {

    $this->data = Helper::initializeRequest();
    $this->username = Helper::sanitize($this->data['username'] ?? '');
    $this->password = $this->data['password'] ?? '';
    $this->role = Token::checkPath();
    $this->config = parse_ini_file(MSG::FILE_INI);

  }


  public function checkAuth(): string
  {

    if ($this->role === 'error') return Helper::error([ 'msg' => 'Invalid Request.' ]);

    $auth = !empty($_SESSION['auth']) && isset($_SESSION['id'], $_SESSION['role']) && $_SESSION['role'] === $this->role;

    return Helper::success([ 'authenticated' => $auth ]);

  }


  public function csrf(): string
  {
    $csrf = Token::issueCSRFToken();

    $_SESSION['csrf_hash'] = $csrf['hash'];
    $_SESSION['csrf_expire'] = $csrf['expires'];

    return Helper::success([ 'csrf' => $csrf['token'] ]);
  }


  public function register(): string
  {
    try {

      $errors = [];
      $username = $this->username;
      $password = $this->password;

      if (User::verifyUser('username', $this->username)) $errors['username'] = MSG::USERNAME_ALREADY_EXISTS;
      if (!Helper::isValidUsername($username)) $errors['username'] = MSG::INVALID_USERNAME;
      if (!Helper::isValidPassword($password)) $errors['password'] = MSG::INVALID_PASSWORD;

      if (!empty($errors)) return Helper::error($errors);

      $id = User::createUser([
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT)
      ]);

      if ($id === 0) return Helper::error([ 'error' => MSG::REGISTRATION_FAILED ]);

      $csrf = Token::issueCSRFToken();

      session_regenerate_id(true);
      $_SESSION['id'] = $id;
      $_SESSION['username'] = $username;
      $_SESSION['role'] = 'common';
      $_SESSION['auth'] = true;
      $_SESSION['csrf_hash'] = $csrf['hash'];
      $_SESSION['csrf_expire'] = $csrf['expires'];

      $bearer = Token::issueToken((string) $id, 'common', $this->config['SHOP']);

      return Helper::success([
        'id' => $id,
        'authenticated' => true,
        'bearer' => $bearer,
        'csrf' => $csrf['token'],
        'success' => MSG::REGISTRATION_SUCCESS
      ], 201);

    } catch (\PDOException $e) {

      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


  public function login(): string
  {
    try {

      $username = $this->username;
      $password = $this->password;

      if ($this->role === 'error') return Helper::error([ 'error' => 'Invalid Request.' ]);

      $result = User::getUser('username', $username);

      if (is_array($result) && !empty($result)) {

        if ($result['role'] !== $this->role) return Helper::error([ 'error' => MSG::INVALID_USER ]);

        $verified = password_verify($password, $result['password']);

        if ($verified) {

          $csrf = Token::issueCSRFToken();

          session_regenerate_id(true);
          $_SESSION['id'] = $result['id'];
          $_SESSION['username'] = $result['username'];
          $_SESSION['role'] = $result['role'];
          $_SESSION['auth'] = true;
          $_SESSION['csrf_hash'] = $csrf['hash'];
          $_SESSION['csrf_expire'] = $csrf['expires'];

          $secret = $result['role'] === 'common' ? $this->config['SHOP'] : $this->config['DASH'];

          $bearer = Token::issueToken((string) $result['id'], $result['role'], $secret);

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
            ],
            'bearer' => $bearer,
            'csrf' => $csrf['token']
          ]);

        }

      }

      return Helper::error([ 'error' => MSG::INVALID_USER ]);

    } catch (\PDOException $e) {

      return Helper::error(MSG::SERVER_ERROR_500);

    }
  }


  public function logout(): string
  {

    $_SESSION = [];
    $this->config = [];

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
