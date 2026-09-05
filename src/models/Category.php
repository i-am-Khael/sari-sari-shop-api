<?php

  declare(strict_types=1);

  namespace Models;
  use Core\Traits\Database;


  class Category {

    use Database;

    public static function addCategory(array $params): bool {
      $query = 'INSERT INTO product_categories(category, category_desc) VALUES(?, ?)';
      return self::create($query, $params);
    }


    public static function getCategory(string $category): array {
      $query = 'SELECT category FROM product_categories WHERE category = ?';
      return self::readOne($query, [ $category ]);
    }


    public static function getCategories(): array {
      $query = 'SELECT id, category, category_desc FROM product_categories';
      return self::readAll($query);
    }


    public static function updateCategory(array $params): bool {
      $query = 'UPDATE product_categories SET category = ?, category_desc = ?, updated_at = NOW() WHERE id = ?';
      return self::create($query, $params);
    }


    public static function deleteCategory(string $id) :bool {
      $query = 'DELETE FROM product_categories WHERE id = ?';
      return self::delete($query, $id);
    }

  }
