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
include_once('../includes/functions.php');
$fnc = new functions;
$db = new Database();
$db->connect();
$currentdate = date('Y-m-d');


$sql = "SELECT SUM(orders) AS today_orders,user_id FROM `transactions` WHERE DATE(datetime) = '2024-01-02' GROUP BY user_id";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);
if ($num >= 1){
    
    foreach ($res as $row) {
        $user_id = $row['user_id'];
        $today_orders = $row['today_orders'];
        $sql = "UPDATE users SET today_orders = '$today_orders' WHERE id = $user_id";
        $db->sql($sql);

    

    }
    $response['success'] = true;
    $response['message'] = "updated Successfully";
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Users Not found";
    print_r(json_encode($response));

}
?>