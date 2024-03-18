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
$fn->monitorApi('updateuser');
if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "Staff Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['first_name'])) {
    $response['success'] = false;
    $response['message'] = "First Name is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['last_name'])) {
    $response['success'] = false;
    $response['message'] = "Last Name is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['email'])) {
    $response['success'] = false;
    $response['message'] = "Email Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile is Empty";
    print_r(json_encode($response));
    return false;
}
$staff_id = $db->escapeString($_POST['staff_id']);
$first_name = $db->escapeString($_POST['first_name']);
$last_name = $db->escapeString($_POST['last_name']);
$email = $db->escapeString($_POST['email']);
$password = $db->escapeString($_POST['password']);
$mobile = $db->escapeString($_POST['mobile']);


$sql = "SELECT * FROM staffs WHERE id=" . $staff_id;
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1) {
    $sql = "UPDATE staffs SET first_name='$first_name',last_name='$last_name',password='$password',email='$email',mobile='$mobile' WHERE id=" . $staff_id;
    $db->sql($sql);
    $sql = "SELECT * FROM staffs WHERE id=" . $staff_id;
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Staff Updated Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
    return false;
}
else{
    
    $response['success'] = false;
    $response['message'] ="Staff Not Found";
    print_r(json_encode($response));
    return false;

}

?>