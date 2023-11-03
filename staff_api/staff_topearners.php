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
date_default_timezone_set('Asia/Kolkata');

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "Staff Id is Empty";
    print_r(json_encode($response));
    return false;
}
$date = date('Y-m-d');
$staff_id = $db->escapeString($_POST['staff_id']);
$sql = "SELECT s.id,s.name AS name,b.name AS branch_name,s.incentives FROM staffs s,branches b WHERE s.branch_id = b.id AND s.incentives != 0 AND s.staff_role_id != 1 AND s.staff_role_id != 2 ORDER BY s.incentives DESC LIMIT 5";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $staff_id = $row['id'];
        $temp['name'] = $row['name'];
        $temp['branch_name'] = $row['branch_name'];
        $sql = "SELECT COALESCE(SUM(amount),0) AS incentives FROM incentives WHERE staff_id = $staff_id AND YEAR(datetime) = YEAR('$date') AND WEEK(datetime) = WEEK('$date')";
        $db->sql($sql);
        $res = $db->getResult();
        $temp['incentives'] = $res[0]['incentives'];


        
    
        
        $rows[] = $temp;
    }
    usort($rows, function ($a, $b) {
        return $b['incentives'] - $a['incentives'];
    });

    $response['success'] = true;
    $response['message'] = "Incentive Details Fetched Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));

} else {
    $response['success'] = false;
    $response['message'] = "Staff Not Found";
    print_r(json_encode($response));
}

?>