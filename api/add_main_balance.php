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
if (empty($_POST['wallet_type'])) {
    $response['success'] = false;
    $response['message'] = " Wallet Type is Empty";
    print_r(json_encode($response));
    return false;
}
$datetime = date('Y-m-d H:i:s');
$currentDate = date('Y-m-d');
$firstDateOfMonth = date('Y-m-01', strtotime('today'));
$user_id=$db->escapeString($_POST['user_id']);
$wallet_type = $db->escapeString($_POST['wallet_type']);

$sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);


if ($num == 1) {
    $today_orders = $res[0]['today_orders'];
    $status = $res[0]['status'];
    $hiring_earings = $res[0]['hiring_earings']; 
    $orders_earnings = $res[0]['orders_earnings'];
    $average_orders = $res[0]['average_orders'];
    $student_plan = $res[0]['student_plan'];
    $days_60_plan = $res[0]['days_60_plan'];
    $total_referrals = $res[0]['total_referrals'];

    if($wallet_type == 'hiring_earnings'){
        if ($hiring_earings < 100) {
            $response['success'] = false;
            $response['message'] = "Minimum 100 rs to add";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'hiring_earings','$datetime',$hiring_earings)";
        $db->sql($sql);
        $sql = "UPDATE users SET hiring_earings= hiring_earings - $hiring_earings,earn = earn + $hiring_earings,balance = balance + $hiring_earings  WHERE id=" . $user_id;
        $db->sql($sql);
    }
    if($wallet_type == 'orders_earnings'){


        if ($orders_earnings < 100) {
            $response['success'] = false;
            $response['message'] = "Minimum 100 rs to add";
            print_r(json_encode($response));
            return false;
        }
        if($student_plan == 0){
            $today = date("N");
            if($average_orders >= 400 && $average_orders < 500 && $today != 1){
                $response['success'] = false;
                $response['message'] = "Enable on Monday only";
                print_r(json_encode($response));
                return false;
    
            }
            if ($average_orders >= 300 && $average_orders < 400 && $currentDate != $firstDateOfMonth) {
                $response['success'] = false;
                $response['message'] = "Enable on First date of the month";
                print_r(json_encode($response));
                return false;
            }
    
    
            if($average_orders < 300 ){
                $response['success'] = false;
                $response['message'] = "Disabled";
                print_r(json_encode($response));
                return false;
    
            }
        }
        if ($days_60_plan == 1) {
            if ($total_referrals >= 0 && $total_referrals <= 3 && $currentDate != $firstDateOfMonth) {
                $response['success'] = false;
                $response['message'] = "Enable on First date of the month";
                print_r(json_encode($response));
                return false;
            } 
            if ($total_referrals >= 4 && $total_referrals <= 6) {
                $response['success'] = false;
                $response['message'] = "Enable on Monday only";
                print_r(json_encode($response));
                return false;
    
            } 
        }
        
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'orders_earnings','$datetime',$orders_earnings)";
        $db->sql($sql);
        $sql = "UPDATE users SET orders_earnings= orders_earnings - $orders_earnings,earn = earn + $orders_earnings,balance = balance + $orders_earnings  WHERE id=" . $user_id;
        $db->sql($sql);
    }



    $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Added to Main Balance Successfully";
    $response['data'] = $res;



}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
}

print_r(json_encode($response));




?>
