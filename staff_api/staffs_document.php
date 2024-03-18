
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
include_once('../includes/custom-functions.php');
include_once('../includes/functions.php');
$fn = new custom_functions;

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "staffs Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['salary_date'])) {
    $response['success'] = false;
    $response['message'] = "Please enter a salery date";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['dob'])) {
    $response['success'] = false;
    $response['message'] = "Date Of Birth is Empty";
    print_r(json_encode($response));
    return false;
}
if (!isset($_FILES['aadhar_card']) || empty($_FILES['aadhar_card']['name'])) {
    $response['success'] = false;
    $response['message'] = "Please upload an Aadhar Card Document";
    print_r(json_encode($response));
    return false;
}

if (!isset($_FILES['resume']) || empty($_FILES['resume']['name'])) {
    $response['success'] = false;
    $response['message'] = "Please upload an resume Document";
    print_r(json_encode($response));
    return false;
}

if (!isset($_FILES['photo']) || empty($_FILES['photo']['name'])) {
    $response['success'] = false;
    $response['message'] = "Please upload an photo";
    print_r(json_encode($response));
    return false;
}

if (!isset($_FILES['education_certificate']) || empty($_FILES['education_certificate']['name'])) {
    $response['success'] = false;
    $response['message'] = "Please upload an education_certificate ";
    print_r(json_encode($response));
    return false;
}


$staff_id = $db->escapeString($_POST['staff_id']);
$salary_date = $db->escapeString($_POST['salary_date']);
$dob = $db->escapeString($_POST['dob']);

if (isset($_FILES['aadhar_card']) && !empty($_FILES['aadhar_card']) && $_FILES['aadhar_card']['error'] == 0 && $_FILES['aadhar_card']['size'] > 0) {
    $uploadDir = '../upload/aadhar_card';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = $_FILES['aadhar_card']['name'];
    $aadhar_card = $uploadDir . '/' . $fileName;
    $extension = pathinfo($aadhar_card, PATHINFO_EXTENSION);
    if (strtolower($extension) !== 'pdf') {
        $response["success"] = false;
        $response["message"] = "Aadhar Card file type must be pdf!";
        print_r(json_encode($response));
        return false;
    }
    $upload_image = 'upload/aadhar_card/' . $fileName;
    move_uploaded_file($_FILES['aadhar_card']['tmp_name'], $aadhar_card);
}

if (isset($_FILES['resume']) && !empty($_FILES['resume']) && $_FILES['resume']['error'] == 0 && $_FILES['resume']['size'] > 0) {
    $uploadDir = '../upload/resume';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = $_FILES['resume']['name'];
    $resume = $uploadDir . '/' . $fileName;
    $extension = pathinfo($resume, PATHINFO_EXTENSION);
    if (strtolower($extension) !== 'pdf') {
        $response["success"] = false;
        $response["message"] = "Resume file type must be pdf!";
        print_r(json_encode($response));
        return false;
    }
    $upload_image1 = 'upload/resume/' . $fileName;
    move_uploaded_file($_FILES['resume']['tmp_name'], $resume);
}

if (isset($_FILES['photo']) && !empty($_FILES['photo']) && $_FILES['photo']['error'] == 0 && $_FILES['photo']['size'] > 0) {
    if (!is_dir('../upload/photo/')) {
        mkdir('../upload/photo/', 0777, true);
    }
    $photo = $db->escapeString($fn->xss_clean($_FILES['photo']['name']));
    $extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
    $result = $fn->validate_image($_FILES["photo"]);
    if (!$result) {
        $response["success"] = false;
        $response["message"] = "Image type must be jpg, jpeg, gif, or png!";
        print_r(json_encode($response));
        return false;
    }
    $photo_name = microtime(true) . '.' . strtolower($extension);
    $full_path = '../upload/photo/' . $photo_name;
    $upload_image2 = 'upload/photo/' . $photo_name;
    if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $full_path)) {
        $response["success"] = false;
        $response["message"] = "Invalid directory to upload image!";
        print_r(json_encode($response));
        return false;
    }
}

if (isset($_FILES['education_certificate']) && !empty($_FILES['education_certificate']) && $_FILES['education_certificate']['error'] == 0 && $_FILES['education_certificate']['size'] > 0) 
{
        $uploadDir = '../upload/education_certificate';
        if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = $_FILES['education_certificate']['name'];
    $education_certificate = $uploadDir . '/' . $fileName;
    $extension = pathinfo($education_certificate, PATHINFO_EXTENSION);
    if (strtolower($extension) !== 'pdf') {
        $response["success"] = false;
        $response["message"] = "Education certificate file type must be pdf!";
        print_r(json_encode($response));
        return false;
    }
    $upload_image3 = 'upload/education_certificate/' . $fileName;
    move_uploaded_file($_FILES['education_certificate']['tmp_name'], $education_certificate);
}
    
$sql = "SELECT * FROM staffs WHERE id=" . $staff_id;
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $sql = "UPDATE staffs SET aadhar_card='$upload_image',resume='$upload_image1',photo='$upload_image2',education_certificate='$upload_image3',salary_date='$salary_date',dob='$dob' WHERE id=" . $staff_id;
    $db->sql($sql);
 
    $response['success'] = true;
    $response['message'] = "staffs documents added Successfully";
    print_r(json_encode($response));
    return false;
}
else{
    
    $response['success'] = false;
    $response['message'] ="staff not found";
    print_r(json_encode($response));
    return false;

}

?>
  

