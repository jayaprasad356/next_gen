<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();


$type = 'order_placed';
$sql = "SELECT * FROM transactions  WHERE type = '$type' AND DATE(datetime) = '2024-01-02'";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);
if ($num >= 1){
    
    foreach ($res as $row) {
        $user_id = $row['user_id'];
        $user_id = - $row['orders'];
        $amount = $row['amount'];
        $total_orders = $row['total_orders'];
        $sql = "UPDATE users SET today_orders = today_orders + $orders, total_orders = total_orders + $orders, orders_earnings = orders_earnings + $amount WHERE id = $user_id";
        $db->sql($sql);
    
        $sql = "DELETE FROM transactions  WHERE type = '$type' AND DATE(datetime) = '2024-01-02'";
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