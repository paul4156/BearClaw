<?php
namespace BearClaw\Warehousing;

/**
 * Not in use
 */
class Requester {

  public function requests($ids) {
    $products = [];
    foreach ($ids as $id) {
      $product = $this->request($id);
      $product = json_decode($product, true)['data'];
      $products[] = $product;
    }

    return $products;
  }

  /**
   * @params $id int
   *
   * @returns array
   */
  public function request($id) {
    $ch = curl_init("https://api.cartoncloud.com.au/CartonCloud_Demo/PurchaseOrders/$id?version=5&associated=true");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERPWD, 'interview-test@cartoncloud.com.au' . ":" . 'test123456');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
  }
}
