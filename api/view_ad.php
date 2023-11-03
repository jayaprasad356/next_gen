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


if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = " User Id is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$date = date('Y-m-d');
$datetime = date('Y-m-d H:i:s');
$dayOfWeek = date('w', strtotime($datetime));
$sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
$sql = "SELECT * FROM leaves WHERE date = '$date'";
$db->sql($sql);
$resl = $db->getResult();
$lnum = $db->numRows($resl);
$enable = 1;
if ($lnum >= 1) {
    $enable = 0;

}
if ($num == 1) {
    $status = $res[0]['status'];
    $plan = $res[0]['plan'];
    $old_plan = $res[0]['old_plan'];
    $total_orders = $res[0]['total_orders'];
    $today_orders = $res[0]['today_orders'];
    $total_referrals = $res[0]['total_referrals'];
    $worked_days = $res[0]['worked_days'];
    $blocked = $res[0]['blocked'];


    $orders_limit = 10;

    $sql = "SELECT * FROM settings";
    $db->sql($sql);
    $settings = $db->getResult();
    $watch_ad_status = $settings[0]['watch_ad_status'];

    if ($blocked == 1) {
        $response['success'] = false;
        $response['message'] = "Your Account is Blocked";
        print_r(json_encode($response));
        return false;
    }

    if ($watch_ad_status == 0) {
        $response['success'] = false;
        $response['message'] = "Watch Ad is disable right now";
        print_r(json_encode($response));
        return false;
    } 
    if ($plan == 'A1' && $old_plan == 0 && $status == 1) {
        $response['success'] = false;
        $response['message'] = "Disabled";
        print_r(json_encode($response));
        return false;
    } 



    if ($enable == 0 && $status == 1) {
        $response['success'] = false;
        $response['message'] = "Holiday,Come Back Tomorrow";
        print_r(json_encode($response));
        return false;
    } 
  
   
    if ($today_orders >= $orders_limit) {
        $response['success'] = false;
        $response['message'] = "You Completed today orders";
        print_r(json_encode($response));
        return false;
    }
    if ($status == 1 && $total_orders >= 3600 && $plan == 'A2') {
        $response['success'] = false;
        $response['message'] = "You Completed total orders";
        print_r(json_encode($response));
        return false;
    }
    if ($status == 1 && $worked_days >= 30 && $plan == 'A1' && $old_plan == 1 && $total_referrals < 3) {
        $response['success'] = false;
        $response['message'] = "orders not available now,if u want to continue ask customer support then shift to new A1 plan";
        print_r(json_encode($response));
        return false;
    }
    $time_left = 0;
    $sql = "SELECT * FROM orders_trans WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1){
        
        $end_time = $res[0]['end_time'];

        $start_datetime = new DateTime($datetime);
        $end_datetime = new DateTime($end_time);
        $diff = $start_datetime->diff($end_datetime);
        
    
        if($datetime < $end_time){
            $time_left = $diff->s + ($diff->i * 60) + ($diff->h * 3600) + ($diff->days * 86400);
            $response['success'] = false;
            $response['message'] = "Pls wait for next Ad....";
            $response['time_left'] = $time_left;
            print_r(json_encode($response));
            return false;


        }
        

    }

    $join = "";
    if($status == 1 &&  $plan == 'A1'){
        $join = ",premium_wallet = premium_wallet + 12";
    }
    if($status == 1 &&  $plan == 'A2'){
        $join = ",premium_wallet = premium_wallet + 18";

    }

    if($status == 0){
        $ad_cost = 0.20;

    }else{
        if($plan == 'A1'){
            $ad_cost = 3;
    
        }else{
            $ad_cost = 2;
    
    
        }


    }


 

    $endtime = date('Y-m-d H:i:s', strtotime($datetime) + 60);
    $sql = "INSERT INTO orders_trans (`user_id`,`ad_count`,`start_time`,`end_time`) VALUES ($user_id,1,'$datetime','$endtime')";
    $db->sql($sql);

    $sql = "UPDATE users SET today_orders = today_orders + 1,total_orders = total_orders + 1,basic_wallet = basic_wallet + $ad_cost $join WHERE id=" . $user_id;
    $db->sql($sql);
    $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Ad is Started";
    $response['time_left'] = $time_left;
    $response['data'] = $res;

}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
}

print_r(json_encode($response));




?>
