<?php

declare(strict_types=1);

namespace Controllers\Shop;

use Core\Helper;
// use Core\Constants as MSG;
use Core\Paypal;
use Models\Product;
use Models\Order;
use Models\Transaction;

class Checkout {


  public function create(): string
  {
    try {

      $input = Helper::initializeRequest();

      $errors = [];
      $paypalItems = [];
      $orderItems = [];
      $subTotal = 0.00;
      $totalQuantity = 0;

      $userID = $input['user_id'];
      $items = $input['items'];
      $referenceId = 'cart-' . bin2hex(random_bytes(16)) . '-' . $userID;

      if ( $userID <= 0 || !is_array($items) || empty($items) ) {
        return Helper::error([ 'checkout' => 'Invalid user or cart items.' ]);
      }

      foreach ($items as $item) {

        $productId = $item['id'] ?? 0;
        $quantity = (int) $item['quantity'] ?? 0;

        if ( $productId <= 0 || $quantity <= 0 ) return Helper::error([ 'checkout' => 'Invalid product or quantity' ]);

        $results = Product::findById((int) $productId);

        if ( !$results || empty($results['product']) ) return Helper::error([ 'checkout' => "Product was not found." ]);

        $product = $results['product'];
        $unitPrice = (float) $results['price'];

        if ( $unitPrice !== (float) $item['price'] ) return Helper::error([ 'checkout' => 'Invalid or modified product price.' ]);

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
      $shippingFee = $subTotal >= 1000.00 ? 0.00 : 100.00;
      $totalAmount = round($subTotal + $shippingFee, 2);

      $subTotalValue = number_format($subTotal, 2, '.', '');
      $shippingValue = number_format($shippingFee, 2, '.', '');
      $totalValue = number_format($totalAmount, 2, '.', '');

      $paypalPayload = [
        'intent' => 'CAPTURE',
        'purchase_units' => [
          [
            'reference_id' => $referenceId,
            'custom_id' => (string) $userID,
            'amount' => [
              'currency_code' => 'USD',
              'value' => $totalValue,
              'breakdown' => [
                'item_total' => [
                  'currency_code' => 'USD',
                  'value' => $subTotalValue,
                ],
              'shipping' => [
                'currency_code' => 'USD',
                'value' => $shippingValue
              ]
              ],
            ],
            'items' => $paypalItems
          ],
        ],
      ];

      $paypalOrder = Paypal::createOrder($paypalPayload);

      if ( empty($paypalOrder) ) return Helper::error([ 'server' => 'PayPal did not return an order ID.' ], 502);

      $lastInsertedId = Order::insertOrder([
        ':uid' => $userID,
        ':refid' => $referenceId,
        ':merid' => $paypalOrder['id'],
        ':amount' => $subTotalValue,
        ':sfee' => $shippingValue,
        ':tamount' => $totalValue
      ]);

      if ((bool) $lastInsertedId) {
        foreach ($orderItems as $item) {
          Order::insertOrderItems([
            ':oid' => $lastInsertedId,
            ':pid' => $item['product_id'],
            ':quantity' => $item['quantity']
          ]);
        }
      }

      return Helper::success([
        'id' => $paypalOrder['id'],
        'status' => $paypalOrder['status'] ?? null,
      ]);


    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function take(): string
  {
    try {

      $input = Helper::initializeRequest();
      $merchantOrderId = $input['orders']['orderID'] ?? '';

      if ( !is_string($merchantOrderId) || $merchantOrderId === '' ) return Helper::error([ 'checkout' => 'PayPal order ID is required.' ]);

      $results = Paypal::captureOrder($merchantOrderId);
      $orders = Paypal::getOrder($merchantOrderId);

      $status = $results['status'];
      $referenceOrderId = $results['purchase_units'][0]['reference_id'];

      $orderId = Order::orderId([ ':refid' => $referenceOrderId, ':merid' => $merchantOrderId ]);

      $orderStatus = $status === 'COMPLETED' ? 'PAID' : $status;
      $orderItems = $orders['purchase_units'][0]['items'];
      $itemTotal = $orders['purchase_units'][0]['amount']['breakdown']['item_total']['value'];
      $shippingFee = $orders['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
      $totalAmount = $orders['purchase_units'][0]['amount']['value'];

      if ( $status === 'COMPLETED' ) {
        foreach($orderItems as $item) {
          $products = Product::findByName($item['name']);
          $remainingStock = $products['stock'] - (int) $item['quantity'];
          Product::updateStock([ ':stock' => $remainingStock, ':id' => $products['id'] ]);
        }
      }

      Order::updateStatus([
        ':id' => $orderId,
        ':status' => $orderStatus,
        ':refid' => $referenceOrderId,
        ':merid' => $merchantOrderId
      ]);

      Transaction::insert([
        ':oid' => $orderId,
        ':status' => $status,
        ':method' => $input['orders']['paymentSource']
      ]);

      $receipt = [
        'order_id' => $results['id'],
        'reference_id' => $referenceOrderId,
        'date' => Helper::formatDate($orders['create_time'], 'Asia/Singapore'),
        'items' => $orders['purchase_units'][0]['items'],
        'item_total' => $itemTotal,
        'shipping_fee' => $shippingFee,
        'total_amount' => $totalAmount,
        'status' => $status
      ];

      return Helper::success($receipt);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


}
