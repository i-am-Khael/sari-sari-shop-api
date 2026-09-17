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
          p.stock,
          p.price,
          p.description,
          p.filename,
          c.category
        FROM products AS p
        INNER JOIN categories AS c
        ON p.category_id = c.id
      ';
      return self::readAll($query, $params);
    }


    public static function addProduct(array $params = []): bool {
      $query = '
        INSERT INTO products(
          product,
          stock,
          price,
          desc,
          file,
          category_id)
        VALUES(?, ?, ?, ?, ?, ?)
      ';
      return self::create($query, $params);
    }


    public static function updateProduct(array $params = []): bool {
      $query = '
        UPDATE
          products SET product = ?,
          stock = ?,
          price = ?,
          description = ?,
          file = ?,
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
