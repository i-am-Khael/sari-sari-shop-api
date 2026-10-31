<?php

declare(strict_types=1);

namespace Models\Dash;

use Core\Database;
use Core\Helper;

class Dashboard
{

  use Database;

  public static function totals(): array
  {

    $conn = self::connection();

    $query = "
      SELECT
        (SELECT COUNT(*) FROM categories) AS total_categories,
        (SELECT COUNT(*) FROM products) AS total_products,
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT COALESCE(SUM(total_amount), 0.00) FROM orders
        WHERE status IN ('PAID', 'SHIPPED', 'COMPLETED')) AS total_sales;
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll();

  }


  public static function sales(array $params): array
  {

    $conn = self::connection();

    $query = "
      SELECT
        created_at,
        total_amount
      FROM orders
      WHERE created_at >= ? AND created_at < ? AND status IN ('PAID', 'SHIPPED', 'COMPLETED')
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll();

  }


  public static function orders(): array
  {

    $conn = self::connection();

    $query = "
      SELECT
        COALESCE(SUM(status = 'PAID'), 0) AS paid,
        COALESCE(SUM(status = 'SHIPPED'), 0) AS shipped,
        COALESCE(SUM(status = 'COMPLETED'), 0) AS completed,
        COALESCE(SUM(status = 'PENDING PAYMENT'), 0) AS pending,
        COALESCE(SUM(status = 'CANCELLED'), 0) AS cancelled
      FROM orders
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll();

  }


  public static function categories(): array
  {

    $conn = self::connection();

    $query = "
      SELECT
        c.id,
        c.category,
        c.icon,
        c.color,
        SUM(oi.quantity) AS total_items_sold
      FROM categories AS c
      INNER JOIN products AS p
        ON p.category_id = c.id
      INNER JOIN order_items AS oi
        ON oi.product_id = p.id
      INNER JOIN orders as o
        ON o.id = oi.order_id
      WHERE o.status IN ('PAID', 'SHIPPED', 'COMPLETED')
      GROUP BY
        c.id,
        c.category,
        c.icon,
        c.color
      ORDER by c.category ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll();

  }

}
