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

// if (empty($_POST['user_id'])) {
//     $response['success'] = false;
//     $response['message'] = "User ID is Empty";
//     print_r(json_encode($response));
//     return false;
// }

$user_id = 5420;

$sql = "SELECT * FROM `posts` ORDER BY RAND() LIMIT 25";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1){
    foreach ($res as $row) {
        $post_id = $row['id'];
        $temp['id'] = $row['id'];
        $temp['caption'] = $row['caption'];
        $temp['name'] = 'John Cena';
        $temp['image'] = DOMAIN_URL.'upload/post/'.$row['image'];
        $temp['likes'] = $row['likes'];
        $temp['share_link'] = DOMAIN_URL.'mypost.php?id='.$row['id'];
        $sql = "SELECT * FROM `likes` WHERE user_id = $user_id AND post_id = $post_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);

        if ($num >= 1){
            $status = $res[0]['status'];
            $temp['user_like'] = $status;

        }else{
            $temp['user_like'] = "0";

        }
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "posts Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Not found";
    print_r(json_encode($response));
}