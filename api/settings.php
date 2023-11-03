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
     
$sql = "SELECT * FROM settings WHERE id=1";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);

if ($num >= 1){
    $rows = array();
    $temp = array();
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['withdrawal_status'] = $row['withdrawal_status'];
        $temp['contact_us'] = $row['contact_us'];
        $temp['min_withdrawal'] = $row['min_withdrawal'];
        $temp['image'] = DOMAIN_URL . $row['image'];
        $temp['watch_ad_status'] = $row['watch_ad_status'];
        $temp['offer_image'] = $row['offer_image'];
        $temp['refer_bonus'] = $row['refer_bonus'];
        $temp['whatspp_group_link'] = $row['whatspp_group_link'];
        $temp['a1_job_video'] = $row['a1_job_video'];
        $temp['post_video_url'] = DOMAIN_URL .$row['post_video_url'];
        $temp['post_video_details'] = DOMAIN_URL . $row['post_video_details'];
        $temp['a1_job_details'] = $row['a1_job_details'];
        $temp['a2_job_details'] = $row['a2_job_details'];
        $temp['a2_job_video'] = $row['a2_job_video'];
        $temp['a1_purchase_link'] = $row['a1_purchase_link'];
        $temp['a2_purchase_link'] = $row['a2_purchase_link'];
        $temp['reward_orders_details'] = $row['reward_orders_details'];
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Settings Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Data Not found";
    print_r(json_encode($response));

}
