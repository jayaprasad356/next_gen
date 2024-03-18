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
    $response['message'] = "Staff Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['date'])) {
    $response['success'] = false;
    $response['message'] = "Date is Empty";
    print_r(json_encode($response));
    return false;
}

if (empty($_POST['reason'])) {
    $response['success'] = false;
    $response['message'] = "Reason is Empty";
    print_r(json_encode($response));
    return false;
}

$staff_id = $db->escapeString($_POST['staff_id']);
$date = $db->escapeString($_POST['date']);
$reason = $db->escapeString($_POST['reason']);


$sql = "INSERT INTO staff_leaves (`staff_id`,`date`,`reason`)VALUES('$staff_id','$date','$reason')";
$db->sql($sql);
$res = $db->getResult();
$response['success'] = true;
$response['message'] = "Leave Applied Successfully";
print_r(json_encode($response));

?>