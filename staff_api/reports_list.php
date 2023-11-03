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
include_once('../includes/functions.php');
$fn = new functions;

$currentdate = date("Y-m-d");

if (empty($_POST['refer'])) {
    $response['success'] = false;
    $response['message'] = "Refer is empty";
    echo json_encode($response);
    return false;
}
if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "staff id is empty";
    echo json_encode($response);
    return false;
}

$staff_id = $db->escapeString($_POST['staff_id']);
$refer = $db->escapeString($_POST['refer']);

$sql = "SELECT * FROM users WHERE current_refers = $refer AND support_id='$staff_id' AND status= 1";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $response['success'] = true;
    $response['message'] = "Users listed successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "No users found";
    print_r(json_encode($response));
}



?>