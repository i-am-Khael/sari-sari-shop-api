<?php

declare(strict_types=1);

namespace Controllers;

use Controllers\Paypal;
use Core\Helper;

class Checkout {

  private array $data = [];
  private string $key = '';

  public function __construct() {
    header('Content-Type: application/json');
    $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
    $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
  }


  public function createOrder(): string {

    try {


      return '';

    } catch (\Exception $e) {

      return Helper::serverError();

    }

  }


  public function getOrders(): string {

    var_dump($this->data);

    return '';

  }


}
