<?php

  declare(strict_types=1);

  namespace Models;

  use Core\Traits\Database;

  class Transactions {

    use Database;

    public static function createTransactions(array $params = []): bool {
      $query = 'INSERT INTO transactions(order_id, status, transaction_method) VALUES(?, ?, ?)';
      return self::create($query, $params);
    }

    public static function checkTransaction(array $params = []): array {
      $query = 'SELECT id, order_id FROM transactions WHERE order_id = ? LIMIT 1';
      return self::readOne($query, $params);
    }

    public static function updateTransactions(array $params = []): bool {
      $query = 'UPDATE transactions SET status = ?, transaction_method = ? WHERE order_id = ?';
      return self::create($query, $params);
    }

  }
