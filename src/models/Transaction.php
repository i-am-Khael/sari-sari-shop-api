<?php

declare(strict_types=1);

namespace Models;

use Core\Trait\Database;


  class Transaction {

  use Database;

  public static function insert(array $params = []): bool
  {

    $conn = self::connection();

    $query = "
      INSERT into transactions(order_id, status, transaction_method)
      VALUES (:oid, :status, :method)
    ";

    $stmt = $conn->prepare($query);

    return $stmt->execute($params);

  }


  public static function checkTransaction(array $params): array
  {

    $conn = self::connection();

    $query = "SELECT id, order_id FROM transactions WHERE id = :id LIMIT 1";

    $stmt = $conn->prepare($query);

    $stmt->execute($params);

    return $stmt->fetch();

  }


  public static function updateTransaction(array $params): bool
  {

    $conn = self::connection();

    $query = "UPDATE TRANSACTIONS set status = :status, transaction_method = :method WHERE user_id = :id";

    $stmt = $conn->prepare($query);

    return $stmt->execute($params);


  }

}
