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
include_once('../includes/functions.php');
$fn = new functions;

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['leave_date'])) {
    $response['success'] = false;
    $response['message'] = "Leave Date is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['reason'])) {
    $response['success'] = false;
    $response['message'] = "Reason is Empty";
    print_r(json_encode($response));
    return false;
}
$user_id = $db->escapeString($_POST['user_id']);
$leave_date = $db->escapeString($_POST['leave_date']);
$reason = $db->escapeString($_POST['reason']);


function checkLeaveDate($leaveDate) {
    // Create DateTime objects for leave date and the day after tomorrow
    $leaveDateObj = new DateTime($leaveDate);
    $dayAfterTomorrow = new DateTime('+1 days');

    // Compare leave date with the day after tomorrow
    if ($leaveDateObj <= $dayAfterTomorrow) {
        return true;
    } else {
        return false;
    }
}
if (!checkLeaveDate($leave_date)) {
    $response['success'] = false;
    $response['message'] = "You can apply leave, one day before the leave date.";
    print_r(json_encode($response));
    return false;
}

$leaveMonthYear = date('Y-m', strtotime($leave_date));

$sql = "SELECT id FROM users WHERE id = $user_id AND status = 1";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num == 1) {
    $sql = "SELECT id FROM leaves WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$leaveMonthYear'";
    $db->sql($sql);
    $res = $db->getResult();
    $numLeavesThisMonth = $db->numRows($res);

    if ($numLeavesThisMonth >= 2) {
        $response['success'] = false;
        $response['message'] = "You are not allowed above 2 leaves in a month";
        print_r(json_encode($response));
    } else {
        $sql = "SELECT id FROM leaves WHERE user_id = $user_id AND date = '$leave_date'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num == 1) {
            $response['success'] = false;
            $response['message'] = "Already Applied on that day";
            print_r(json_encode($response));
    
        }else{
            $sql = "SELECT id FROM leaves WHERE type = 'common_leave' AND date = '$leave_date'";
            $db->sql($sql);
            $res = $db->getResult();
            $num = $db->numRows($res);
            if ($num >= 1) {
                $response['success'] = false;
                $response['message'] = "That Day Common Leave For All";
                print_r(json_encode($response));
    
            }
            else{
                $sql_query = "INSERT INTO leaves (type,user_id,date,reason,status)VALUES('user_leave','$user_id','$leave_date','$reason',1)";
                $db->sql($sql_query);
                $response['success'] = true;
                $response['message'] = "Leave Requested Successfully";
                print_r(json_encode($response));
    
            }
        }


    }



}
else{
    
    $response['success'] = false;
    $response['message'] ="User Not Found";
    print_r(json_encode($response));
    return false;

}

?>