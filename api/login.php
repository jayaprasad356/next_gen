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

if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['device_id'])) {
    $response['success'] = false;
    $response['message'] = "Device Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    print_r(json_encode($response));
    return false;
}
$mobile = $db->escapeString($_POST['mobile']);
$device_id = $db->escapeString($_POST['device_id']);
$password = $db->escapeString($_POST['password']);
$datetime = date('Y-m-d H:i:s');

$sql = "SELECT * FROM users WHERE mobile = '$mobile' AND password = '$password'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1){
    $status = $res[0]['status'];
    if ($status == 1 || $status == 0) {
        $sql_query = "UPDATE users SET device_id = '$device_id' WHERE mobile ='$mobile' AND device_id = ''";
        $db->sql($sql_query);



        $sql = "SELECT * FROM users WHERE mobile = '$mobile'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $user_id = $res[0]['id']; 
        $login_time = $res[0]['login_time'];
        $sql = "INSERT INTO login_attempts (`user_id`, `datetime`) VALUES ('$user_id', '$datetime')";
        $db->sql($sql);

        if ($login_time != '0000-00-00 00:00:00' && $login_time != null) {
            $sql_update_blocked = "UPDATE users SET blocked = 1,order_available = 0 WHERE mobile ='$mobile'";
            $db->sql($sql_update_blocked);
            
            $response['success'] = false;
            $response['registered'] = false;
            $response['login_time'] = $login_time;
            $response['message'] = "User is blocked";
            print_r(json_encode($response));
            return false;
        }

        $datetime = date('Y-m-d H:i:s');
            

        $sql_query = "UPDATE users SET login_time = '$datetime' WHERE mobile ='$mobile'";
        $db->sql($sql_query);


        if ($num == 1) {
            $response['success'] = true;
            $response['registered'] = true;
            $response['message'] = "Logged In Successfully";
            $response['data'] = $res;
            print_r(json_encode($response));
        } else {
            $sql = "INSERT INTO devices (`mobile`,`device_id`) VALUES ('$mobile','$device_id')";
            $db->sql($sql);
           
            $response['success'] = false;
            $response['registered'] = false;
            $response['message'] = "Please Login With your Device";
            print_r(json_encode($response));
            return false;
        }
    }
            


}
else{
    $response['success'] = false;
    $response['registered'] = false;
    $response['message'] = "User Credentials not match";
    print_r(json_encode($response));
    return false;
}

