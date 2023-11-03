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

if (empty($_POST['refer_code'])) {
    $response['success'] = false;
    $response['message'] = "Refer Code is Empty";
    print_r(json_encode($response));
    return false;
}

$refer_code = $db->escapeString($_POST['refer_code']);

$sql = "SELECT * FROM posts
        LEFT JOIN users ON posts.user_id = users.id
        WHERE users.refer_code = '$refer_code'";

$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['user_id'] = $row['user_id'];
        $temp['image'] = $row['image'];
        $temp['caption'] = $row['caption'];
        $temp['likes'] = $row['likes'];
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Posts Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Posts Not found for the specified Refer Code";
    print_r(json_encode($response));
}
?>
