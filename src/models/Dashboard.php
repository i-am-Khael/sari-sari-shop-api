<?php

  declare(strict_types=1);

  namespace Models;

  use Core\Traits\Database;

  class Dashboard {

    use Database;

    public static function totals(): array {

      $query = "
        SELECT
          (SELECT COUNT(*) FROM categories) AS total_categories,
          (SELECT COUNT(*) FROM products) AS total_products,
          (SELECT COUNT(*) FROM orders WHERE status = 'PAID') AS total_orders,
          (SELECT COALESCE(SUM(total_amount), 0.00) FROM orders WHERE status = 'PAID') AS total_sales;
      ";

      return self::readAll($query);

    }


    public static function getSales(array $params): array {
      $query = "SELECT created_at, total_amount FROM orders WHERE created_at >= ? AND created_at < ? AND status = 'PAID'";
      return self::readAll($query, $params);
    }


    public static function popularCategories(): array {

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
        WHERE o.status = 'PAID'
        GROUP BY
          c.id,
          c.category,
          c.icon,
          c.color
        ORDER by c.category ASC;
      ";

      return self::readAll($query);

    }

  }
