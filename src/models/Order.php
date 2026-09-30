<?php

  declare(strict_types=1);

  namespace Models;

  use Core\Traits\Database;

  class Order {

    use Database;

    public static function createOrder(array $params = []): int {
      $query = 'INSERT INTO orders(user_id, reference_order_id, merchant_order_id, amount, shipping_fee, total_amount) VALUES(?, ?, ?, ?, ?, ?)';
      return self::lastInsertedId($query, $params);
    }


    public static function createOrderItems(array $params = []): bool {
      $query = 'INSERT INTO order_items(order_id, product_id, quantity) VALUES(?, ?, ?)';
      return self::create($query, $params);
    }


    public static function getOrderID(array $params = []): array {
      $query = 'SELECT id FROM orders WHERE reference_order_id = ? AND merchant_order_id = ?';
      return self::readOne($query, $params);
    }


    public static function updateOrderStatus(array $params): bool {
      $query = 'UPDATE orders SET status = ? WHERE reference_order_id = ? AND merchant_order_id = ?';
      return self::create($query, $params);
    }

  }
