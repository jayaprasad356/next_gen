<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
date_default_timezone_set('Asia/Kolkata');
$db = new Database();
$db->connect();

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "staffs Id is Empty";
    print_r(json_encode($response));
    return false;
}

$staff_id = $db->escapeString($_POST['staff_id']);
$date = date('Y-m-d');
$sql = "SELECT *,staffs.id AS id FROM staffs LEFT JOIN staff_roles ON staffs.staff_role_id = staff_roles.id WHERE staffs.id = " . $staff_id;
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $sql = "SELECT id FROM `users` WHERE support_id = $staff_id AND status = 1 AND YEAR(joined_date) = YEAR('$date') AND WEEK(joined_date) = WEEK('$date')";
    $db->sql($sql);
    $wres = $db->getResult();
    $week_joins = $db->numRows($wres);
    $res[0]['staff_id'] = 'Weekly Target '.$week_joins.'/'.$res[0]['weekly_target'];

    $sql = "SELECT SUM(amount) AS salary_amount FROM staff_transactions WHERE type='salary' AND staff_id=" . $staff_id;
    $db->sql($sql);
    $result = $db->getResult();
    $salary=$result[0]['salary_amount'];
    
    $sql = "SELECT SUM(amount) AS incentive_amount FROM staff_transactions WHERE type!='salary' AND staff_id=" . $staff_id;
    $db->sql($sql);
    $result1 = $db->getResult();
    $incentive=$result1[0]['incentive_amount'];

    $sql ="SELECT COUNT(id) AS total_leads FROM users WHERE lead_id='$staff_id'";
    $db->sql($sql);
    $res_count = $db->getResult();
    $sql ="SELECT COUNT(id) AS total_joinings FROM users WHERE support_id='$staff_id'";
    $db->sql($sql);
    $res_count1= $db->getResult();

    $sql ="SELECT COUNT(id) AS today_joinings FROM users WHERE support_id='$staff_id' AND joined_date = '$date'  AND status = 1";
    $db->sql($sql);
    $res_count2= $db->getResult();

    $sql ="SELECT COUNT(id) AS total_active_users FROM users WHERE support_id='$staff_id' AND status = 1 AND code_generate = 1 AND today_codes != 0";
    $db->sql($sql);
    $res_count3= $db->getResult();


    $sql = "SELECT u.id FROM `users` u,`transactions` t WHERE u.id = t.user_id AND DATE(t.datetime) = '$date' AND t.type = 'refer_bonus' AND u.support_id = $staff_id";
    $db->sql($sql);
    $res_count4 = $db->getResult();
    $today_refers = $db->numRows($res_count4);


    
    $response['success'] = true;
    $response['message'] = "staff details Retrieved Successfully";
    if(!empty($res[0]['resume']) && !empty($res[0]['aadhar_card']) && !empty($res[0]['education_certificate']) && !empty($res[0]['photo'])){
        $response['document_upload'] = 1;
    }
    else{
        $response['document_upload'] = 0;
    }
    $today_performance = 0;
    if($res_count3[0]['total_active_users'] != 0){
        $today_performance = ($today_refers / $res_count3[0]['total_active_users']) * 100;
    

    }
  
    $response['salary'] = $salary;
    $response['incentive_earn'] = $incentive;
    $response['total_earnings'] = $salary + $incentive;
    $response['total_leads'] = $res_count[0]['total_leads'];
    $response['total_joinings'] =$res_count1[0]['total_joinings'];
    $response['today_joinings'] =$res_count2[0]['today_joinings'];
    $response['total_active_users'] = $res_count3[0]['total_active_users'];
    $response['today_refers'] =$today_refers;
    $response['today_performance'] =round($today_performance);
    $response['data'] = $res;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] ="Staff Not Found";
    print_r(json_encode($response));
}
?>
