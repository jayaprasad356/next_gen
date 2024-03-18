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

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['name'])) {
    $response['success'] = false;
    $response['message'] = "Name is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile Number is Empty";
    print_r(json_encode($response));
    return false;
}

// Remove any non-numeric characters from the mobile number
$mobileNumber = preg_replace('/[^0-9]/', '', $_POST['mobile']);

if (strlen($mobileNumber) !== 10) {
    $response['success'] = false;
    $response['message'] = "Mobile number should be exactly 10 digits,please remove if +91 is there";
    print_r(json_encode($response));
    return false;
}

if (empty($_POST['email'])) {
    $response['success'] = false;
    $response['message'] = "Email is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['dob'])) {
    $response['success'] = false;
    $response['message'] = "Date of Birth is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['hr_id'])) {
    $response['success'] = false;
    $response['message'] = "HR ID is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['location'])) {
    $response['success'] = false;
    $response['message'] = "Location is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['aadhaar_num'])) {
    $response['success'] = false;
    $response['message'] = "Aadhaar Number is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$name = $db->escapeString($_POST['name']);
$mobile = $db->escapeString($_POST['mobile']); 
$email = $db->escapeString($_POST['email']);
$dob = $db->escapeString($_POST['dob']);
$hr_id = $db->escapeString($_POST['hr_id']);
$location = $db->escapeString($_POST['location']);
$aadhaar_num = $db->escapeString($_POST['aadhaar_num']);


$response['success'] = false;
$response['message'] = "Cannot update profile";
print_r(json_encode($response));
return false;

$sql = "SELECT * FROM users WHERE id=" . $user_id;
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1) {
    $sql = "UPDATE users SET name='$name',mobile='$mobile',email='$email',dob='$dob',hr_id='$hr_id',location='$location',aadhaar_num='$aadhaar_num' WHERE id=" . $user_id;
    $db->sql($sql);
    $sql = "SELECT * FROM users WHERE id=" . $user_id;
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "User Updated Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
    return false;
}
else{
    
    $response['success'] = false;
    $response['message'] ="User Not Found";
    print_r(json_encode($response));
    return false;

}

?>