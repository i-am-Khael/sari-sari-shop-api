<?php

declare(strict_types=1);

namespace Models;

use Core\Database;
use Core\Helper;
use Core\Constants as MSG;

class User
{

  use Database;


  public function verifyUser(string $column, string|int $value): bool|string
  {
    try {

      if (!in_array($column, [ 'id', 'username' ], true)) return false;

      $conn = self::connection();

      $query = "SELECT 1 FROM users WHERE {$column} = :value LIMIT 1";
      $stmt = $conn->prepare($query);
      $stmt->execute([ ':value' => $value ]);

      return (bool) $stmt->fetch();

    } catch (\PDOException $e) {

      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


  public static function createUser(array $params): int|string
  {
    try {

      $conn = self::connection();

      $query = "INSERT INTO users(username, password) VALUES(:username, :password)";
      $stmt = $conn->prepare($query);

      if(!stmt->execute($params)) return 0;

      return (int) $conn->lastInsertId();

    } catch (\PDOException $e) {

      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


  public static function createUserAddress(array $params): bool|string
  {
    try {

      $conn = self::connection();

      $query = "INSERT INTO address(user_id, region, provice, municipality, brgy, street) VALUES(:user_id, :region, :province, :municipality, :brgy, :street)";

      $stmt = $conn->prepare($query);

      return $stmt->execute($params);

    } catch (\PDOException $e) {

      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


  public static function getUser(string $column, string|int $value): array|string
  {
    try {

      if (!in_array($column, [ 'id', 'username' ], true)) return [];

      $conn = self::connection();

      $query = "
        SELECT
          u.id,
          u.username,
          u.password,
          u.email,
          u.phone,
          u.role,
          a.region,
          a.province,
          a.municipality,
          a.brgy,
          a.street
        FROM users AS u
        RIGHT JOIN address AS a
        ON u.id = a.user_id
        WHERE u.{$column} = :value
      ";

      $stmt = $conn->prepare($query);
      $stmt->execute([ ":value" => $value ]);

      return $stmt->fetch() ?: [];

    } catch (\PDOException $e) {

      // error_log('Database error in User::getUser: ' . $e->getMessage());
      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


}
