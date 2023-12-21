<?php
session_start();
// include_once('../api-firebase/send-email.php');
include('../includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");
date_default_timezone_set('Asia/Kolkata');


include_once('../includes/custom-functions.php');
$fn = new custom_functions;
require_once('../includes/firebase.php');
require_once ('../includes/push.php');
include_once('../includes/functions.php');
$function = new functions;

$datetime = date('Y-m-d H:i:s');

if (isset($_POST['cancel_withdrawal']) && $_POST['cancel_withdrawal'] == 1) {
    $count = 0;
    $count1 = 0;
    $error = false;

    $filename = $_FILES["upload_file"]["tmp_name"];
    $result = $fn->validate_image($_FILES["upload_file"], false);
    if (!$result) {
        $error = true;
    }

    if ($_FILES["upload_file"]["size"] > 0  && $error == false) {
        $file = fopen($filename, "r");

        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            if ($count1 != 0) {
                $w_id = trim($db->escapeString($emapData[0])); 
                $mobile = trim($db->escapeString($emapData[1]));

                $sql = "UPDATE withdrawals SET status=2 WHERE id = $w_id";
                $db->sql($sql);
                $sql = "SELECT * FROM `withdrawals` WHERE id = $w_id ";
                $db->sql($sql);

                $mobile = trim($db->escapeString($emapData[0]));
                $orders = trim($db->escapeString($emapData[1]));
                $sql = "SELECT * FROM `users` WHERE mobile = '$mobile'";
                $db->sql($sql);
                $res = $db->getResult();

                    $ID = $res[0]['id'];
                    $datetime = date('Y-m-d H:i:s');
                    $type = 'admin_orders';
                    $per_code_cost = 0.20;
                    $amount = $orders * $per_code_cost;

                
                    $sql = "INSERT INTO transactions (`user_id`,`orders`,`amount`,`datetime`,`type`)VALUES('$ID','$orders','$amount','$datetime','$type')";
                    $db->sql($sql);

                    $sql = "UPDATE `users` SET  `today_orders` = today_orders + $orders, total_orders = total_orders + $orders, orders_earnings = orders_earnings + $amount WHERE `id` = $ID";
                    $db->sql($sql);
                }
            

            $count1++;
        }

        fclose($file);

        echo "<p class='alert alert-success'>CSV file is successfully imported!</p><br>";
    } else {
        echo "<p class='alert alert-danger'>Invalid file format! Please upload data in CSV file!</p><br>";
    }
}

if (isset($_POST['delete_variant'])) {
    $v_id = $db->escapeString(($_POST['id']));
    $sql = "DELETE FROM product_variant WHERE id = $v_id";
    $db->sql($sql);
    $result = $db->getResult();
    if ($result) {
        echo 1;
    } else {
        echo 0;
    }
}


