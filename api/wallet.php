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
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['orders'])) {
    $response['success'] = false;
    $response['message'] = "orders is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['sync_type'])) {
    $response['success'] = false;
    $response['message'] = "Sync Type is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['device_id'])) {
    $response['success'] = false;
    $response['message'] = "Device Id is Empty";
    print_r(json_encode($response));
    return false;
}


$currentdate = date('Y-m-d');
$user_id = $db->escapeString($_POST['user_id']);
$orders = $db->escapeString($_POST['orders']);
$device_id = $db->escapeString($_POST['device_id']);

$sync_type = $db->escapeString($_POST['sync_type']);
$datetime = date('Y-m-d H:i:s');
$type = 'watch_orders';
$ad_cost = $orders * 0.125;
$t_sync_unique_id = '';
$sql = "SELECT * FROM settings";
$db->sql($sql);
$settings = $db->getResult();
$watch_ad_status = $settings[0]['watch_ad_status'];

$sql = "SELECT * FROM leaves WHERE date = '$currentdate'";
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

if ( $watch_ad_status == 0) {
    $response['success'] = false;
    $response['message'] = "Watch Ad is disable right now";
    print_r(json_encode($response));
    return false;
} 
$sql = "SELECT id,reward_orders,device_id,referred_by,status,blocked FROM users WHERE id = $user_id ";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $reward_orders = $res[0]['reward_orders'];
    $user_device_id = $res[0]['device_id'];
    $referred_by = $res[0]['referred_by'];
    $status = $res[0]['status'];
    $blocked = $res[0]['blocked'];

    if ($status == 2) {
        $response['success'] = false;
        $response['message'] = "You are blocked";
        print_r(json_encode($response));
        return false;
    }
    if ($blocked == 1) {
        $response['success'] = false;
        $response['message'] = "Your Account is Blocked";
        print_r(json_encode($response));
        return false;
    }



    if($user_device_id == ''){
        $sql = "UPDATE users SET device_id = '$device_id'  WHERE id=" . $user_id;
        $db->sql($sql);

    }else{
        if ($user_device_id != $device_id) {
            $response['success'] = false;
            $response['message'] = "Device Verification Failed";
            print_r(json_encode($response));
            return false;
    
        }
    
    }



    if($sync_type == 'reward_sync'){

            
            if (empty($reward_orders)) {
                $response['success'] = false;
                $response['message'] = "Reward orders is Empty";
                print_r(json_encode($response));
                return false;
            }
            $type = 'reward_orders';
            $ad_cost = $reward_orders * 0.125;
            $orders  = $reward_orders;
            $sql = "INSERT INTO transactions (`user_id`,`orders`,`amount`,`datetime`,`type`)VALUES('$user_id','$orders','$ad_cost','$datetime','$type')";
            $db->sql($sql);
        
            $sql = "UPDATE users SET reward_orders = 0,today_orders = today_orders + $orders,total_orders = total_orders + $orders,balance = balance + $ad_cost,earn = earn + $ad_cost  WHERE id=" . $user_id;
            $db->sql($sql);
        



        
    }else {
        if (empty($_POST['sync_unique_id'])) {
            $response['success'] = false;
            $response['message'] = "Sync Unique Id is Empty";
            print_r(json_encode($response));
            return false;
        }
        $sync_unique_id = $db->escapeString($_POST['sync_unique_id']);
        $sql = "SELECT COUNT(id) AS count  FROM transactions WHERE user_id = $user_id AND DATE(datetime) = '$currentdate' AND type = '$type'";
        $db->sql($sql);
        $tres = $db->getResult();
        $t_count = $tres[0]['count'];
        if ($t_count >= 10) {
            $response['success'] = false;
            $response['message'] = "You Reached Daily Sync Limit";
            print_r(json_encode($response));
            return false;
        }
        
        $sql = "SELECT sync_unique_id,datetime FROM transactions WHERE user_id = $user_id AND type = '$type' ORDER BY datetime DESC LIMIT 1 ";
        $db->sql($sql);
        $tres = $db->getResult();
        $num = $db->numRows($tres);
        $code_min_sync_time = 45;
        if ($num >= 1) {
            $t_sync_unique_id = $tres[0]['sync_unique_id'];
            $dt1 = $tres[0]['datetime'];
            $date1 = new DateTime($dt1);
            $date2 = new DateTime($datetime);
        
            $diff = $date1->diff($date2);
            $totalMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
            $dfi = $code_min_sync_time - $totalMinutes;
            // if($totalMinutes < $code_min_sync_time ){
            //     $response['success'] = false;
            //     $response['message'] = "Cannot Sync Right Now, Try again after ".$dfi." mins";
            //     print_r(json_encode($response));
            //     return false;
        
            // }
        
        
        }
        if($orders > '120'){
            $orders = '120';
        }
        
        if($orders == '120'){
            // if(($sync_unique_id != $t_sync_unique_id) || $t_sync_unique_id == ''){


            //     $sql = "UPDATE users SET reward_orders = reward_orders + 12 WHERE refer_code = '$referred_by' AND status = 1 AND plan = 'A1' AND old_plan = 0";
            //     $db->sql($sql);


            //     $sql = "INSERT INTO transactions (`user_id`,`orders`,`amount`,`datetime`,`type`,`sync_unique_id`)VALUES('$user_id','$orders','$ad_cost','$datetime','$type','$sync_unique_id')";
            //     $db->sql($sql);
        
            //     $sql = "UPDATE users SET today_orders = today_orders + $orders,total_orders = total_orders + $orders,balance = balance + $ad_cost,earn = earn + $ad_cost WHERE id=" . $user_id;
            //     $db->sql($sql);
            
            
            // }else{
            //     $message= "you cannot sync without watching orders";
        
            //     // $sql = "INSERT INTO duplicate_sync (`user_id`,`orders`,`amount`,`datetime`,`type`,`sync_unique_id`)VALUES('$user_id','$orders','$ad_cost','$datetime','$type','$sync_unique_id')";
            //     // $db->sql($sql);
        
            // }

            $sql = "UPDATE users SET reward_orders = reward_orders + 12 WHERE refer_code = '$referred_by' AND status = 1 AND plan = 'A1' AND old_plan = 0";
            $db->sql($sql);


            $sql = "INSERT INTO transactions (`user_id`,`orders`,`amount`,`datetime`,`type`,`sync_unique_id`,`sync_time`)VALUES('$user_id','$orders','$ad_cost','$datetime','$type','$sync_unique_id',$totalMinutes)";
            $db->sql($sql);
    
            $sql = "UPDATE users SET t_sync_time = t_sync_time + $totalMinutes,t_sync = t_sync + 1,today_orders = today_orders + $orders,total_orders = total_orders + $orders,balance = balance + $ad_cost,earn = earn + $ad_cost WHERE id=" . $user_id;
            $db->sql($sql);
            if($totalMinutes < 40){
                $sql_query = "UPDATE users SET blocked = 1 WHERE id= $user_id";
                $db->sql($sql_query);

            }

            
        
        }else{
            $message= "you cannot sync about 120 orders";
        
        }

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
$response['message'] = "Sync updated successfully";
$response['data'] = $res;
echo json_encode($response);


?>