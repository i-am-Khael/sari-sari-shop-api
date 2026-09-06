<?php

  declare(strict_types=1);

  namespace Models;
  use Core\Traits\Database;

  class Product {

    use Database;


    public static function getProducts(array $params = []): array {
      $query = '
        SELECT
          p.id,
          p.product,
          p.product_stock AS stock,
          p.product_price AS price,
          p.product_desc AS description,
          p.product_file AS filename,
          c.category
        FROM products AS p
        INNER JOIN product_categories AS c
        ON p.category_id = c.id
      ';
      return self::readAll($query, $params);
    }


    public static function addProduct(array $params = []): bool {
      $query = '
        INSERT INTO products(
          product,
          product_stock,
          product_price,
          product_desc,
          product_file,
          category_id)
        VALUES(?, ?, ?, ?, ?, ?)
      ';
      return self::create($query, $params);
    }


    public static function updateProduct(array $params = []): bool {
      $query = '
        UPDATE
          products SET product = ?,
          product_stock = ?,
          product_price = ?,
          product_desc = ?,
          product_file = ?,
          category_id = ?,
          updated_at = NOW()
        WHERE id = ?
      ';
      return self::create($query, $params);
    }


    public static function deleteProduct(string $id): bool {
      $query = 'DELETE FROM products WHERE id = ?';
      return self::delete($query, $id);
    }

  }
