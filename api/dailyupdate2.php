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
$currentdate = date('Y-m-d');


$sql = "SELECT * FROM leaves WHERE date = '$currentdate' AND type = 'common_leave'";
$db->sql($sql);
$resl = $db->getResult();
$lnum = $db->numRows($resl);
$enable = 1;
if ($lnum >= 1) {
    $sql = "UPDATE settings SET orders_status = 0 ";
    $db->sql($sql);

}
else{
    $sql = "UPDATE settings SET orders_status = 1 ";
    $db->sql($sql);
}



?>