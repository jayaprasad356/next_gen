<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
require_once '../includes/functions.php';
require_once('../includes/firebase.php');
require_once ('../includes/push.php');

$fnc = new functions;

include_once('../includes/custom-functions.php');
    
$fn = new custom_functions;
$db = new Database();
$db->connect();
if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['title'])) {
    $response['success'] = false;
    $response['message'] = "Title is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['description'])) {
    $response['success'] = false;
    $response['message'] = "Description is Empty";
    print_r(json_encode($response));
    return false;
}
$title = $db->escapeString($_POST['title']);
$description = $db->escapeString($_POST['description']);
$mobile = $db->escapeString($_POST['mobile']);
$type = (isset($_POST['type']) && !empty($_POST['type'])) ? $db->escapeString($_POST['type']) : "chat";
$sql = "SELECT * FROM users WHERE mobile ='$mobile'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $url .= $_SERVER['REQUEST_URI'];
    $server_url = dirname($url).'/';
    
    $push = null;
    $id = "0";
    $devicetoken = $fnc->getTokenByMobile($mobile);
    $push = new Push(
        $title,
        $description,
        null,
        $type,
        $id
    );

    // $mPushNotification = $push->getPush();


    $f_tokens = array_unique($devicetoken);
    $devicetoken_chunks = array_chunk($f_tokens,1000);
    foreach($devicetoken_chunks as $devicetokens){
        $firebase = new Firebase(); 
        $tokenString = implode(',', $devicetokens);
        $firebase->send($tokenString, $title, $description);
        
        $response['token'] = $devicetokens;
        
    }

    $response['success'] = true;
    $response['message'] = "Notification Send Successfully";
    print_r(json_encode($response));
}

?>