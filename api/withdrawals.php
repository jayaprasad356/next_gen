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
include_once('../includes/custom-functions.php');
$fn = new custom_functions;
$db = new Database();
$db->connect();

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User ID is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['amount'])) {
    $response['success'] = false;
    $response['message'] = "Amount is Empty";
    print_r(json_encode($response));
    return false;
}
$date = date('Y-m-d');
function isBetween10AMand6PM() {
    $currentHour = date('H');
    $startTimestamp = strtotime('10:00:00');
    $endTimestamp = strtotime('18:00:00');
    return ($currentHour >= date('H', $startTimestamp)) && ($currentHour < date('H', $endTimestamp));
}



$user_id = $db->escapeString($_POST['user_id']);
$amount = $db->escapeString($_POST['amount']);
$datetime = date('Y-m-d H:i:s');
$dayOfWeek = date('w', strtotime($datetime));
$sql = "SELECT * FROM leaves WHERE date = '$date'";
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

$sql = "SELECT * FROM settings";
$db->sql($sql);
$settings = $db->getResult();


$sql = "SELECT * FROM settings WHERE id=1";
$db->sql($sql);
$result = $db->getResult();
$min_withdrawal = $result[0]['min_withdrawal'];

$sql = "SELECT * FROM users WHERE id='$user_id'";
$db->sql($sql);
$res = $db->getResult();
$balance = $res[0]['balance'];
$account_num = $res[0]['account_num'];
$earn = $res[0]['earn'];
$min_withdrawal = $res[0]['min_withdrawal'];
$withdrawal_status = $res[0]['withdrawal_status'];
$status = $res[0]['status'];
$total_ads = $res[0]['total_ads'];
$worked_days = $res[0]['worked_days'];
$total_referrals = $res[0]['total_referrals'];
$missed_days = $res[0]['missed_days'];
$old_plan = $res[0]['old_plan'];
$plan = $res[0]['plan'];
$blocked = $res[0]['blocked'];
$ads_10th_day = $res[0]['ads_10th_day'];
$performance = $res[0]['performance'];
$project_type = $res[0]['project_type'];
$joined_date = $res[0]['joined_date'];
$target_ads = 12000;
$percentage = 70;
$result = 8400;
if ($blocked == 1) {
    $response['success'] = false;
    $response['message'] = "Your Account is Blocked";
    print_r(json_encode($response));
    return false;
}

// if ($plan == 'A1' && $project_type == 'free' && $performance < 100) {
//     $refer_bonus = 1200 * $refer_target;
//     $response['success'] = false;
//     $response['message'] = "You missed to Complete daily target So refer 5 person to withdrawal";
//     print_r(json_encode($response));
//     return false;
// }


// $refer_target = $fn->get_my_refer_target($user_id);
// if ($plan == 'A1' && $performance < 100 && $refer_target > 0) {
//     $refer_bonus = 1200 * $refer_target;
//     $response['success'] = false;
//     $response['message'] = "You missed to Complete daily target So give work ".$refer_target." person get ".$refer_bonus." ads to withdrawal";
//     print_r(json_encode($response));
//     return false;
// }
// if ($plan == 'A1' && $performance < 100 && $worked_days >= 6 ) {
//     $target_ads = ($worked_days + 1 ) * 1200;
//     $c_ads = $target_ads - $total_ads;

//     $response['success'] = false;
//     $response['message'] = "You missed to Watch ".$c_ads;
//     print_r(json_encode($response));
//     return false;
// }


if ($plan == 'A2' && $performance < 100 ) {
    $target_ads = ($worked_days + 1 ) * 10;
    $c_ads = $target_ads - $total_ads;
    $response['success'] = false;
    $response['message'] = "You missed to Watch ".$c_ads." Ads.So refer to A1 plan get 10 ads extra";
    print_r(json_encode($response));
    return false;
}

// if ($ads_10th_day < $result && $total_referrals == 0 && $worked_days >= 10 && $plan == 'A1') {
//     $response['success'] = false;
//     $response['message'] = "You missed to Complete 70% work in 10 days So refer 1 person get 1200 ads to withdrawal";
//     print_r(json_encode($response));
//     return false;
// }

if ($withdrawal_status == '0') {
    $response['success'] = false;
    $response['message'] = "Withdrawals are currently disabled for your account.";
    print_r(json_encode($response));
    return false;
}
if($total_referrals < 3 && $plan == 'A1' && $status == 1 && $old_plan == 0 && $total_referrals < $missed_days){
    if($missed_days > 3){
        $missed_days = 3;

    }
    $missed_days = $missed_days - $total_referrals;

    $sql = "SELECT DATE(datetime) AS date, SUM(ads) AS total_ads FROM `transactions` WHERE type = 'watch_ads' AND user_id = $user_id AND DATE(datetime) < '$date' AND DATE(datetime) >= '$joined_date'  GROUP BY DATE(datetime) HAVING total_ads < 1200 ORDER BY datetime DESC LIMIT $missed_days";
    $db->sql($sql);
    $res= $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1){
        $miss_date = '';
        foreach ($res as $row) {
            $date = $row['date'];
            $dateTime = new DateTime($date);
            $date = $dateTime->format('M d');
            $miss_date .= $date.',';
        }

        $response['success'] = false;
        $response['message'] = "Not Completing ".$missed_days." Days Work (".$miss_date.") So Refer ".$missed_days." Persons";
        print_r(json_encode($response));
        return false;
        
    }else{
        $response['success'] = false;
        $response['message'] = "Not Completing Work";
        print_r(json_encode($response));
        return false;

    }


}

if (!isBetween10AMand6PM()) {
    $response['success'] = false;
    $response['message'] = "Withdrawal time morning 10AM to 6PM";
    print_r(json_encode($response));
    return false;
}
if ($amount >= $min_withdrawal) {
    if ($amount <= $balance) {
        if ($account_num == '') {
            $response['success'] = false;
            $response['message'] = "Please Update Your Bank details";
            print_r(json_encode($response));
            return false;
        } else {
            if ($amount > 500 ) {
                $response['success'] = false;
                $response['message'] = "Maximum Withdrawal ₹500";
                print_r(json_encode($response));
                return false;
            }
            $sql = "SELECT id FROM withdrawals WHERE user_id = $user_id AND status = 0";
            $db->sql($sql);
            $res= $db->getResult();
            $num = $db->numRows($res);

            if ($num >= 1){
                $response['success'] = false;
                $response['message'] = "You Already Requested to Withdrawal pls wait...";
                print_r(json_encode($response));
                return false;

            }
            

            $sql = "INSERT INTO withdrawals (`user_id`,`amount`,`balance`,`status`,`datetime`) VALUES ('$user_id','$amount',$balance,0,'$datetime')";
            $db->sql($sql);
            $sql = "UPDATE users SET balance=balance-'$amount' WHERE id='$user_id'";
            $db->sql($sql);

            $response['success'] = true;
            $response['message'] = "Withdrawal Requested Successfully.";
            print_r(json_encode($response));
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Insufficient Balance";
        print_r(json_encode($response));
    }
} else {
    $response['success'] = false;
    $response['message'] = "Minimum Withdrawal Amount is $min_withdrawal";
    print_r(json_encode($response));
}
?>
