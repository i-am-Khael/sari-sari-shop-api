<?php

  declare(strict_types=1);

  namespace Core;
  use Models\Auth;

  class Helper {

    public static function _404(): void {
      echo '404 | API Not Found!';
    }


    public static function serverError(): string {
      return self::error(500, [ 'server' => SERVER_ERROR_500 ]);
    }


    public static function success(int $status = 200, array $data = []): string {
      http_response_code($status);
      return json_encode([ 'status' => $status, 'data' => $data ]);
    }

    public static function error(int $status, array $data = []): string {
      http_response_code($status);
      return json_encode([ 'status' => $status, 'errors' => $data ]);
    }


    public static function sanitize(string|int $input, string $type = ''): string {

      $input = (string) $input;
      $input = stripcslashes($input);
      $input = trim($input);

      switch ($type) {

        case 'email':
              $email = filter_var($input, FILTER_VALIDATE_EMAIL);
              return $email ? $email : 'error';

        case 'number':
              $number = filter_var($input, FILTER_VALIDATE_INT);
              return $number ? (string) $number : '0';

        case 'money':
              $number = filter_var($input, FILTER_VALIDATE_FLOAT);
              return $number ? (string) $number : '0';

        case 'text':
              return strip_tags($input);

        default:
              return strip_tags($input);

      }

    }


    public static function validate(string $input, string $type = ''): string {

      switch($type) {

        case 'username':
          $username = self::sanitize($input);
          return !preg_match('/^[a-zA-Z0-9]{3,50}+$/', $username) ? 'error' : '';

        case 'password':
          $password = self::sanitize($input);
          return strlen($password) >= 8 && strlen($password) <= 255 ? '' : 'error';

        case 'user':
          $username = self::sanitize($input);
          $result = Auth::getUser($username);
          return ( is_array($result) && count($result) > 0 ) ? 'error' : '';

        case 'key':
          $result = Auth::getKey($input);
          return ( is_array($result) && count($result) === 0 ) ? 'error' : '';

        default:
          return '';

      }

    }


  }
