<?php
session_start(); 
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT COUNT(*) as count FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $result = $db->getResult();

    if ($result[0]['count'] > 0) {
        $_SESSION['mobile'] = $mobile;

        $sql_query = "SELECT id FROM users WHERE mobile = '$mobile'";
        $db->sql($sql_query);
        $userData = $db->getResult();

        if (!empty($userData)) {
            $_SESSION['user_id'] = $userData[0]['id'];
        }

        echo json_encode(array('registered' => true));
    } else {
        echo json_encode(array('registered' => false));
    }
}
?>