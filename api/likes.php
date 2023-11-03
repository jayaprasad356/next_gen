<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['post_id'])) {
    $response['success'] = false;
    $response['message'] = "Post Id is Empty";
    print_r(json_encode($response));
    return false;
}


$user_id = $db->escapeString($_POST['user_id']);
$post_id = $db->escapeString($_POST['post_id']);
$status = $db->escapeString($_POST['status']);


$sql = "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
 

    if($status == 1){
        $sql = "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id AND status = 0";
        $db->sql($sql);
        $lres = $db->getResult();
        $num = $db->numRows($lres);
        if ($num >= 1) {
            $sql = "UPDATE posts SET `likes` = likes + 1 WHERE id = $post_id";
            $db->sql($sql);

        }



    }else{
        $sql = "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id  AND status = 1";
        $db->sql($sql);
        $lres = $db->getResult();
        $num = $db->numRows($lres);
        if ($num >= 1) {
            $sql = "UPDATE posts SET `likes` = likes - 1 WHERE id = $post_id";
            $db->sql($sql);

        }

    }



    $sql = "UPDATE likes SET `status` = $status WHERE post_id = $post_id";
    $db->sql($sql);

    $response['success'] = true;
    $response['message'] = "Like Updated Successfully";
    echo json_encode($response);
} else {

    $sql = "INSERT INTO likes (user_id, post_id, `status`) VALUES ($user_id, $post_id, 1)";
    $db->sql($sql);

    $sql = "UPDATE posts SET `likes` = likes + 1 WHERE id = $post_id";
    $db->sql($sql);

    $response['success'] = true;
    $response['message'] = "Like Added Successfully";
    echo json_encode($response);
}
?>
