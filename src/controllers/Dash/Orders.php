<?php

declare(strict_types=1);

namespace Controllers\Dash;

use Core\Helper;
use Core\Constants as MSG;
use Models\Order;

class Orders
{

  public static function all(): string
  {
    try {

      $result = Order::all();

      if (is_array($result)) return Helper::success($result);

      return Helper::error([ 'msg' => 'Failed to load orders.' ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function ship(): string
  {
    try {

      $input = Helper::initializeRequest();
      $id = Helper::sanitize($input['id']);
      $merid = Helper::sanitize($input['order_id']);
      $refid = Helper::sanitize($input['reference_id']);

      $result = Order::updateStatus([
        ':id' => $id,
        ':merid' => $merid,
        ':refid' => $refid,
        ':status' => 'SHIPPED'
      ]);

      return Helper::success([ 'msg' => 'Order is now shipped.' ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


}
