<?php

declare(strict_types=1);

namespace Controllers\Dash;

use Core\Helper;
use Core\Constants as MSG;
use Models\Dash\Categories as CModel;


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

      if (CModel::exists($category)) return Helper::error([ 'category' => MSG::CATEGORY_ALREADY_EXISTS ]);

      $result = CModel::insert([
        ':icon' => $icon,
        ':category' => $category,
        ':description' => $description
      ]);

      if ((bool) $result) return Helper::success([ 'msg' => MSG::CATEGORY_ADDED_SUCCESS ], 201);

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

      if (CModel::check($id, $category)) return Helper::error([ 'category' => MSG::CATEGORY_ALREADY_EXISTS ]);

      $result = CModel::update([
        ':id' => $id,
        ':icon' => $icon,
        ':category' => $category,
        ':description' => $description
      ]);

      if ($result) return Helper::success([ 'msg' => MSG::CATEGORY_UPDATED_SUCCESS ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function categories(): string
  {
    try {

      $result = CModel::all();
      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


}
