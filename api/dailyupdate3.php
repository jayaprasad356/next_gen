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


// $sql = "SELECT user_id FROM leaves WHERE date = '$currentdate' AND type = 'user_leave'";
// $db->sql($sql);
// $resl = $db->getResult();
// $lnum = $db->numRows($resl);
// if ($lnum >= 1) {
//     foreach ($resl as $row) {
//         $user_id = $row['user_id'];
//         $sql = "UPDATE users SET worked_days = worked_days - 1 WHERE id = $user_id ";
//         $db->sql($sql);
//     }
// }



?>