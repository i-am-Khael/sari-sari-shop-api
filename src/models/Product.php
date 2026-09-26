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
          c.id AS category_id,
          c.category
        FROM products AS p
        INNER JOIN categories AS c
        ON p.category_id = c.id
      ';
      return self::readAll($query, $params);
    }


    public static function getProductById(string $id): array {
      $query = 'SELECT id, product, price, category_id FROM products WHERE id = ?';
      return self::readOne($query, [ $id ]);
    }


    public static function addProduct(array $params = []): bool {
      $query = '
        INSERT INTO products(
          product,
          stock,
          price,
          description,
          filename,
          category_id
        ) VALUES(?, ?, ?, ?, ?, ?)
      ';
      return self::create($query, $params);
    }


    public static function updateProduct(array $params = []) {
      $set = [
        'product = :product',
        'stock = :stock',
        'price = :price',
        'description = :description',
        'category_id = :category'
      ];

      if ( array_key_exists('filename', $params) ) $set[] = 'filename = :filename';

      $query = 'UPDATE products SET ' . implode(', ', $set) . ' WHERE id = :id';

      return self::create($query, $params);
    }


    public static function deleteProduct(string $id): bool {
      $query = 'DELETE FROM products WHERE id = ?';
      return self::delete($query, $id);
    }

  }
