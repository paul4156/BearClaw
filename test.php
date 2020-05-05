<?php
require_once(__DIR__ . '/PurchaseOrderService.php');
require_once(__DIR__ . '/Requester.php');

class TestPurchaseOrderService extends \BearClaw\Warehousing\PurchaseOrderService {
  protected function jsApiCall($ids) {
    return [
      [
        'PurchaseOrderProduct' => [
          [
            'id' => 1,
            'product_type_id' => '1',
            'unit_quantity_initial' => 2,
            'Product' => [
              'weight' => 1,
            ],
          ],
        ],
      ],
    ];
  }
}

$result = (new TestPurchaseOrderService())->calculateTotals([2344, 2345, 2346]);

function diyAssert($actual, $expected) {
  if ($actual !== $expected) {
    print('E');
    return;
  }
  print('.');
}

diyAssert(is_array($result), true);
diyAssert(count($result), 1);
diyAssert(count($result[1]), 2);
diyAssert($result[1]['product_type_id'], '1');
diyAssert($result[1]['total'], 2);
