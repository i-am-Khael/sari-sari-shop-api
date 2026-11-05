<?php

declare(strict_types=1);

namespace Models;

use Core\Trait\Database;
use Core\Constants as MSG;
use Core\Helper;

class Token
{

  use Database;

  public static function insertUserToken(array $params): bool {
    $conn = self::connection();
    $query = "INSERT INTO auth_tokens(user_id, token, expires_at) VALUES(:id, :token, :expires)";
    $stmt = $conn->prepare($query);

    return (bool) $stmt->execute($params);
  }


  public static function updateUserToken(array $params): bool {
    $conn = self::connection();
    $query = "UPDATE auth_tokens SET token = :token WHERE user_id = :uid";
    $stmt = $conn->prepare($query);

    return (bool) $stmt->execute($params);
  }


  public static function checkToken(int $id): bool
  {
    $conn = self::connection();
    $query = "SELECT EXISTS(SELECT 1 FROM auth_tokens WHERE user_id = :uid)";
    $stmt = $conn->prepare($query);

    return (bool) $stmt->execute([ "uid" => $id ]);
  }


}
