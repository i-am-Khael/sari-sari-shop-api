<?php

declare(strict_types=1);

namespace Models;

use Core\Trait\Database;


class Product
{

  use Database;


  public static function insert(array $params): int
  {

    $conn = self::connection();

    $query = "
      INSERT INTO products(category_id, product, stock, price, description, filename, is_featured)
      VALUES(:cid, :product, :stock, :price, :description, :filename, :featured)
    ";

    $stmt = $conn->prepare($query);

    $stmt->execute($params);

    return (int) $conn->lastInsertId();

  }


  public static function update(array $params): bool
  {

    $conn = self::connection();

    $query = "
      UPDATE
        products
      SET
        category_id = :cid,
        product = :product,
        price = :price,
        stock = :stock,
        description = :description,
        is_featured = :featured
    ";

    if (array_key_exists(':filename', $params)) $query .= ", filename = :filename";

    $query .= " WHERE id = :id";

    $stmt = $conn->prepare($query);

    return $stmt->execute($params);

  }


  public static function all(): array
  {

    $conn = self::connection();

    $query = "
      SELECT
        p.id,
        p.category_id,
        p.product,
        p.price,
        p.stock,
        p.description,
        p.filename AS file,
        p.is_featured AS featured,
        c.category
      FROM
        products AS p
      LEFT JOIN
        categories AS c
      ON
        p.category_id = c.id
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll();

  }


  public static function findById(int $id): array {

    $conn = self::connection();

    $query = "
      SELECT
        p.id,
        p.category_id,
        p.product,
        p.price,
        p.stock,
        p.description,
        p.filename AS file,
        p.is_featured AS featured,
        c.category
      FROM
        products AS p
      INNER JOIN
        categories AS c
      ON
        p.category_id = c.id
      WHERE p.id = :pid
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([ ':pid' => $id ]);

    return $stmt->fetch();

  }


  public static function findByName(string $product): array {

    $conn = self::connection();

    $query = "
      SELECT
        p.id,
        p.category_id,
        p.product,
        p.price,
        p.stock,
        p.description,
        p.filename AS file,
        p.is_featured AS featured,
        c.category
      FROM
        products AS p
      INNER JOIN
        categories AS c
      ON
        p.category_id = c.id
      WHERE p.product = :product
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([ ':product' => $product ]);

    return $stmt->fetch();

  }

}
