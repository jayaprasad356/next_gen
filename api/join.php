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

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['description'])) {
    $response['success'] = false;
    $response['message'] = "Description is Empty";
    print_r(json_encode($response));
    return false;
} 

$user_id = $db->escapeString($_POST['user_id']);
$abcd_user = isset($_POST['abcd_user']) ? $db->escapeString($_POST['abcd_user']) : '';
$description = $db->escapeString($_POST['description']);

$sql = "SELECT * FROM users WHERE id=" . $user_id;
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1) {
    $sql = "UPDATE users SET abcd_user='$abcd_user',description='$description' WHERE id=" . $user_id;
    $db->sql($sql);
    $sql = "SELECT * FROM users WHERE id=" . $user_id;
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "User Updated Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
    return false;
}
else{
    
    $response['success'] = false;
    $response['message'] ="User Not Found";
    print_r(json_encode($response));
    return false;

}

?>