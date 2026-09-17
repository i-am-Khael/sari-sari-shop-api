<?php

  declare(strict_types=1);

  namespace Controllers;
  use Core\Helper;
  use Models\Product;

  class Products {

    private array $data = [];
    private string $key = '';
    private string $id = '';

    public function __construct() {
      header('Content-Type: application/json');
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
      $this->id = Helper::sanitize($this->data['id'] ?? '');
    }


    public function getAll(): string {

      try {

        if ( Helper::validate($this->key, 'key') === 'error' ) {
          return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        }

        $result = Product::getProducts();
        return Helper::success(200, $result);

      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }

    public function add(): string {

      $errors = [];
      $product = Helper::sanitize($_POST['product'] ?? '');
      $stock = Helper::sanitize($_POST['stock'] ?? '', 'number');
      $price = Helper::sanitize($_POST['price'] ?? '', 'money');
      $description = Helper::sanitize($_POST['description'] ?? '');
      $category_id = Helper::sanitize($_POST['category_id'] ?? '');
      $fileName = '';
      $storage = FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      try {

        if ( Helper::validate($this->key, 'key') === 'error' ) {
          return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        }

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

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }


    public function update(): string {

      $errors = [];
      $id = Helper::sanitize($_POST['id'] ?? '');
      $product = Helper::sanitize($_POST['product'] ?? '');
      $stock = Helper::sanitize($_POST['stock'] ?? '', 'number');
      $price = Helper::sanitize($_POST['price'] ?? '', 'money');
      $description = Helper::sanitize($_POST['description'] ?? '');
      $category_id = Helper::sanitize($_POST['category_id'] ?? '');
      $fileName = Helper::sanitize($_POST['fileName'] ?? '');
      $storage = FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      try {

        if ( Helper::validate($this->key, 'key') ) {
          return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        }

        if ( !empty($file) ) {
          $fileName = Helper::validate($file, 'product_') ?? '';
          if ( $fileName === 'error' ) $errors['file'] = IMAGE_ERROR;
        }

        if ( $errors ) return Helper::error(400, $errors);

        $result = Product::updateProduct([ $product, $stock, $price, $description, $fileName, $category_id, $id ]);

        if ( $result ) {
          if ( !empty($file) ) move_upload_file($file['tmp_name'], $storage . $fileName);
          return Helper::success(200, [ 'msg' => PRODUCT_UPDATED ]);
        }

      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }


    public function delete(): string {

      $id = Helper::sanitize($this->id);

      try {

        $result = Product::deleteProduct($id);
        if ( $result ) return Helper::success(200, [ 'msg' => DELETE_PRODUCT ]);

      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }

  }
