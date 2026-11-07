<?php

declare(strict_types=1);

namespace Controllers\Shop;

use Core\Helper;
use Models\Category;
use Models\Dashboard;

class Categories
{


  public function all(): string
  {
    try {

      $result = Category::all();

      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function popular(): string
  {
    try {

      $result = Dashboard::popularCategories();

      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function category(): string
  {
    try {

      $input = Helper::initializeRequest();

      if (empty($input)) return Helper::error([ 'msg' => 'Category id or name is required.' ]);

      if (!empty($input['id'])) {

        $result = Category::findById((int) $input['id']);

      } else {

        $result = Category::findByCategory($input['category']);

      }

      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }

}
