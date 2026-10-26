<?php

declare(strict_types=1);

namespace Core\Traits;

use Core\Constants as MSG;

final class Database
{

  private ?\PDO $pdo = null;


  public function connection(): \PDO
  {
    try {

      if ( $this->pdo instanceof \PDO ) return $this->pdo;

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

      $this->pdo = new \PDO($dsn, $user, $pass, $options);

      return $this->pdo;

    } catch (\RuntimeException $e) {

      http_response_code(500);
      throw new \RuntimeException(MSG::SERVER_ERROR_500);

    }
  }


}
