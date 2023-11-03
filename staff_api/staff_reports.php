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
$date = date('Y-m-d');

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "Staff Id is Empty";
    print_r(json_encode($response));
    return false;
}

$staff_id = $db->escapeString($_POST['staff_id']);
$user_id = $db->escapeString($_POST['user_id']);
$level = isset($_POST['level']) ? $db->escapeString($_POST['level']) : null; // Modified here
if($level == 1){
    $level = 2;
}
else if($level == 2){
    $level = 3;
}
else if($level == 3){
    $level = 4;
}
$sql = "SELECT id,name,mobile,joined_date,level,datediff('$date', joined_date) AS history_days FROM users WHERE support_id = $staff_id ORDER BY id DESC";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    if ($level !== null) { // Modified here
        $sql = "UPDATE users SET level = $level WHERE id='$user_id'";
        $db->sql($sql);
    }

    $response['success'] = true;
    $response['message'] = "My Users For Support Listed Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "No Users Found";
    print_r(json_encode($response));
}
?>
