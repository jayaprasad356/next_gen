<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();


$sql_query = "SELECT name,mobile FROM `users` u,`withdrawals` w WHERE u.id = w.user_id AND w.status = 1 AND u.status = 0";
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "unverified_withdrawal_user-data" . date('Ymd') . ".xls";			
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
