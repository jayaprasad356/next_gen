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
$user_id=$db->escapeString($_POST['user_id']);
$wallet_type = $db->escapeString($_POST['wallet_type']);

$sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);


if ($num == 1) {
    $basic_wallet = $res[0]['basic_wallet'];
    $premium_wallet = $res[0]['premium_wallet'];
    $current_refers = $res[0]['current_refers'];
    $today_orders = $res[0]['today_orders'];
    $target_refers = $res[0]['target_refers'];
    $status = $res[0]['status'];
    $plan = $res[0]['plan'];
    $media_wallet = $res[0]['media_wallet']; 
    $old_plan = $res[0]['old_plan']; 
    $hiring_earnings = $res[0]['hiring_earnings']; 
    $orders_earnings = $res[0]['orders_earnings'];

    if($wallet_type == 'basic_wallet'){
        $min_basic_withdrawal = 30;
        if($status == 0){
            $min_basic_withdrawal = 60;

        }
        if($plan == 'A2'){
            $min_basic_withdrawal = 20;

        }
        if($plan == 'A1' && $status == 1){
            $sql = "SELECT * FROM monthly_target WHERE user_id = $user_id AND status = 0";
            $db->sql($sql);
            $res = $db->getResult();
            $num = $db->numRows($res);
            if ($num >= 1){
                $response['success'] = false;
                $response['message'] = "Complete Last Month Target";
                print_r(json_encode($response));
                return false;

            }
        }
        if ($basic_wallet < $min_basic_withdrawal) {
            $response['success'] = false;
            $response['message'] = "Minimum ₹".$min_basic_withdrawal." to add balance";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'basic_wallet','$datetime',$basic_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + $basic_wallet,earn = earn + $basic_wallet,basic_wallet = basic_wallet - $basic_wallet WHERE id=" . $user_id;
        $db->sql($sql);


    }
    if($wallet_type == 'premium_wallet'){
        if ($plan == 'A1' && $old_plan == 0) {
            $response['success'] = false;
            $response['message'] = "Disabled";
            print_r(json_encode($response));
            return false;
        }
        if ($current_refers < $target_refers && $plan == 'A1') {
            $response['success'] = false;
            $response['message'] = "Minimum ".$target_refers." refers to add balance";
            print_r(json_encode($response));
            return false;
        }
        if ($premium_wallet < 120  && $plan == 'A1') {
            $response['success'] = false;
            $response['message'] = "Minimum ₹120 to add balance";
            print_r(json_encode($response));
            return false;
        }
        if($plan == 'A2'){
            $premium_wallet = 700;
            $sql_query = "SELECT * FROM `premium_refer_bonus` WHERE user_id = $user_id AND status = 0";
            $db->sql($sql_query);
            $res = $db->getResult();
            $num = $db->numRows($res);

            if($num>=1){
                if ($premium_wallet < 700 ) {
                    $response['success'] = false;
                    $response['message'] = "Minimum ₹700 to add balance";
                    print_r(json_encode($response));
                    return false;
                }
                $premium_id = $res[0]['id'];
                $sql = "UPDATE premium_refer_bonus SET status= 1 WHERE id=" . $premium_id;
                $db->sql($sql);
    
            }else{
                $response['success'] = false;
                $response['message'] = "Refer 1 Person to get ₹700";
                print_r(json_encode($response));
                return false;

            }

        }

        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'premium_wallet','$datetime',$premium_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + $premium_wallet,earn = earn + $premium_wallet,premium_wallet = premium_wallet - $premium_wallet WHERE id=" . $user_id;
        $db->sql($sql);
    
    }
    if($wallet_type == 'media_wallet'){
        $min_withdrawal = 2000;
        if ($media_wallet < $min_withdrawal) {
            $response['success'] = false;
            $response['message'] = "Minimum ₹".$min_withdrawal." to add balance";
            print_r(json_encode($response));
            return false;
        }

        $response['success'] = false;
        $response['message'] = "Disabled";
        print_r(json_encode($response));
        return false;
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'media_wallet','$datetime',$media_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + $media_wallet,earn = earn + $media_wallet,media_wallet = media_wallet - $media_wallet WHERE id=" . $user_id;
        $db->sql($sql);


    }
    if($wallet_type == 'hiring_earnings'){
        if ($hiring_earnings <= 0) {
            $response['success'] = false;
            $response['message'] = "Hiring earnings is Low";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'hiring_earnings','$datetime',$hiring_earnings)";
        $db->sql($sql);
        $sql = "UPDATE users SET hiring_earnings= hiring_earnings + $hiring_earnings,earn = earn + $hiring_earnings  WHERE id=" . $user_id;
        $db->sql($sql);
    }
    if($wallet_type == 'orders_earnings'){
        if ($orders_earnings <= 0) {
            $response['success'] = false;
            $response['message'] = "Orders earnings is Low";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'orders_earnings','$datetime',$orders_earnings)";
        $db->sql($sql);
        $sql = "UPDATE users SET orders_earnings= orders_earnings + $orders_earnings,earn = earn + $orders_earnings  WHERE id=" . $user_id;
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
