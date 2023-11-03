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

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "Staff Id is Empty";
    print_r(json_encode($response));
    return false;
}
$staff_id = $db->escapeString($_POST['staff_id']);
$sql = "SELECT * FROM staff_withdrawals WHERE staff_id = '$staff_id' ORDER BY id DESC";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['staff_id'] = $row['staff_id'];
        $temp['amount'] = $row['amount'];
        $temp['datetime'] = $row['datetime'];
        $status = $row['status'];
        if($status == 0){
            $temp['status'] = 'pending';

        }else if($status == 1){
            $temp['status'] = 'paid';

        }else{
            $temp['status'] = 'cancelled';

        }
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Staff Withdrawals listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>