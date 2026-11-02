<?php

declare(strict_types=1);

namespace Core;

use finfo;
use DateTime;
use DateTimeZone;
use Core\Constants as MSG;

class Helper
{


  public static function initializeRequest(): array
  {
    header('Content-Type: application/json');
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    return $data ?? [];
  }


  public static function checkPath(): string
  {

    $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

    if (str_starts_with($route, '/api/dash/')) {

      return 'administrator';

    } elseif (str_starts_with($route, '/api/shop/')) {

      return 'common';

    } else {

      return 'error';

    }

  }


  public static function createToken(): array
  {

    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);
    $expires = new \DateTimeImmutable('+1 hour');

    return [
      'token' => $hashedToken,
      'expires' => $expires
    ];

  }


  public static function formatDate(string $date, string $timezone): string {
    $newDate = new DateTime($date, new DateTimeZone('UTC'));
    $newDate->setTimezone(new DateTimeZone($timezone));
    return $newDate->format('Y-m-d H:i:s');
  }


  public static function sanitize(string|int|float $input): string
  {
    $input = (string) $input;
    $input = stripcslashes($input);
    $input = trim($input);
    return strip_tags($input);
  }


  public static function isValidInteger(string|int $input, ?int $min, ?int $max): bool
  {
    $input = (int) self::sanitize($input);
    $input = filter_var($input, FILTER_VALIDATE_INT);
    if ( $input === false ) return false;
    if ( $min !== null && input < $min ) return false;
    if ( $max !== null && $input > $max ) return false;
    return true;
  }


  public static function isValidMoney(string|float $input): bool
  {
    $input = (float) self::sanitize($input);
    return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
  }


  public static function validateImage(array $file = [], string $prefix = ''): string
  {

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


  public static function isValidEmail(string $input): bool
  {
    $input = self::sanitize($input);
    return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
  }


  public static function isValidUsername(string $input): bool
  {
    $input = self::sanitize($input);
    return preg_match('/^[a-zA-Z0-9]{3,50}$/', $input) === 1;
  }


  public static function isValidPassword(string $input): bool
  {
    $input = strlen(self::sanitize($input));
    return $input >= 8 && $input <=255;
  }


  public static function success(array $data = [], int $status = 200): string
  {
    http_response_code($status);
    return json_encode([ 'status' => $status, 'data' => $data ]);
  }


  public static function error(array $data = [], int $status = 400): string
  {
    http_response_code($status);
    return json_encode([ 'status' => $status, 'errors' => $data ]);
  }


  public static function serverError(string $msg = '', int $status = 500): string
  {
    if ( $msg === '' ) $msg = MSG::SERVER_ERROR_500;
    return self::error([ 'server' => $msg ], $status);
  }

}
