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
include_once('../includes/functions.php');
$fn = new functions;


if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "Staff ID is empty";
    echo json_encode($response);
    exit; 
}

if (empty($_POST['amount'])) {
    $response['success'] = false;
    $response['message'] = "Amount is empty";
    echo json_encode($response);
    exit; 
}

$staff_id = $db->escapeString($_POST['staff_id']);
$amount = $db->escapeString($_POST['amount']);
$datetime = date('Y-m-d');

$sql = "SELECT * FROM staffs WHERE id = $staff_id";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if($num == 1){
    $staff = $res[0];
    $has_bank_details = (!empty($staff['bank_name']) && !empty($staff['bank_account_number']));
    if($has_bank_details){
        if($amount>$staff['balance']){
            $response['success'] = false;
            $response['message'] = "Your Wallet Balance is Low";
            print_r(json_encode($response)); 
        }
        else{
            $sql = "INSERT INTO staff_withdrawals (staff_id, amount,date) VALUES ('$staff_id', '$amount','$datetime')";
            $db->sql($sql);
            $sql = "UPDATE staffs SET balance = balance - $amount WHERE id = '$staff_id'";
            $db->sql($sql);
            $sql = "SELECT balance FROM staffs WHERE id = $staff_id ";
            $db->sql($sql);
            $res = $db->getResult();
            $balance = $res[0]['balance'];
            $response['success'] = true;
            $response['message'] = "Withdrawal request submitted successfully.";
            $response['balance'] = $balance;
            print_r(json_encode($response));
        }
    }
    else{
        $response['success'] = false;
        $response['message'] = "Staff does not have bank details. Please update your bank details.";
        print_r(json_encode($response)); 
    }
}else{
    $response['success'] = false;
    $response['message'] = "Staff not found.";
    print_r(json_encode($response));
}
