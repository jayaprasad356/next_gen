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
    $response['message'] = "Staff Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['amount'])) {
    $response['success'] = false;
    $response['message'] = "Amount is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['type'])) {
    $response['success'] = false;
    $response['message'] = "Type is Empty";
    print_r(json_encode($response));
    return false;
}
$staff_id = $db->escapeString($_POST['staff_id']);
$amount = $db->escapeString($_POST['amount']);
$type = $db->escapeString($_POST['type']);

$datetime = date('Y-m-d H:i:s');
$date = date('Y-m-d');
$sql = "SELECT balance,salary_balance,incentive_percentage AS ip,weekly_target FROM staffs WHERE id = $staff_id ";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
$balance = $res[0]['balance'];
$salary_balance = $res[0]['salary_balance'];
$ip = $res[0]['ip'];
$weekly_target = $res[0]['weekly_target'];
$min_withdrawal = 250;
if ($num >= 1){
   $sql = "SELECT id FROM `users` WHERE support_id = $staff_id AND status = 1 AND YEAR(joined_date) = YEAR('$date') AND WEEK(joined_date) = WEEK('$date')";
   $db->sql($sql);
   $res = $db->getResult();
   $week_joins = $db->numRows($res);
   if($amount >= $min_withdrawal){
        if($type == 'incentives'){
            if($week_joins >= $weekly_target){
                $in_amount = ($ip / 100) * $amount;
                if($balance >= $amount){
                    $sql = "UPDATE `staffs` SET `balance` = balance - $amount,`withdrawal` = withdrawal + $amount WHERE `id` = $staff_id";
                    $db->sql($sql);
                    $sql = "INSERT INTO staff_withdrawals (`staff_id`,`amount`,`datetime`,`type`)VALUES('$staff_id','$in_amount','$datetime','incentives')";
                    $db->sql($sql);
                    $sql = "SELECT * FROM staffs WHERE id = $staff_id ";
                    $db->sql($sql);
                    $res = $db->getResult();
                    $response['success'] = true;
                    $response['message'] = "Withdrawal Requested Successfully";
                    $response['data'] = $res;
                    print_r(json_encode($response));
            
                }
                else{
                    $response['success'] = false;
                    $response['message'] = "Insufficent Balance";
                    print_r(json_encode($response)); 
                }

            }else{
                $response['success'] = false;
                $response['message'] = "you are not eligible for withdrawal";
                $response['week_joins'] = $week_joins;
                print_r(json_encode($response)); 
            }

        }
        else{
            if($salary_balance >= $amount){
                $sql = "UPDATE `staffs` SET `salary_balance` = salary_balance - $amount,`withdrawal` = withdrawal + $amount WHERE `id` = $staff_id";
                $db->sql($sql);
                $sql = "INSERT INTO staff_withdrawals (`staff_id`,`amount`,`datetime`,`type`)VALUES('$staff_id','$amount','$datetime','salary')";
                $db->sql($sql);
                $sql = "SELECT * FROM staffs WHERE id = $staff_id ";
                $db->sql($sql);
                $res = $db->getResult();
                $response['success'] = true;
                $response['message'] = "Withdrawal Requested Successfully";
                $response['data'] = $res;
                print_r(json_encode($response));
        
            }
            else{
                $response['success'] = false;
                $response['message'] = "Insufficent Balance";
                print_r(json_encode($response)); 
            }
        }
    }
    else{
        $response['success'] = false;
        $response['message'] = "Required Minimum Amount to Withdrawal is ".$min_withdrawal;
        print_r(json_encode($response)); 
    }
    

}
else{
    $response['success'] = false;
    $response['message'] = "Staff Not Found";
    print_r(json_encode($response)); 

}






?>