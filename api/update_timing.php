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


$sql = "SELECT * FROM users WHERE  total_referrals = 0 AND worked_days >= 7 AND status = 1";
$db->sql($sql);
$res = $db->getResult();
foreach ($res as $row) {
    $user_id = $row['id'];
    $total_orders = $row['total_orders'];
    $worked_days = $row['worked_days'];
    $target_orders = ($worked_days + 1) * 1200;
    $percentage = 70;
    $result = ($percentage / 100) * $target_orders;
    if ($total_orders < $result) {
        $sql = "UPDATE users SET orders_time = 25 WHERE id = $user_id";
        $db->sql($sql);
    }

    $rows[] = $temp;
}
$response['success'] = false;
$response['message'] = "Updated";
print_r(json_encode($response));


?>
