<?php

declare(strict_types=1);

namespace Controllers\Shop;

use Core\Helper;
use Core\Constants as MSG;

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

}
