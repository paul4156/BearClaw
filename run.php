<?php
require_once(__DIR__ . '/TotalCalculator.php');
require_once(__DIR__ . '/PurchaseOrderService.php');
require_once(__DIR__ . '/Requester.php');

(new \BearClaw\Warehousing\TotalsCalculator())->generateReport([2344, 2345, 2346]);
