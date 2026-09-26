<?php

  declare(strict_types=1);

  namespace Models;

  use Core\Traits\Database;

  class Order {

    use Database;

    public static function createOrder(array $params = []): int {
      $query = 'INSERT INTO orders(user_id, reference_order_id, merchant_order_id, total_amount) VALUES(?, ?, ?, ?)';
      return self::lastInsertedId($query, $params);
    }


    public static function createOrderItems(array $params = []): bool {
      $query = 'INSERT INTO order_items(order_id, product_id, quantity) VALUES(?, ?, ?)';
      return self::create($query, $params);
    }

  }
