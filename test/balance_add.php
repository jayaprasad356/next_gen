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



$sql = "SELECT id FROM `users` WHERE earn != 0 ";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);
if ($num >= 1){
    
    foreach ($res as $row) {
        $user_id = $row['id'];

        $sql = "SELECT SUM(amount) FROM `transactions` WHERE type = 'orders_earnings' AND user_id = $user_id";
        $db->sql($sql);
        $res= $db->getResult();
        $amount = $res[0]['amount'];
        $sql = "UPDATE users SET balance = $amount WHERE id = $user_id";
        $db->sql($sql);
    

    }
    $response['success'] = true;
    $response['message'] = "updated Successfully";
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Users Not found";
    print_r(json_encode($response));

}
?>