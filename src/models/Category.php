<?php

  declare(strict_types=1);

  namespace Models;
  use Core\Traits\Database;


  class Category {

    use Database;


    public static function addCategory(array $params): bool {
      $query = 'INSERT INTO categories(category, description) VALUES(?, ?)';
      return self::create($query, $params);
    }


    public static function getCategory(string $category): array {
      $query = 'SELECT id, category, description FROM categories WHERE category = ?';
      return self::readOne($query, [ $category ]);
    }


    public static function getCategories(): array {
      $query = 'SELECT id, category, description FROM categories';
      return self::readAll($query);
    }


    public static function checkCategory(string $category, string $id): ?array {
      $query = 'SELECT id, category, description FROM categories WHERE category = ? AND id <> ? LIMIT 1';
      return self::readOne($query, [ $category, $id ]);
    }


    public static function updateCategory(array $params): bool {
      $query = 'UPDATE categories SET category = ?, description = ? WHERE id = ?';
      return self::create($query, $params);
    }


    public static function deleteCategory(string $id) :bool {
      $query = 'DELETE FROM categories WHERE id = ?';
      return self::delete($query, $id);
    }

  }
