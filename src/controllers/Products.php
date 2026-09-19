<?php

  declare(strict_types=1);

  namespace Controllers;
  use Core\Helper;
  use Models\Product;

  class Products {

    private string $key = '';

    public function __construct() {
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
    }


    public function getAll(): string {

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);

        $result = Product::getProducts();
        return Helper::success(200, $result);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }

    public function add(): string {

      $errors = [];
      $product = Helper::sanitize($_POST['product'] ?? '');
      $stock = Helper::sanitize($_POST['stock'] ?? 0);
      $price = Helper::sanitize($_POST['price'] ?? 0);
      $description = Helper::sanitize($_POST['description'] ?? '');
      $category_id = Helper::sanitize($_POST['category_id'] ?? '');
      $fileName = '';
      $storage = FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);
        if ( $product === '' ) $errors['product'] = 'Product is required.';
        if ( (int) $category_id <= 0 ) $errors['category'] = 'Category is required.';

        if ( !empty($file) ) {
          $fileName = Helper::validateImage($file, 'product_') ?? '';
          if ( $fileName === 'error' ) $errors['file'] = IMAGE_ERROR;
        }

        if ( $errors ) return Helper::error(400, $errors);

        $result = Product::addProduct([ $product, $stock, $price, $description, $fileName, $category_id ]);

        if ( $result ) {
          if ( !empty($file) ) move_uploaded_file($file['tmp_name'], $storage . $fileName);
          return Helper::success(200, [ 'msg' => PRODUCT_OK ]);
        }

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function update(): string {

      $errors = [];
      $id = Helper::sanitize($_POST['id'] ?? '');
      $product = Helper::sanitize($_POST['product'] ?? '');
      $stock = Helper::sanitize($_POST['stock'] ?? 0);
      $price = Helper::sanitize($_POST['price'] ?? 0);
      $description = Helper::sanitize($_POST['description'] ?? '');
      $category_id = Helper::sanitize($_POST['category'] ?? '');
      $fileName = Helper::sanitize($_POST['fileName'] ?? '');
      $storage = FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);

        if ( $id === '' ) $errors['product_id'] = 'Product ID is required.';
        if ( $product === '' ) $errors['product'] = 'Product is required.';
        if ( (int) $category_id <= 0 ) $errors['category'] = 'Category is required.';

        if ( !empty($file) ) {
          $fileName = Helper::validateImage($file, 'product_') ?? '';
          if ( $fileName === 'error' ) $errors['file'] = IMAGE_ERROR;
        }

        if ( $errors ) return Helper::error(400, $errors);

        $data = [
        'id' => $id,
        'product' => $product,
        'stock' => $stock,
        'price' => $price,
        'description' => $description,
        'category' => $category_id,
        ];

        if ( $fileName !== '' ) $data['filename'] = $fileName;

        $result = Product::updateProduct($data);

        if ( $result ) {
          if ( !empty($file) ) move_uploaded_file($file['tmp_name'], $storage . $fileName);
          return Helper::success(200, [ 'msg' => PRODUCT_UPDATED ]);
        }

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function delete(): string {

      $id = Helper::sanitize($this->id);

      try {

        $result = Product::deleteProduct($id);
        if ( $result ) return Helper::success(200, [ 'msg' => DELETE_PRODUCT ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }

  }
