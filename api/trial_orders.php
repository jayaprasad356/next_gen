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
    $response['message'] = "User ID is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['type'])) {
    $response['success'] = false;
    $response['message'] = "Type is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['device_id'])) {
    $response['success'] = false;
    $response['message'] = "Device Id is Empty";
    print_r(json_encode($response));
    return false;
}


$user_id = $db->escapeString($_POST['user_id']);
$type = $db->escapeString($_POST['type']);
$device_id = $db->escapeString($_POST['device_id']);

$datetime = date('Y-m-d H:i:s');
$currentdate = date('Y-m-d');

$sql = "SELECT *,DATEDIFF( '$currentdate',joined_date) AS history_days FROM users WHERE id = $user_id";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1){

    $watch_orders = $res[0]['watch_orders'];
    $user_device_id = $res[0]['device_id'];
    $balance = $res[0]['balance'];
    $level = $res[0]['level'];
    $orders_cost = $res[0]['orders_cost'];
    $status = $res[0]['status'];
    $history_days = $res[0]['history_days'] + 1;

    if($type == 'watch_ad'){
        if($user_device_id == ''){
            $sql_query = "UPDATE users SET device_id = '$device_id' WHERE id = $user_id";
            $db->sql($sql_query);
        
        }

        if($user_device_id != $device_id){
            $response['success'] = false;
            $response['message'] = "Device Verification Failed,Please Login with your device";
            print_r(json_encode($response));
            return false;
        
        }

        if ($watch_orders == '0') {
            $response['success'] = false;
            $response['message'] = "Watch Ad Currenly Disabled";
            print_r(json_encode($response));
            return false;
        }
        if($level == '0' && $history_days > 7){
            $response['success'] = false;
            $response['message'] = "Your Trial Period Expired,Please Purchase Database and start work";
            print_r(json_encode($response));
            return false;

        }
        $sql = "SELECT trial_limit,level1_limit,level2_limit FROM settings LIMIT 1";
        $db->sql($sql);
        $res = $db->getResult();
        $trial_limit = $res[0]['trial_limit'];
        $level1_limit = $res[0]['level1_limit'];
        $level2_limit = $res[0]['level2_limit'];

        $sql = "SELECT SUM(ad_count) AS total FROM orders_trans WHERE user_id = $user_id AND DATE(start_time) = '$currentdate'";
        $db->sql($sql);
        $res = $db->getResult();
        $daily_limit = $res[0]['total'];

        $sql = "SELECT SUM(ad_count) AS total FROM orders_trans WHERE user_id = $user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $trial_daily_limit = $res[0]['total'];
        if ($status == 0 && $trial_daily_limit >= $trial_limit){
            $response['success'] = false;
            $response['message'] = "You Completed Free Trial,Purchase Server then continue work.";
            print_r(json_encode($response));
            return false;

        }
        else if ($level == 1 && $daily_limit >= $level1_limit){
            $response['success'] = false;
            $response['message'] = "Congrats,You Viewed All orders Today.";
            print_r(json_encode($response));
            return false;

        }
        else if ($level == 2 && $daily_limit >= $level2_limit){
            $response['success'] = false;
            $response['message'] = "Congrats,You Viewed All orders Today.";
            print_r(json_encode($response));
            return false;

        }
        $sql = "SELECT * FROM orders_trans WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num >= 1){
            
            $end_time = $res[0]['end_time'];
        
            if($datetime < $end_time){
                $response['success'] = false;
                $response['message'] = "Pls wait for next Ad....";
                print_r(json_encode($response));
                return false;

    
            }
            
    
        }
        $endtime = date('Y-m-d H:i:s', strtotime($datetime) + 20);

        if($status == 1){
            $sql = "UPDATE users SET total_orders_viewed = total_orders_viewed + 1,balance = balance + orders_cost,earn = earn + orders_cost WHERE id = " . $user_id;
            $db->sql($sql);

        }else{
            $sql = "UPDATE users SET total_orders_viewed = total_orders_viewed + 1,balance = balance + orders_cost WHERE id = " . $user_id;
            $db->sql($sql);
        }



        if($status == 0 || $level == 1){
            $ad_count = 1;

        }else if($level == 2){
            $ad_count = 2;

        }else{
            $ad_count = 4;

        }
    
        $sql = "INSERT INTO orders_trans (`user_id`,`ad_count`,`start_time`,`end_time`) VALUES ($user_id,$ad_count,'$datetime','$endtime')";
        $db->sql($sql);
        $time_start = 1;
        
        $sql = "SELECT SUM(ad_count) AS total_orders FROM orders_trans WHERE user_id = $user_id AND DATE(start_time) = '$currentdate'";
        $db->sql($sql);
        $res = $db->getResult();
        if($status == 0){
            $sql = "SELECT SUM(ad_count) AS total_orders FROM orders_trans WHERE user_id = $user_id";
            $db->sql($sql);
            $res = $db->getResult();
            $today_orders_remain = $trial_limit - $res[0]['total_orders'];

        }else if($status == 1){
            $today_orders_remain = $level1_limit - $res[0]['total_orders'];

        }else{
            $today_orders_remain = $level2_limit - $res[0]['total_orders'];
        }
       

        $sql = "SELECT * FROM users WHERE id = $user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $balance = $res[0]['balance'];

        $sql = "SELECT * FROM `orders`ORDER BY RAND() LIMIT 1";
        $db->sql($sql);
        $res = $db->getResult();
        $rows = array();
        $image = DOMAIN_URL . $res[0]['image'];
    
    

        $response['success'] = true;
        $response['message'] = "orders Started";
        $response['today_orders_remain'] = $today_orders_remain;
        $response['time_start'] = $time_start;
        $response['history_days'] = $history_days;
        $response['status'] = $status;
        $response['time_left'] = 30;
        $response['refer_amount'] = 150;
        $response['watch_orders'] = $watch_orders;
        $response['level'] = $level;
        $response['balance'] = $balance;
        $response['orders_image'] = $image;
        print_r(json_encode($response));
    
    }
    else{
        
        $sql = "SELECT * FROM orders_trans WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num >= 1){
            $end_time = $res[0]['end_time'];

            $start_datetime = new DateTime($datetime);
            $end_datetime = new DateTime($end_time);
            $interval = $start_datetime->diff($end_datetime);
            $seconds_difference = $interval->s;
            if($datetime > $end_time){
                $time_left = 30;
                $time_start = 0;

            }else{
                $time_left = $interval->s + $interval->i * 60 + $interval->h * 3600;
                $time_start = 1;
            }
            

            
        }else{
            
            $time_start = 0;
            
        }


        $sql = "SELECT trial_limit,level1_limit,level2_limit FROM settings LIMIT 1";
        $db->sql($sql);
        $res = $db->getResult();
        $trial_limit = $res[0]['trial_limit'];
        $level1_limit = $res[0]['level1_limit'];
        $level2_limit = $res[0]['level2_limit'];

        $sql = "SELECT SUM(order_count) AS total_orders FROM orders_trans WHERE user_id = $user_id AND DATE(start_time) = '$currentdate'";
        $db->sql($sql);
        $res = $db->getResult();
        if($status == 0){
            $sql = "SELECT SUM(order_count) AS total_orders FROM orders_trans WHERE user_id = $user_id";
            $db->sql($sql);
            $res = $db->getResult();
            $today_orders_remain = $trial_limit - $res[0]['total_orders'];

        }else if($status == 1){
            $today_orders_remain = $level1_limit - $res[0]['total_orders'];

        }else{
            $today_orders_remain = $level2_limit - $res[0]['total_orders'];
        }
       

        $sql = "SELECT COUNT(id) AS total_orders FROM orders_trans WHERE id = $user_id";
        $db->sql($sql);

        $sql = "SELECT * FROM `orders`ORDER BY RAND() LIMIT 1";
        $db->sql($sql);
        $res = $db->getResult();
        $rows = array();
        $image = DOMAIN_URL . $res[0]['image'];


        

        $response['success'] = true;
        $response['message'] = "orders Status";
        $response['today_orders_remain'] = $today_orders_remain;
        $response['time_start'] = $time_start;
        $response['time_left'] = $time_left;
        $response['refer_amount'] = 150;
        $response['level'] = $level;
        $response['status'] = $status;
        $response['history_days'] = $history_days;
        $response['watch_orders'] = $watch_orders;
        $response['balance'] = $balance;
        $response['orders_image'] = $image;
        print_r(json_encode($response));

    }


}
else{
    $response['success'] = false;
    $response['message'] = "User Not Exist";
    print_r(json_encode($response));

}
function isTimeBetweenMorningAndEvening($dateTimeStr) {
    $morningStart = new DateTime('08:00:00');
    $eveningEnd = new DateTime('18:00:00');

    // Convert the input datetime string to a DateTime object
    $dateTime = new DateTime($dateTimeStr);

    // Get the time component of the DateTime object
    $timeOfDay = $dateTime->format('H:i:s');

    // Compare with the morning and evening time ranges
    return ($timeOfDay >= $morningStart->format('H:i:s')) && ($timeOfDay <= $eveningEnd->format('H:i:s'));
}

?>