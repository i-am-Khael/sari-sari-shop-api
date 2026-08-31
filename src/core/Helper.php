<?php

  declare(strict_types=1);

  namespace Core;
  use Models\Auth;

  class Helper {

    public static function _404(): void {
      echo '404 | API Not Found!';
    }


    public static function sanitizeInput(string|int $input, string $type = ''): string {

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

  }
