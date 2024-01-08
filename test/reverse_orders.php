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
$datetime = date('Y-m-d H:i:s');

$type = 'order_placed';
$sql = "SELECT t.* FROM `users`u,`transactions`t WHERE u.id = t.user_id AND DATE(t.datetime) = '2024-01-08' AND u.average_orders < 400 AND u.id = 1622 AND t.type= 'orders_earnings'";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);
if ($num >= 1){
    
    foreach ($res as $row) {
        $id = $row['id'];
        $user_id = $row['user_id'];
        $orders_earnings = - $row['amount'];

    
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'orders_earnings','$datetime',$orders_earnings)";
        $db->sql($sql);
        $sql = "UPDATE users SET orders_earnings= orders_earnings - $orders_earnings,earn = earn + $orders_earnings,balance = balance + $orders_earnings  WHERE id=" . $user_id;
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