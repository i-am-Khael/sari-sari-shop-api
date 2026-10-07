<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Constants as MSG;
  use Core\Helper;
  use Models\Order;

  class Orders {

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

        $result = Order::getAll();

        if ( is_array($result) ) return Helper::success($result);

        return Helper::error([ 'msg' => 'Failed to get all orders.' ]);

      } catch (\Exception $e) {

        return Helper::serverError();

      }
    }


  }
