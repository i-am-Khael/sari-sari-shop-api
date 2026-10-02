<?php

  declare(strict_types=1);

  namespace Controllers;
  use Core\Constants as MSG;
  use Core\Helper;
  use Models\Product;

  class Products {

    private string $key = '';
    private array $data = [];

    public function __construct() {
      header('Content-Type: application/json');
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
    }


    public function getAll(): string {

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $result = Product::getProducts();
        return Helper::success($result);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function featured(): string {

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $results = Product::featuredProducts();
        return Helper::success($results);

      } catch (\Exception $e) {

        return Helper::serverError();

      }

    }


    public function add(): string {

      $errors = [];
      $product = Helper::sanitize($_POST['product'] ?? '');
      $stock = (int) Helper::sanitize($_POST['stock'] ?? 0);
      $price = (float) Helper::sanitize($_POST['price'] ?? 0);
      $description = Helper::sanitize($_POST['description'] ?? '');
      $category_id = Helper::sanitize($_POST['category_id'] ?? '');
      $featured = Helper::sanitize($_POST['featured'] ?? 0);
      $fileName = '';
      $storage = MSG::FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);
        if ( $product === '' ) $errors['product'] = MSG::PRODUCT_REQUIRED;
        if ( (int) $category_id <= 0 ) $errors['category'] = MSG::CATEGORY_REQUIRED;

        if ( !empty($file) ) {
          $fileName = Helper::validateImage($file, 'product_') ?? '';
          if ( $fileName === 'error' ) $errors['file'] = MSG::INVALID_IMAGE;
        }

        if ( $errors ) return Helper::error($errors);

        $result = Product::addProduct([ $product, $stock, $price, $description, $fileName, $category_id, $featured ]);

        if ( $result ) {
          if ( !empty($file) ) move_uploaded_file($file['tmp_name'], $storage . $fileName);
          return Helper::success([ 'msg' => MSG::PRODUCT_ADDED_SUCCESS ], 201);
        }

        return Helper::error([ 'msg' => MSG::PRODUCT_ADDED_FAILED ]);

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
      $featured = Helper::sanitize($_POST['featured'] ?? 0);
      $fileName = Helper::sanitize($_POST['fileName'] ?? '');
      $storage = MSG::FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        if ( $id === '' ) $errors['product_id'] = MSG::PRODUCT_ID_REQUIRED;
        if ( $product === '' ) $errors['product'] = MSG::PRODUCT_REQUIRED;
        if ( (int) $category_id <= 0 ) $errors['category'] = MSG::CATEGORY_REQUIRED;

        if ( !empty($file) ) {
          $fileName = Helper::validateImage($file, 'product_') ?? '';
          if ( $fileName === 'error' ) $errors['file'] = MSG::INVALID_IMAGE;
        }

        if ( $errors ) return Helper::error($errors);

        $data = [
          'product' => $product,
          'stock' => $stock,
          'price' => $price,
          'description' => $description,
          'category' => $category_id,
          'is_featured' => $featured,
          'id' => $id,
        ];

        if ( $fileName !== '' ) $data['filename'] = $fileName;

        $oldFileName = Product::getProductById($id)['filename'];

        $result = Product::updateProduct($data);

        if ( $result && !empty($file) ) {
          unlink($storage . basename($oldFileName));
          move_uploaded_file($file['tmp_name'], $storage . $fileName);
          return Helper::success([ 'msg' => MSG::PRODUCT_UPDATED_SUCCESS ]);
        }

        return Helper::error([ 'msg' => MSG::PRODUCT_UPDATE_FAILED ]);


      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function delete(): string {

      $id = Helper::sanitize($this->data['id']);
      $storage = MSG::FILE_PATH . 'products/';

      try {

        $oldFileName = Product::getProductById($id)['filename'];
        $result = Product::deleteProduct($id);

        if ( $result ) {
          unlink($storage .$oldFileName);
          return Helper::success([ 'msg' => MSG::PRODUCT_DELETED_SUCCESS ]);
        }

        return Helper::error([ 'msg' => MSG::PRODUCT_DELETE_FAILED ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }

  }
