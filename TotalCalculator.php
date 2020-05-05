<?php
namespace BearClaw\Warehousing;

class TotalsCalculator
{
  /**
   * @param array $ids
   */
  public function generateReport(array $ids) {
    $service = new PurchaseOrderService();
    $result = $service->calculateTotals($ids);
    foreach($result as $record) {
      echo "Product Type " . $record['product_type_id'] . " has total of " . $record['total'] . "\n";
    }
  }
}
