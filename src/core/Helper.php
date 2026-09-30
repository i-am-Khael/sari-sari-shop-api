<?php

  declare(strict_types=1);

  namespace Core;

  use DateTime;
  use DateTimeZone;
  use Models\Auth;
  use finfo;


  class Helper {

    public static function _404(): void {
      echo '404 | API Not Found!';
    }


    public static function success(int $status = 200, array $data = []): string {
      http_response_code($status);
      return json_encode([ 'status' => $status, 'data' => $data ]);
    }


    public static function error(int $status, array $data = []): string {
      http_response_code($status);
      return json_encode([ 'status' => $status, 'errors' => $data ]);
    }


    public static function serverError(): string {
      return self::error(500, [ 'server' => SERVER_ERROR_500 ]);
    }


    public static function formatDate(string $date, string $timezone): string {
      $newDate = new DateTime($date, new DateTimeZone('UTC'));
      $newDate->setTimezone(new DateTimeZone($timezone));
      return $newDate->format('Y-m-d H:i:s');
    }


    public static function sanitize(string|int|float $input): string {
      $input = (string) $input;
      $input = stripcslashes($input);
      $input = trim($input);
      return strip_tags($input);
    }


    public static function isValidInteger(string|int $input, ?int $min, ?int $max): bool {
      $input = (int) self::sanitize($input);
      $input = filter_var($input, FILTER_VALIDATE_INT);
      if ( $input === false ) return false;
      if ( $min !== null && input < $min ) return false;
      if ( $max !== null && $input > $max ) return false;
      return true;
    }


    public static function isValidMoney(string|float $input): bool {
      $input = (float) self::sanitize($input);
      return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
    }


    public static function isValidUsername(string $input): bool {
      $input = self::sanitize($input);
      return preg_match('/^[a-zA-Z0-9]{3,50}$/', $input) === 1;
    }


    public static function isValidPassword(string $input): bool {
      $input = strlen(self::sanitize($input));
      return $input >= 8 && $input <=255;
    }


    public static function validateImage(array $file, string $prefix): array|string {

      if ( !isset($file['error']) ) return 'error';
      if ( $file['error'] !== UPLOAD_ERR_OK ) return 'error';
      if ( empty($file['tmp_name'] || !is_uploaded_file($file['tmp_name'])) ) return 'error';

      $fileInfo = new finfo(FILEINFO_MIME_TYPE);
      $mimeType = (string) $fileInfo->file($file['tmp_name']);
      $oneMB = 1 * 1024 * 1024;
      $imageTypes = [
        'image/png' => 'png',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpeg',
        'image/webp' => 'webp',
      ];

      if ($file['size'] > $oneMB) return 'error';
      if (!array_key_exists($mimeType, $imageTypes)) return 'error';

      return $prefix . bin2hex(random_bytes(16)) . '.' . $imageTypes[$mimeType];

    }


    public static function hasValidApiKey(string $input): bool {
      $result = Auth::getKey($input);
      return is_array($result) && count($result) > 0;
    }

  }
