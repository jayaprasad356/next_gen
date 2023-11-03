<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();

$currentdate = date('Y-m-d');
$sql_query = "SELECT * FROM `users`"; // Fetch all users without any condition
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "AllUsers-data" . date('Ymd') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
$show_column = false;

if (!empty($developer_records)) {
    foreach ($developer_records as $record) {
        if (!$show_column) {
            // display field/column names in the first row
            echo implode("\t", array_keys($record)) . "\n";
            $show_column = true;
        }
        echo implode("\t", array_values($record)) . "\n";
    }
}

exit;
?>
