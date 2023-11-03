<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
include('../includes/custom-functions.php');
include('../includes/variables.php');
$db = new Database();
$db->connect();
$fn = new custom_functions;

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User ID is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$caption = (isset($_POST['caption']) && $_POST['caption'] != "") ? $db->escapeString($_POST['caption']) : "";
    
$sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1) {
    if (isset($_FILES['image']) && !empty($_FILES['image']) && $_FILES['image']['error'] == 0 && $_FILES['image']['size'] > 0) {
        if (!is_dir('../upload/post/')) {
            mkdir('../upload/post/', 0777, true);
        }
        $image = $db->escapeString($fn->xss_clean($_FILES['image']['name']));
        $extension = pathinfo($_FILES["image"]["name"])['extension'];
        $result = $fn->validate_image($_FILES["image"]);
        if (!$result) {
            $response["success"]   = false;
            $response["message"] = "Image type must jpg, jpeg, gif, or png!";
            print_r(json_encode($response));
            return false;
        }
        $filename = microtime(true) . '.' . strtolower($extension);
        $full_path = '../upload/post/' . "" . $filename;
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
            $response["success"]   = false;
            $response["message"] = "Invalid directory to load image!";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO posts (`user_id`,`caption`,`image`,`status`) VALUES ('$user_id','$caption','$filename',0)";
        $db->sql($sql);
        $response["success"]   = true;
        $response["message"] = "Post Image Successfully";
    }
    else{
        $response["success"]   = false;
        $response["message"] = "image parameter is missing.";

    }

}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";

}
print_r(json_encode($response));
return false;
?>