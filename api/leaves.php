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

date_default_timezone_set('Asia/Kolkata');
include_once('../includes/functions.php');
$fn = new functions;



$date = date('Y-m-d');


$sql = "UPDATE `users` SET `code_generate` = 1,withdrawal_status = 1 WHERE `code_enable_date` = '$date'";
$db->sql($sql);

$sql = "UPDATE `settings` SET `code_generate` = 1 WHERE `id` = 1";
$db->sql($sql);

$sql = "SELECT * FROM leaves WHERE date='$date' AND status = 1 AND type = 'common_leave' ORDER BY date";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    $sql = "UPDATE `settings` SET `code_generate` = 0 WHERE `id` = 1";
    $db->sql($sql);

    $response['success'] = true;
    $response['message'] = "Leave Updated Successfully";
    print_r(json_encode($response));


}else{
    $sql = "UPDATE `users` SET `worked_days` = worked_days + 1 WHERE `code_generate` = 1 AND status = 1";
    $db->sql($sql);

    $sql = "SELECT * FROM leaves WHERE date='$date' AND status = 1 AND type = 'user_leave' ORDER BY date";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);

    if ($num >= 1) {
        foreach ($res as $row) {
            $user_id = $row['user_id'];
            $leave_date = $row['date'];
            $next_date = date('Y-m-d', strtotime($leave_date . ' +1 day'));
            $sql = "UPDATE users SET `worked_days` = worked_days - 1,code_enable_date = '$next_date',code_generate=0,withdrawal_status = 0  WHERE id = $user_id";
            $db->sql($sql);
        }

        $response['success'] = true;
        $response['message'] = "Leave Updated Successfully";
        print_r(json_encode($response));
    }
}


