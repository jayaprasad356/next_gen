<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$currentdate = date('Y-m-d');

$join = "w.user_id = u.id AND w.status = 0"; 
$currentdate = date("Y-m-d"); 

    $sql = "SELECT w.id AS id, w.*, w.datetime, w.amount, u.mobile, u.earn, DATEDIFF('$currentdate', u.joined_date) AS history, u.bank, CONCAT(',', u.account_num, ',') AS account_num, u.ifsc, u.holder_name, u.branch
        FROM withdrawals w JOIN users u ON $join";
       $db->sql($sql);
    $developer_records = $db->getResult();

	
	$filename = "unpaid-withdrawals-data".date('Ymd') . ".xls";			
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"$filename\"");	
	$show_coloumn = false;
	if(!empty($developer_records)) {
	  foreach($developer_records as $record) {
		if(!$show_coloumn) {
		  // display field/column names in first row
		  echo implode("\t", array_keys($record)) . "\n";
		  $show_coloumn = true;
		}
		echo implode("\t", array_values($record)) . "\n";
	  }
	}
	exit;  
?>
