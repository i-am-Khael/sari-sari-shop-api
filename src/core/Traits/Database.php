<?php

  declare(strict_types=1);

  namespace Core\Traits;

  use Core\Constants as MSG;

  trait Database {

    private static ?\PDO $conn = null;


    private static function setConnection(): \PDO {

      if ( self::$conn instanceof \PDO ) return self::$conn;

      $config = parse_ini_file(MSG::FILE_INI);
      $host = $config['HOST'] ?? '';
      $dbname = $config['DBNAME'] ?? '';
      $charset = $config['CHARSET'] ?? '';
      $user = $config['DBUSER'] ?? '';
      $pass = $config['DBPASS'] ?? '';

      $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

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


    protected  static function lastInsertedId(string $query, array $params): int {
      $stmt = self::setConnection()->prepare($query);
      if ( !$stmt->execute($params) ) return 0;
      return (int) self::setConnection()->lastInsertId();
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


    protected static function delete(string $query, string $id): bool {
      $stmt = self::setConnection()->prepare($query);
      return $stmt->execute([ $id ]);
    }


  }
