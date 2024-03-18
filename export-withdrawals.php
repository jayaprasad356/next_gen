<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$currentdate = date('Y-m-d');

$condition = "w.status = 1"; // Condition for verified users
$sql_query = "SELECT w.*, u.account_num, u.branch, u.bank, u.ifsc, u.holder_name FROM `withdrawals` w JOIN `users` u ON w.user_id = u.id WHERE $condition AND DATE(w.datetime) = '$currentdate'";
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "withdrawals-data" . date('Ymd') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
$show_column = false;
if (!empty($developer_records)) {
    foreach ($developer_records as $record) {
        if (!$show_column) {
            // display field/column names in first row
            echo implode("\t", array_keys($record)) . "\n";
            $show_column = true;
        }
        echo implode("\t", array_values($record)) . "\n";
    }
}
exit;
?>
