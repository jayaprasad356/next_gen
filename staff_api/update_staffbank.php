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

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "staffs Id is Empty";
    print_r(json_encode($response));
    return false;
}

if (empty($_POST['bank_account_number'])) {
    $response['success'] = false;
    $response['message'] = "Bank Account Number is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['ifsc_code'])) {
    $response['success'] = false;
    $response['message'] = "ifsc_code is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['bank_name'])) {
    $response['success'] = false;
    $response['message'] = "Bank Name is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['branch'])) {
    $response['success'] = false;
    $response['message'] = "Branch is Empty";
    print_r(json_encode($response));
    return false;
}


$staff_id = $db->escapeString($_POST['staff_id']);
$bank_account_number = $db->escapeString($_POST['bank_account_number']);
$ifsc_code = $db->escapeString($_POST['ifsc_code']);
$bank_name = $db->escapeString($_POST['bank_name']);
$branch = $db->escapeString($_POST['branch']);
$family1 = $db->escapeString($_POST['family1']);
$family2 = $db->escapeString($_POST['family2']);

$sql = "SELECT * FROM staffs WHERE id=" . $staff_id;
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $sql = "UPDATE staffs SET bank_account_number='$bank_account_number',ifsc_code='$ifsc_code',bank_name='$bank_name',branch='$branch',family1='$family1',family2='$family2' WHERE id=" . $staff_id;
    $db->sql($sql);
    $sql = "SELECT * FROM staffs WHERE id=" . $staff_id;
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Bank details Updated Successfully";
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