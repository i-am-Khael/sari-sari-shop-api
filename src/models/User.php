<?php

declare(strict_types=1);

namespace Models;

use Core\Trait\Database;
use Core\Helper;
use Core\Constants as MSG;

class User
{

  use Database;


  public static function verifyUser(string $column, string|int $value): bool|string
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

      if(!$stmt->execute($params)) return 0;

      return (int) $conn->lastInsertId();

    } catch (\PDOException $e) {

      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


  public static function getUser(string $column, string|int $value): array
  {
    try {

      if (!in_array($column, [ 'id', 'username' ], true)) return [];

      $conn = self::connection();

      $query = "
        SELECT
          id,
          username,
          first_name as firstName,
          last_name as lastName,
          password,
          email,
          phone,
          role,
          region,
          province,
          municipality,
          brgy,
          street
        FROM users
        WHERE {$column} = :value
      ";

      $stmt = $conn->prepare($query);
      $stmt->execute([ ":value" => $value ]);

      return $stmt->fetch() ?: [];

    } catch (\PDOException $e) {

      // error_log('Database error in User::getUser: ' . $e->getMessage());
      return Helper::serverError(MSG::SERVER_ERROR_500);

    }
  }


  public static function update(array $params): bool
  {

    $conn = self::connection();

    $query = "
      UPDATE users
      SET
        first_name = :firstName,
        last_name = :lastName,
        email = :email,
        phone = :phone,
        region = :region,
        province = :province,
        municipality = :municipality,
        brgy = :brgy,
        street = :street
      WHERE
        id = :id
    ";

    $stmt = $conn->prepare($query);

    return $stmt->execute($params);

  }


}
