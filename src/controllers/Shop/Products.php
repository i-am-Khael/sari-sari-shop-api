<?php

declare(strict_types=1);

namespace Controllers\Shop;

use Core\Helper;
use Models\Product;

class Products
{

  public function all(): string
  {
    try {

      $result = Product::all();

      if (!empty($result)) return Helper::success($result);

      return Helper::error([ 'msg' => 'Failed to load products.' ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function featured(): string
  {
    try {

      $result = Product::featured();

      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }

}
