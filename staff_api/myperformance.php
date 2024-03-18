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

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "staff Id is Empty";
    print_r(json_encode($response));
    return false;
}
$staff_id = $db->escapeString($_POST['staff_id']);

$sql = "SELECT DATE(datetime) AS date,staff_id,(SELECT COUNT(id) FROM users WHERE support_id = $staff_id AND joined_date = date AND status = 1) AS total_joins,(SELECT COUNT(id) FROM users WHERE LENGTH(referred_by) = 3 AND support_id = 4 AND joined_date = date AND status = 1) AS direct_joins, SUM(amount) As total_earn FROM incentives WHERE staff_id = $staff_id GROUP BY DATE(datetime) ORDER BY datetime DESC";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    $response['success'] = true;
    $response['message'] = "Performance listed successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] =" Not Found";
    print_r(json_encode($response));
}
?>
