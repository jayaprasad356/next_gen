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

if (!isBetween10AMand6PM()) {
    $response['success'] = false;
    $response['message'] = "Withdrawal time morning 10AM to 6PM";
    print_r(json_encode($response));
    return false;
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
$total_orders = $res[0]['total_orders'];
$worked_days = $res[0]['worked_days'];
$total_referrals = $res[0]['total_referrals'];
$old_plan = $res[0]['old_plan'];
$plan = $res[0]['plan'];
$blocked = $res[0]['blocked'];
$target_orders = ($worked_days + 1) * 1200;
$percentage = 70;
$result = ($percentage / 100) * $target_orders;
if ($blocked == 1) {
    $response['success'] = false;
    $response['message'] = "Your Account is Blocked";
    print_r(json_encode($response));
    return false;
}


if ($worked_days > 10 && $total_orders < $result && $total_referrals == 0 && $old_plan == 0 && $plan == 'A1') {
    $response['success'] = false;
    $response['message'] = "You missed to Complete 70% work(".$result." orders) So refer 1 person get 1200 orders to withdrawal";
    print_r(json_encode($response));
    return false;
}

if ($withdrawal_status == '0') {
    $response['success'] = false;
    $response['message'] = "Withdrawals are currently disabled for your account.";
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

            $sql = "INSERT INTO withdrawals (`user_id`,`amount`,`balance`,`status`,`datetime`) VALUES ('$user_id','$amount',$balance,0,'$datetime')";
            $db->sql($sql);
            $sql = "UPDATE users SET balance=balance-'$amount' WHERE id='$user_id'";
            $db->sql($sql);

            $response['success'] = true;
            $response['message'] = "Withdrawal Requested Successfully. Your withdrawal status is now stable.";
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
