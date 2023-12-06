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

$sql = "UPDATE users SET last_today_orders = today_orders";
$db->sql($sql);

$sql = "UPDATE users SET today_orders = 0";
$db->sql($sql);

$sql = "UPDATE users
SET worked_days = DATEDIFF('$currentdate', joined_date) - (
    SELECT COUNT(*) 
    FROM leaves
    WHERE date >= users.joined_date  AND date <= '$currentdate'
)
WHERE status = 1";
$db->sql($sql);

$sql = "UPDATE users SET average_orders = total_orders / worked_days WHERE status = 1";
$db->sql($sql);


$sql = "SELECT * FROM leaves WHERE date = '$currentdate'";
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