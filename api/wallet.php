<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');
include_once('../includes/crud.php');

$db = new Database();
$db->connect();

$response = [];

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    echo json_encode($response);
    return;
}

if (empty($_POST['orders'])) {
    $response['success'] = false;
    $response['message'] = "Orders is Empty";
    echo json_encode($response);
    return;
}

$user_id = $db->escapeString($_POST['user_id']);
$orders = $db->escapeString($_POST['orders']);
$datetime = date('Y-m-d H:i:s');

$sql = "SELECT average_orders FROM users WHERE id = $user_id";
$db->sql($sql);
$res = $db->getResult();

$average_orders = $res[0]['average_orders'];

if ($average_orders < 500) {
    $response['success'] = false;
    $response['message'] = "Daily order limit reached for the user";
    echo json_encode($response);
    return;
}

$sql = "SELECT store_id FROM users WHERE id = $user_id";
$db->sql($sql);
$res = $db->getResult();

$store_id = $res[0]['store_id'];

$sql = "SELECT per_order_cost FROM stores WHERE id = $store_id";
$db->sql($sql);
$res = $db->getResult();

$per_order_cost = $res[0]['per_order_cost'];

$amount = $orders * $per_order_cost;

$sql = "UPDATE users SET today_orders = today_orders + $orders, total_orders = total_orders + $orders, orders_earnings = orders_earnings + $amount, average_orders = orders_earnings / total_orders WHERE id = $user_id";
$db->sql($sql);

$sql = "INSERT INTO transactions (`user_id`,`orders`,`amount`,`datetime`,`type`)VALUES('$user_id','$orders','$amount','$datetime','order_placed')";
$db->sql($sql);

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$res = $db->getResult();

if (!$res) {
    $response['success'] = false;
    $response['message'] = "User not found with the specified user_id";
    echo json_encode($response);
    return;
}

$response['success'] = true;
$response['message'] = "Orders added Successfully";
$response['data'] = $res;
echo json_encode($response);
?>
