<?php

  declare(strict_types=1);

  namespace Models;
  use Core\Traits\Database;
  use Core\Helper;

  class Auth {

    use Database;

    public static function getUser(array $params): array {

      try {

        $query = 'SELECT id, username FROM useras WHERE username = ?';
        return [ 'data' => self::readOne($query, $params) ];

      } catch (\PDOException $e) {

        return [ 'error' => SERVER_500 ];

      }

    }


  }
