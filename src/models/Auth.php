<?php

  declare(strict_types=1);

  namespace Models;
  use Core\Traits\Database;

  class Auth {

    use Database;

    public static function getKey(string $key): array {
      $query = "SELECT secret FROM secrets WHERE secret = ?";
      return self::readOne($query, [ $key ]);
    }


    public static function createUser(array $params): bool {
      $query = 'INSERT INTO users(username, password) VALUES(?, ?)';
      return self::create($query, $params);
    }


    public static function getUser(string $username): array {
      $query = 'SELECT id, username, password FROM users WHERE username = ?';
      return self::readOne($query, [ $username ]);
    }


  }
