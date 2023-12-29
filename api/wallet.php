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
// if (empty($_POST['total_qty_sold'])) {
//     $response['success'] = false;
//     $response['message'] = "Total Qty Sold is Empty";
//     echo json_encode($response);
//     return;
// }

$user_id = $db->escapeString($_POST['user_id']);
$orders = $db->escapeString($_POST['orders']);
//$total_qty_sold = 100;

$total_qty_sold = isset($_POST['total_qty_sold']) ? $db->escapeString($_POST['total_qty_sold']) : 0;
$datetime = date('Y-m-d H:i:s');
$currentdate = date('Y-m-d');

$sql = "SELECT * FROM leaves WHERE date = '$currentdate' AND type = 'common_leave'";
$db->sql($sql);
$resl = $db->getResult();
$lnum = $db->numRows($resl);
$enable = 1;
if ($lnum >= 1) {
    $enable = 0;

}
if ($enable == 0) {
    $response['success'] = false;
    $response['message'] = "Holiday, Come Back Tomorrow";
    print_r(json_encode($response));
    return false;
}

$sql = "SELECT * FROM leaves WHERE date = '$currentdate' AND user_id = $user_id";
$db->sql($sql);
$resl = $db->getResult();
$lnum = $db->numRows($resl);
if ($lnum >= 1) {
    $response['success'] = false;
    $response['message'] = "You cannot work in Leave Day.";
    print_r(json_encode($response));
    return false;

}




$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $average_orders = $res[0]['average_orders'];
    $store_id = $res[0]['store_id'];
    $blocked = $res[0]['blocked'];
    $status = $res[0]['status'];
    $joined_date = $res[0]['joined_date'];

    $sql = "SELECT per_order_cost FROM `stores` WHERE id = $store_id";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $per_order_cost = $res[0]['per_order_cost'];

    }else{

        $response['success'] = false;
        $response['message'] = "Store not found";
        print_r(json_encode($response));
        return false;

    }

    if ($status == 0) {
        $response['success'] = false;
        $response['message'] = "Your Account is not Approved";
        print_r(json_encode($response));
        return false;
    }
    if ($blocked == 1) {
        $response['success'] = false;
        $response['message'] = "Your Account is Blocked";
        print_r(json_encode($response));
        return false;
    }

    if($joined_date > $currentdate){
        $response['success'] = false;
        $response['message'] = "Your Plan not Started";
        print_r(json_encode($response));
        return false;

    }
    if ($average_orders < 300) {
        $response['success'] = false;
        $response['message'] = "Your cannot sync because your average is very low";
        print_r(json_encode($response));
        return false;
    }
    $type = 'order_placed';
    $sync_limit = $average_orders / 100;

    if($sync_limit >= 5){
        $sync_limit = 5;
        

    }


    $sql = "SELECT COUNT(id) AS count  FROM transactions WHERE user_id = $user_id AND DATE(datetime) = '$currentdate' AND type = '$type'";
    $db->sql($sql);
    $tres = $db->getResult();
    $t_count = $tres[0]['count'];
    if ($t_count >= $sync_limit) {
        $response['success'] = false;
        $response['message'] = "You Reached Daily Sync Limit";
        print_r(json_encode($response));
        return false;
    }

    $sql = "SELECT sync_unique_id, datetime FROM transactions WHERE user_id = $user_id AND type = '$type' ORDER BY datetime DESC LIMIT 1 ";
    $db->sql($sql);
    $tres = $db->getResult();
    $num = $db->numRows($tres);
    $code_min_sync_time = 30; 
    $totalSeconds = 0;
    
    if ($num >= 1) {
        $t_sync_unique_id = $tres[0]['sync_unique_id'];
        $dt1 = $tres[0]['datetime'];
        $date1 = new DateTime($dt1);
        $date2 = new DateTime($datetime);
    
        $diff = $date1->diff($date2);
        $totalSeconds = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->days * 24 * 3600);
    
        $dfi = $code_min_sync_time - $totalSeconds;
    
        if ($totalSeconds < $code_min_sync_time) {
            $response['success'] = false;
            $response['message'] = "Cannot Sync Right Now, Try again after " . $dfi . " seconds";
            print_r(json_encode($response));
            return false;
        }
    }
    


    if($orders == '100'){
        $amount = $orders * $per_order_cost;

        $sql = "UPDATE users SET today_orders = today_orders + $orders, total_orders = total_orders + $orders, orders_earnings = orders_earnings + $amount WHERE id = $user_id";
        $db->sql($sql);
    
        $sql = "INSERT INTO transactions (`user_id`, `orders`, `amount`, `datetime`, `type`, `total_qty_sold`) VALUES ('$user_id', '$orders', '$amount', '$datetime', '$type', '$total_qty_sold')";
        $db->sql($sql);


        $message = "Order Placed successfully";

        
    
    }else{
        $message = "order not placed";

        
    
    }


    

}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
    print_r(json_encode($response));
    return false;
}





$sql = "SELECT * FROM users WHERE id = $user_id ";
$db->sql($sql);
$res = $db->getResult();
$response['success'] = true;
$response['message'] = $message;
$response['data'] = $res;
echo json_encode($response);
?>
