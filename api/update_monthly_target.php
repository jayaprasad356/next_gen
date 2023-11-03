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

$datetime = date('Y-m-d H:i:s');
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

$sql = "SELECT * FROM users WHERE worked_days = 30 AND plan = 'A1'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);


if ($num >= 1){
    foreach ($res as $row) {
        $user_id = $row['id'];
        $basic_wallet = $row['basic_wallet'];
        $premium_wallet = $row['premium_wallet'];
        $current_refers = $row['current_refers'];
        $target_refers = $row['target_refers'];
        if($current_refers < $target_refers){
            $status = 0;
        }else{
            $status = 1;
        }
        if($basic_wallet != 0){
            $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'basic_wallet','$datetime',$basic_wallet)";
            $db->sql($sql);

        }

        $sql = "UPDATE users SET balance= balance + $basic_wallet,earn = earn + $basic_wallet,basic_wallet = basic_wallet - $basic_wallet,current_refers = 0,target_refers = 5 WHERE id=" . $user_id;
        $db->sql($sql);
        $sql = "INSERT INTO monthly_target (user_id,premium_wallet,current_refers,target_refers,datetime,status) VALUES ($user_id,$premium_wallet,$current_refers,$target_refers,'$datetime',$status)";
        $db->sql($sql);
    }

    $response['success'] = false;
    $response['message'] = "Updated Successfully";

}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
}

print_r(json_encode($response));




?>
