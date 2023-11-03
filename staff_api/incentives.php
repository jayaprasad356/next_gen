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

$sql = "SELECT * FROM incentives LEFT JOIN users ON incentives.user_id=users.id WHERE incentives.staff_id = '$staff_id' ORDER BY datetime DESC";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $temp['name'] = $row['name'];
        $temp['amount'] = $row['amount'];
        $temp['joined_date'] = $row['joined_date'];
        $temp['type'] = $row['type'];
        $temp['refer_code'] = $row['refer_code'];
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "incentives listed successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));    
} else {
    $response['success'] = false;
    $response['message'] = "incentives not found";
    print_r(json_encode($response));
}
