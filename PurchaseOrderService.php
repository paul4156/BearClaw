<?php
namespace BearClaw\Warehousing;

require_once(__DIR__ . '/Requester.php');

class PurchaseOrderService
{
  /**
   * I am not a fan of magic routing / class loading.
   * Although having a map introduced one more step when adding a new product type,
   * it makes the code more predictable.
   */
  protected $map = [
    1 => 'totalByWeight',
    2 => 'totalByVolume',
    3 => 'totalByWeight',
  ];

  /**
   * Flatten the order products to a single level array
   */
  protected function getOrderProducts(array $orders) {
    $orderProducts = [];
    foreach ($orders as $order) {
      foreach ($order['PurchaseOrderProduct'] as $orderProduct) {
        $id = $orderProduct['id'];
        $orderProducts[$id] = $orderProduct;
      }
    }

    return $orderProducts;
  }

  /**
   * Calculate totals for different product types
   *
   * @params $ids array the order ids
   *
   * @returns array of different product types and the totals
   */
  public function calculateTotals(array $ids) {
    // phpApiCall is synchronous calls which are slow. The order of purchase orders is kept.
    // $orders = $this->phpApiCall($ids);
    // jsApiCall is asynchronous calls for all ids. The order of purchase orders is not guaranteed.
    $orders = $this->jsApiCall($ids);

    $orderProducts = $this->getOrderProducts($orders);

    $totals = [];

    foreach ($orderProducts as $product) {
      $productTypeId = $product['product_type_id'];
      $method = $this->map[$productTypeId];
      $total = $this->{$method}($product);

      if (!isset($totals[$productTypeId])) {
        $totals[$productTypeId] = [
          'product_type_id' => $productTypeId,
          'total' => 0,
        ];
      }

      $totals[$productTypeId]['total'] += $total;
    }

    return $totals;
  }

  /**
   * Not in use
   * Synchronous api calls to get purchase orders
   */
  protected function phpApiCall($ids) {
    return (new Requester())->requests($ids);
  }

  /**
   * In use
   * Asynchronous api calls to get purchase orders
   *
   * php calls nodejs with id string, eg '[1234,2345,3456]'
   * nodejs makes async calls per id and accumulate all responses into an array
   * nodejs code then writes the response as a json string to console
   * php code picks it up and parse
   */
  protected function jsApiCall($ids) {
    $idString = json_encode($ids);
    exec("node requester.js '$idString'", $output);
    return json_decode($output[0], true);
  }

  /**
   * One way to calculate total for a product
   */
  protected function totalByWeight(array $product) {
    return $product['unit_quantity_initial'] * $product['Product']['weight'];
  }

  /**
   * The other way to calculate total for a product
   */
  protected function totalByVolume(array $product) {
    return $product['unit_quantity_initial'] * $product['Product']['volume'];
  }

  // More ways can be added to calculate total for a product
  // Remember to add to $map
  // If it becomes more complex, I will create an interface and have each calculation as a class.
}
