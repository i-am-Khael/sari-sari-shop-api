<?php

declare(strict_types=1);

namespace Controllers;

use Core\Helper;
use Core\Paypal;
use Models\Order;
use Models\Product;

class Checkout {

  private array $data = [];
  private string $key = '';

  public function __construct() {
    header('Content-Type: application/json');
    $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
    $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
  }


  public function createOrder(): string {

    if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);
    if ( empty($this->data) ) return Helper::error(400, [ 'checkout' => 'Invalid checkout data.' ]);

    $errors = [];
    $paypalItems = [];
    $orderItems = [];
    $subTotal = 0.00;
    $totalQuantity = 0;

    try {

      $userID = $this->data['user_id'];
      $items = $this->data['items'];
      $referenceId = 'cart-' . bin2hex(random_bytes(16)) . '-' . $userID;

      if ( $userID <= 0 || !is_array($items) || empty($items) ) {
        return Helper::error(400, [ 'checkout' => 'Invalid user or cart items.' ]);
      }

      foreach ($items as $item) {

        $productId = $item['id'] ?? 0;
        $quantity = (int) $item['quantity'] ?? 0;

        if ( $productId <= 0 || $quantity <= 0 ) return Helper::error(400, [ 'checkout' => 'Invalid product or quantity' ]);

        $results = Product::getProductById((string) $productId);

        if ( !$results || empty($results['product']) ) return Helper::error(400, [ 'checkout' => "Product was not found." ]);

        $product = $results['product'];
        $unitPrice = (float) $results['price'];

        if ( $unitPrice !== (float) $item['price'] ) return Helper::error(400, [ 'checkout' => 'Invalid or modified product price.' ]);

        $lineTotal = round($unitPrice * $quantity, 2);

        $paypalItems[] = [
          'name' => mb_substr($product, 0, 127),
          'quantity' => $quantity,
          'unit_amount' => [
            'currency_code' => 'USD',
            'value' => number_format($unitPrice, 2, '.', ''),
          ],
        ];

        $subTotal += $lineTotal;
        $totalQuantity += $quantity;

        $orderItems[] = [
          'product_id' => (int) $productId,
          'quantity' => $quantity
        ];

      }

      $subTotal = round($subTotal, 2);
      $amount = number_format($subTotal, 2, '.', '');

      $paypalPayload = [
        'intent' => 'CAPTURE',
        'purchase_units' => [
          [
            'reference_id' => $referenceId,
            'custom_id' => (string) $userID,
            'amount' => [
              'currency_code' => 'USD',
              'value' => $amount,
              'breakdown' => [
                'item_total' => [
                  'currency_code' => 'USD',
                  'value' => $amount,
                ],
              ],
            ],
            'items' => $paypalItems
          ],
        ],
      ];

      $paypalOrder = Paypal::createOrder($paypalPayload);

      if ( empty($paypalOrder) ) return Helper::error(502, [ 'server' => 'PayPal did not return an order ID.' ]);

      $results = Order::createOrder([ $userID, $referenceId, $paypalOrder['id'], $amount ]);

      if ( (bool) $results ) {
        foreach ($orderItems as $item) {
          Order::createOrderItems([ $results, $item['product_id'], $item['quantity'] ]);
        }
      }

      return Helper::success(200, [
        'id' => $paypalOrder['id'],
        'status' => $paypalOrder['status'] ?? null,
      ]);

    } catch (\Exception $e) {

      var_dump($e->getMessage());
      return Helper::serverError();

    }

  }



  public function captureOrder() {

    $orderId = $this->data['orders']['orderID'] ?? '';

    try {

      if ( !is_string($orderId) || $orderId === '' ) return Helper::error(400, [ 'checkout' => 'PayPal order ID is required.' ]);

      $results = Paypal::captureOrder($orderId);

      $orders = Paypal::getOrder($orderId);

      if ( $results ) {
        return Helper::success(200, [ 'resultOne' => $results, 'orders' => $orders ]);
      }

    } catch (\Exception $e) {

      var_dump($e->getMessage());
      return Helper::serverError();

    }


  }



  public function getOrder() {

  }


}
