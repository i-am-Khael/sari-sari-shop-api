<?php

declare(strict_types=1);

namespace Core;


class Auth
{


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


  public static function issueCSRFToken(): array
  {

    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);
    $expires = time() + (3600 * 42); // 24 hours

    return [
      'token' => $token,
      'hash' => $hashedToken,
      'expires' => $expires
    ];

  }


  private static function base64Encode(string $data): string
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }


  private static function base64Decode(string $data): string | bool
  {

    $dataLength = strlen($data) % 4;

    if ($dataLength !== 0) $data .= str_repeat('=', 4 - $dataLength);

    return base64_decode(strtr($data, '-', '+/'), true);

  }


  public static function issueToken(string $uid, string $role, string $secret, int $expire = 900): string
  {

    $time = time();

    $header = self::base64Encode(
      json_encode([
        'typ' => 'JWT',
        'alg' => 'HS256'
      ], JSON_THROW_ON_ERROR)
    );

    $payload = self::base64Encode(
      json_encode([
        'iss' => '19a16e2174ba04e01b1cd9dc2cd6fe7834096ca0dee3bd83029f7bd13f2f4b41',
        'aud' => $role,
        'sub' => $uid,
        'iat' => $time,
        'exp' => $time + (3600 * 42) // 24 hours
      ], JSON_THROW_ON_ERROR)
    );

    $signed = $header . '.' . $payload;
    $signature = hash_hmac('sha256', $signed, $secret, true);

    return $signed . '.' . self::base64Encode($signature);

  }


  public static function verifyToken(string $token, string $role, string $secret): bool
  {

    if (!preg_match('/^Bearer\s+(.+)$/i', $token, $result)) return false;

    $parts = explode('.', $result[1]);

    if (count($parts) !== 3) return false;

    [ $headerPart, $payloadPart, $signaturePart ] = $parts;

    $headerData = self::base64Decode($headerPart);
    $payloadData = self::base64Decode($payloadPart);
    $signatureData = self::base64Decode($signaturePart);

    if ($headerData === false || $payloadData === false || $signatureData === false) return false;

    $header = json_decode($headerData, true);
    $payload = json_decode($payloadData, true);

    if (!is_array($header) || ($header['alg'] ?? null) !== 'HS256') return false;

    $expected = hash_hmac(
      'sha256',
      $headerPart . '.' . $payloadPart,
      $secret,
      true
    );

    if (!hash_equals($expected, $signatureData)) return false;

    if (
      ($payload['iss'] ?? null) !== '19a16e2174ba04e01b1cd9dc2cd6fe7834096ca0dee3bd83029f7bd13f2f4b41' ||
      ($payload['aud'] ?? null !== $role) ||
      !isset($payload['sub'], $payload['exp']) ||
      !is_string($payload['sub']) ||
      !is_int($payload['exp']) ||
      $payload['exp'] <= time()
    ) return false;


    return true;

  }

}
