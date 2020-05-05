<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// auth
if (!isset($_SERVER['PHP_AUTH_USER'])
  || $_SERVER['PHP_AUTH_USER'] !== 'interview-test@cartoncloud.com.au'
  || !isset($_SERVER['PHP_AUTH_PW'])
  || $_SERVER['PHP_AUTH_PW'] !== 'test123456'
) {
  header('HTTP/1.1 401 Unauthorized');
  exit();
}

// all of our endpoints start with /person
// everything else results in a 404 Not Found
if ($uri[1] !== 'test') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (!isset($_REQUEST['purchase_order_ids'])
  || empty($_REQUEST['purchase_order_ids'])
) {
  header("HTTP/1.1 400 Bad Request");
  exit();
}

$ids = json_decode($_REQUEST['purchase_order_ids'], true);

require_once(__DIR__ . '/PurchaseOrderService.php');
require_once(__DIR__ . '/Requester.php');
try {
  $service = new \BearClaw\Warehousing\PurchaseOrderService();
  $result = $service->calculateTotals($ids);
} catch(\Exception $e) {
  header('HTTP/1.1 500 Internal Server Error');
  exit();
}

header('HTTP/1.1 200 OK');
echo json_encode($result);
