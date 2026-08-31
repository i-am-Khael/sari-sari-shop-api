<?php

  declare(strict_types=1);

  namespace Core\Traits;

  trait Database {

    private static ?\PDO $conn = null;


    private static function setConnection(): \PDO {

      if ( self::$conn instanceof \PDO ) return self::$conn;

      $dsn = 'mysql:host=127.0.0.1;dbname=e_store_api;charset=utf8mb4';
      $user = 'root';
      $pass = '';
      $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
      ];

      try {

        self::$conn = new \PDO($dsn, $user, $pass, $options);
        return self::$conn;

      } catch (\PDOException $e) {

        http_response_code(500);
        throw new \RuntimeException(SERVER_500);

      }

    }


    protected static function create(string $query, array $params): bool {
      $stmt = self::setConnection()->prepare($query);
      return $stmt->execute($params);
    }


    protected static function readOne(string $query, array $params = []): array {
      $stmt = self::setConnection()->prepare($query);
      $stmt->execute($params);
      $result =  $stmt->fetch();
      return $result ?: [];
    }


    protected static function readAll(string $query, array $params = []): array {
      $stmt = self::setConnection()->prepare($query);
      $stmt->execute($params);
      $result = $stmt->fetchAll();
      return $result ?: [];
    }


  }
