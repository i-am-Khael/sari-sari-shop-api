<?php

declare(strict_types=1);

namespace Controllers\Dash;

use Core\Helper;
use Core\Constants as MSG;
use Models\Product;

class Products
{

  public function insert(): string
  {
    try {

      $errors = [];

      $product = Helper::sanitize($_POST['product'] ?? '');
      $featured = Helper::sanitize($_POST['featured'] ?? 0);
      $categoryID = (int) Helper::sanitize($_POST['category_id'] ?? 0);
      $price = (float) Helper::sanitize($_POST['price'] ?? 0);
      $stock = (int) Helper::sanitize($_POST['stock'] ?? 0);
      $description = Helper::sanitize($_POST['description'] ?? '');

      $filename = '';
      $storage = MSG::FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      if ($product === '') $errors['product'] = MSG::PRODUCT_REQUIRED;
      if ($categoryID <= 0) $errors['category'] = MSG::CATEGORY_REQUIRED;

      if (!empty($file)) {
        $filename = Helper::validateImage($file, 'product_') ?? '';
        if ($filename === 'error') $errors['file'] = MSG::INVALID_IMAGE;
      }

      if ($errors) return Helper::error($errors);

      $result = Product::insert([
        ':cid' => $categoryID,
        ':product' => $product,
        ':stock' => $stock,
        ':price' => $price,
        ':description' => $description,
        ':filename' => $filename,
        ':featured' => $featured
      ]);

      if ( $result ) {
        if (!empty($file)) move_uploaded_file($file['tmp_name'], $storage . $filename);
        return Helper::success([ 'msg' => MSG::PRODUCT_ADD_SUCCESS ], 201);
      }

      return Helper::error([ 'msg' => MSG::PRODUCT_ADD_FAILED ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function update(): string
  {
    try {

      $errors = [];

      $id = (int) Helper::sanitize($_POST['id'] ?? 0);
      $product = Helper::sanitize($_POST['product'] ?? '');
      $featured = Helper::sanitize($_POST['featured'] ?? 0);
      $categoryID = (int) Helper::sanitize($_POST['category_id'] ?? 0);
      $price = (float) Helper::sanitize($_POST['price'] ?? 0);
      $stock = (int) Helper::sanitize($_POST['stock'] ?? 0);
      $description = Helper::sanitize($_POST['description'] ?? '');
      $filename = Helper::sanitize($_POST['fileName'] ?? '');

      $storage = MSG::FILE_PATH . 'products/';
      $file = $_FILES['file'] ?? [];

      if ($id === '') $errors['product_id'] = MSG::PRODUCT_ID_REQUIRED;
      if ($product === '') $errors['product'] = MSG::PRODUCT_REQUIRED;
      if ((int) $categoryID <= 0) $errors['category'] = MSG::CATEGORY_REQUIRED;

      if (!empty($file)) {
        $filename = Helper::validateImage($file, 'product_') ?? '';
        if ($filename === 'error') $errors['file'] = MSG::INVALID_IMAGE;
      }

      if ($errors) return Helper::error($errors);

      $data = [
        ':id' => $id,
        ':cid' => $categoryID,
        ':product' => $product,
        ':price' => $price,
        ':stock' => $stock,
        ':description' => $description,
        ':featured' => $featured
      ];

      if ($filename !== '') $data[':filename'] = $filename;

      $oldFile = Product::findById($id)['file'];

      $result = Product::update($data);

      if ($result) {
        if ($filename !== '' && is_file($storage . $oldFile)) unlink($storage . $oldFile);
        if (!empty($file)) move_uploaded_file($file['tmp_name'], $storage . $filename);
        return Helper::success([ 'msg' => MSG::PRODUCT_UPDATE_SUCCESS ]);
      }

      return Helper::error([ 'msg' => MSG::PRODUCT_UPDATE_FAILED ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function all(): string
  {
    try {

      $result = Product::all();

      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


}
