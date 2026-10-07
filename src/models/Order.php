<?php

  declare(strict_types=1);

  namespace Models;

  use Core\Traits\Database;

  class Order {

    use Database;


    public static function getAll(): array {

      $query = "
        SELECT
          o.id,
          o.merchant_order_id AS order_id,
          o.reference_order_id AS reference_id,
          o.amount,
          o.shipping_fee,
          o.total_amount,
          o.status,
          o.order_date,
          oi.quantity,
          p.product,
          p.price,
          p.filename,
          c.category
        FROM orders AS o
        LEFT JOIN order_items AS oi ON oi.order_id = o.id
        LEFT JOIN products AS p ON p.id = oi.product_id
        LEFT JOIN categories AS c ON c.id = p.category_id
        ORDER BY o.order_date DESC;
      ";

      $rows = self::readAll($query);

      $orders = [];

      foreach ($rows as $row) {

        $id = $row['id'];

        if ( !isset($orders[$id]) ) {
          $orders[$id] = [
            'id' => $row['id'],
            'order_id' => $row['order_id'],
            'reference_id' => $row['reference_id'],
            'amount' => $row['amount'],
            'shipping_fee' => $row['shipping_fee'],
            'total_amount' => $row['total_amount'],
            'status' => $row['status'],
            'order_date' => $row['order_date'],
            'items' => [],
          ];
        }

        if ( $row['product'] !== null ) {
          $orders[$id]['items'][] = [
            'product' => $row['product'],
            'price' => $row['price'],
            'category' => $row['category'],
            'quantity' => $row['quantity'],
            'filename' => $row['filename'],
          ];
        }

      }

      return array_values($orders);

    }


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
