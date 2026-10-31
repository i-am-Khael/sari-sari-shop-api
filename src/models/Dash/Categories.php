<?php

declare(strict_types=1);

namespace Models\Dash;

use Core\Database;

class Categories
{

  use Database;


  public static function all(): array {

    $conn = self::connection();

    $query = "SELECT id, icon, category, description FROM categories ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll();

  }


  public static function category(string $category): array {

    $conn = self::connection();

    $query = "SELECT id, icon, category, description FROM categories WHERE category = :category";

    $stmt = $conn->prepare($query);
    $stmt->execute([ ':category' => $category ]);

    return $stmt->fetch();

  }


  public static function insert(array $params): int
  {

    $conn = self::connection();

    $query = "INSERT INTO categories(icon, category, description) VALUES(:icon, :category, :description)";

    $stmt = $conn->prepare($query);

    $stmt->execute($params);

    return (int) $conn->lastInsertId();

  }


  public static function update(array $params): bool
  {

    $conn = self::connection();

    $query = "
      UPDATE categories
      SET
        icon = :icon,
        category = :category,
        description = :description
      WHERE id = :id
    ";

    $stmt = $conn->prepare($query);
    return $stmt->execute($params);

    // return (bool) $stmt->rowCount();

  }


  public static function exists(string $category): bool
  {

    $conn = self::connection();

    $query = "SELECT EXISTS(SELECT 1 FROM categories WHERE category = :category) AS is_exist";

    $stmt = $conn->prepare($query);
    $stmt->execute([ ':category' => $category ]);

    return (bool) $stmt->fetch()['is_exist'];

  }


  public static function check(int $id, string $category): bool {

    $conn = self::connection();

    $query = "SELECT id, category FROM categories WHERE category = :category AND id <> :id";

    $stmt = $conn->prepare($query);

    $stmt->execute([ ':id' => $id, ':category' => $category ]);

    return (bool) $stmt->fetch();


  }


}
