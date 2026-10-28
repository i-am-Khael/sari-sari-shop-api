<?php

declare(strict_types=1);

namespace Core;

use Core\Constants as MSG;

trait Database
{

  private static ?\PDO $pdo = null;


  public static function connection(): \PDO
  {
    try {

      if ( self::$pdo instanceof \PDO ) return self::$pdo;

      $config = parse_ini_file(MSG::FILE_INI);

      $host = $config['HOST'] ?? null;
      $dbname = $config['DBNAME'] ?? null;
      $charset = $config['CHARSET'] ?? null;
      $user = $config['DBUSER'] ?? null;
      $pass = $config['DBPASS'] ?? null;

      if ( !$config || !is_string($host) || !is_string($dbname) || !is_string($charset) || !is_string($user) || !is_string($pass) ) {
        throw new \RuntimeException('No db configuration file.');
      }

      $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

      $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
      ];

      self::$pdo = new \PDO($dsn, $user, $pass, $options);

      return self::$pdo;

    } catch (\RuntimeException $e) {

      http_response_code(500);
      throw new \RuntimeException(MSG::SERVER_ERROR_500);

    }
  }


}
