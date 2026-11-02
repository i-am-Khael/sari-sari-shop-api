<?php

declare(strict_types=1);

namespace Controllers\Dash;

use Core\Helper;
use Core\Constants as MSG;
use Models\Category;


class Categories
{

  private array $data = [];

  public function __construct()
  {
    $this->data = Helper::initializeRequest();
  }


  public function insert(): string
  {
    try {

      $icon = $this->data['icon'] ?? '';
      $category = Helper::sanitize($this->data['category'] ?? '');
      $description = Helper::sanitize($this->data['description'] ?? '');

      if (Category::exists($category)) return Helper::error([ 'category' => MSG::CATEGORY_ALREADY_EXISTS ]);

      $result = Category::insert([
        ':icon' => $icon,
        ':category' => $category,
        ':description' => $description
      ]);

      if ((bool) $result) return Helper::success([ 'msg' => MSG::CATEGORY_ADD_SUCCESS ], 201);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function update(): string
  {
    try {

      $id = $this->data['id'] ?? '';
      $icon = $this->data['icon'] ?? '';
      $category = Helper::sanitize($this->data['category'] ?? '');
      $description = Helper::sanitize($this->data['description'] ?? '');

      if (Category::check($id, $category)) return Helper::error([ 'category' => MSG::CATEGORY_ALREADY_EXISTS ]);

      $result = Category::update([
        ':id' => $id,
        ':icon' => $icon,
        ':category' => $category,
        ':description' => $description
      ]);

      if ($result) return Helper::success([ 'msg' => MSG::CATEGORY_UPDATE_SUCCESS ]);

      return Helper::error([ 'msg' => MSG::CATEGORY_UPDATE_FAILED ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function all(): string
  {
    try {

      $result = Category::all();
      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


}
