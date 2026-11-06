<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Constants as MSG;
use Core\Interface\Middleware;


class Bearer implements Middleware
{

  public function handle(): bool
  {

    $config = parse_ini_file(MSG::FILE_INI);

    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $role = $_SESSION['role'] ?? null;

    if ($role === null) return $this->error();

    $secret = $role === 'common' ? $config['SHOP'] : $config['DASH'];

    if (!preg_match('/^Bearer\s+(.+)$/i', $token, $result)) return $this->error();

    $parts = explode('.', $result[1]);

    if (count($parts) !== 3) return $this->error();

    [ $headerPart, $payloadPart, $signaturePart ] = $parts;

    $headerData = $this->base64Decode($headerPart);
    $payloadData = $this->base64Decode($payloadPart);
    $signatureData = $this->base64Decode($signaturePart);

    if ($headerData === false || $payloadData === false || $signatureData === false) return $this->error();

    $header = json_decode($headerData, true);
    $payload = json_decode($payloadData, true);

    if (!is_array($header) || ($header['alg'] ?? null) !== 'HS256') return $this->error();

    $expected = hash_hmac(
      'sha256',
      $headerPart . '.' . $payloadPart,
      $secret,
      true
    );

    if (!hash_equals($expected, $signatureData)) return $this->error();

    if (
      ($payload['iss'] ?? null) !== '19a16e2174ba04e01b1cd9dc2cd6fe7834096ca0dee3bd83029f7bd13f2f4b41' ||
      ($payload['aud'] ?? null) !== $role ||
      !isset($payload['sub'], $payload['exp']) ||
      !is_string($payload['sub']) ||
      !is_int($payload['exp']) ||
      $payload['exp'] <= time()
    ) return $this->error();

    return true;

  }


  private function base64Decode(string $data): string|false
  {

    $data = strtr($data, '-_', '+/');

    if (!preg_match('/\A[A-Za-z0-9+\/]*={0,2}\z/', $data)) return false;

    $data = rtrim($data, '=');
    $remainder = strlen($data) % 4;

    if ($remainder === 1) return false;

    if ($remainder !== 0) {
      $data .= str_repeat('=', 4 - $remainder);
    }

    return base64_decode($data, true);

  }


  private function error(int $status = 400): bool
  {
    http_response_code($status);
    echo json_encode([
      'status' => $status,
      'errors' => [ 'msg' => 'Invalid or expired token.' ]
    ]);
    return false;
  }

}
