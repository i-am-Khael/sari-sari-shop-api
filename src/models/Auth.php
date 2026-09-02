<?php

  declare(strict_types=1);

  namespace Models;
  use Core\Traits\Database;
  use Core\Helper;

  class Auth {

    use Database;

    public static function createUser(array $params): bool {

      $query = 'INSERT INTO users(username, password) VALUES(?, ?)';
      return self::create($query, $params);

    }


    public static function getUser(array $params): array {

      $query = 'SELECT id, username FROM users WHERE username = ?';
      return self::readOne($query, $params);

    }




  }
