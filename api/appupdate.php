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

$app_version = (isset($_POST['app_version']) && $_POST['app_version'] != "") ? $db->escapeString($_POST['app_version']) : 0;
$sql = "SELECT * FROM app_settings";
$db->sql($sql);
$set = $db->getResult();
$version = $set[0]['version'];
$res = array();
if($app_version < $version){
    $response['success'] = false;
    $response['message'] = "Please update your app to the latest version.";
    $response['data'] = $set;
    print_r(json_encode($response));

}
else{
    $response['success'] = true;
    $response['message'] = "Your app is up to date.";
    print_r(json_encode($response));
}

?>