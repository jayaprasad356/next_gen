<?php
session_start();

// set time for session timeout
$currentTime = time() + 25200;
$expired = 3600;

// if session not set go to login page
if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

// if current time is more than session timeout back to login page
if ($currentTime > $_SESSION['timeout']) {
    session_destroy();
    header("location:index.php");
}

// destroy previous session timeout and create new one
unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');

include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/crud.php');
include_once('../includes/variables.php');
$db = new Database();
$db->connect();

        // Get the current date and time
        $date = new DateTime('now');

        // Round off to the nearest hour
        $date->modify('+' . (60 - $date->format('i')) . ' minutes');
        $date->setTime($date->format('H'), 0, 0);
    
        // Format the date and time as a string
        $date_string = $date->format('Y-m-d H:i:s');
        $currentdate = date('Y-m-d');
if (isset($_GET['table']) && $_GET['table'] == 'users') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';

    if (isset($_GET['status']) && $_GET['status'] != '') {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "status = '$status' ";
    }    
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        if (!empty($where)) {
            $where .= "AND ";
        }
        $where .= "joined_date = '$date' ";
    }
    if (isset($_GET['trail_completed']) && $_GET['trail_completed'] != '') {
        $trail_completed = $db->escapeString($fn->xss_clean($_GET['trail_completed']));
        $where .= "trail_completed = '$trail_completed' ";
    }
    if (isset($_GET['referred_by']) && $_GET['referred_by'] != '') {
        $referred_by = $db->escapeString($fn->xss_clean($_GET['referred_by']));
        $where .= "referred_by = '$referred_by' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

     if (isset($_GET['search']) && !empty($_GET['search'])) {
         $search = $db->escapeString($fn->xss_clean($_GET['search']));
         $searchCondition = "name LIKE '%$search%' OR mobile LIKE '%$search%' OR status LIKE '%$search%' OR refer_code LIKE '%$search%'";
         $where = $where ? "$where AND $searchCondition" : $searchCondition;
     }
    
     $sqlCount = "SELECT COUNT(id) as total FROM users " . ($where ? "WHERE $where" : "");
     $db->sql($sqlCount);
     $resCount = $db->getResult();
     $total = $resCount[0]['total'];
    
     $sql = "SELECT * FROM users " . ($where ? "WHERE $where" : "") . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;

    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $support_id = $row['support_id'];
        $lead_id = $row['lead_id'];

        $operate = ' <a href="edit-users.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-users.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['fcm_id'] = $row['fcm_id'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['total_orders_viewed'] = $row['total_orders_viewed'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['today_orders'] = $row['today_orders'];
        $tempRow['total_orders'] = $row['total_orders'];
        $tempRow['description'] = $row['description'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['order_earnings'] = $row['order_earnings'];
        $tempRow['hr_earnings'] = $row['hr_earnings'];
        $sql = "SELECT name FROM `staffs` WHERE id = $support_id";
        $db->sql($sql);
        $res = $db->getResult();
        $support_name = isset($res[0]['name']) ? $res[0]['name'] :"";
        $sql = "SELECT name FROM `staffs` WHERE id = $lead_id";
        $db->sql($sql);
        $res = $db->getResult();
        $lead_name = isset($res[0]['name']) ? $res[0]['name'] :"";
        $tempRow['support_name'] = $support_name;
        $tempRow['lead_name'] = $lead_name;
        $tempRow['account_num'] = $row['account_num'];
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['device_id'] = $row['device_id'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";

         $tempRow['joined_date'] = $row['joined_date'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
//challenges table goes here
if (isset($_GET['table']) && $_GET['table'] == 'challenges') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $new_format = 'Y-m-d';
        $converted_date = date($new_format, strtotime($date));
        $where .= "AND ch.`datetime` LIKE '" . $converted_date . "%' ";  
      }
      if (isset($_GET['time']) && $_GET['time'] != '') {
        $time = $db->escapeString($fn->xss_clean($_GET['time']));
        $where .= "AND TIME(ch.`datetime`) = '" . $time . "' ";  
    }
    if (isset($_GET['color_id']) && $_GET['color_id'] != '') {
        $color_id = $db->escapeString($fn->xss_clean($_GET['color_id']));
        $where .= "AND c.`id` = '" . $color_id . "' ";  
    }
    
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($fn->xss_clean($_GET['search']));
            $where .= "AND u.mobile like '%" . $search . "%' OR c.name like '%" . $search . "%' OR c.code like '%" . $search . "%' OR ch.datetime like '%" . $search . "%' ";
        }
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `colors` c ON ch.color_id = c.id LEFT JOIN `users` u ON ch.user_id = u.id WHERE ch.id IS NOT NULL ";

    $sql = "SELECT COUNT(*) as `total` FROM `challenges` ch $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
    
    $sql = "SELECT ch.id AS id,ch.*,c.name AS name,c.code AS code,u.mobile AS mobile,u.earn AS earn,ch.coins AS coins,u.name AS user_name FROM `challenges` ch $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
    $db->sql($sql);
    $res = $db->getResult();
    
    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
    
        // $operate = '<a href="edit-transaction.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        // $operate .= ' <a class="text text-danger" href="delete-transaction.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['name'] = $row['name'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['user_name'] = $row['user_name']; // Add user name to the tempRow
        $tempRow['code'] = $row['code'];
        $tempRow['coins'] = $row['coins'];
        $tempRow['datetime'] = $row['datetime'];
    
        $rows[] = $tempRow;
    }
    
    $bulkData['rows'] = $rows;
    
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'analysis') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $new_format = 'Y-m-d';
        $converted_date = date($new_format, strtotime($date));
        $where .= "AND ch.`datetime` LIKE '" . $converted_date . "%' ";  
      }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        //$where .= "AND u.mobile like '%" . $search . "%' OR c.name like '%" . $search . "%' OR c.code like '%" . $search . "%' OR ch.datetime like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    //$join = "LEFT JOIN `colors` c ON ch.color_id = c.id LEFT JOIN `users` u ON ch.user_id = u.id WHERE ch.id IS NOT NULL ";

    $sql = "SELECT COUNT(*) as `total` FROM `colors` " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM `colors`
    $where ORDER BY $sort $order LIMIT $offset, $limit";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {
        $id = $row['id'];

        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $sql = "SELECT c.id FROM `challenges` c,`users` u WHERE c.user_id = u.id AND u.earn = 0 AND c.color_id = $id AND c.datetime = '$date_string'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $tempRow['nuc'] = $num;

        $sql = "SELECT c.id FROM `challenges` c,`users` u WHERE c.user_id = u.id AND u.earn != 0 AND c.color_id = $id AND c.datetime = '$date_string'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $tempRow['tuc'] = $num;

        $sql = "SELECT SUM(c.coins) AS coins FROM `challenges` c,`users` u WHERE c.user_id = u.id AND c.color_id = $id AND c.datetime = '$date_string'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $total_coins = $res[0]['coins'] * 2;
        $tempRow['ew'] = $total_coins;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'results') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE c.name like '%" . $search . "%' OR r.date like %" . $search . "%'";
    }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);

    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);

    }        
    $join = "LEFT JOIN `colors` c ON r.color_id = c.id";

    $sql = "SELECT COUNT(*) as `total` FROM `results` r $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT r.id AS id,r.*,c.name AS name FROM `results` r $join 
          $where ORDER BY $sort $order LIMIT $offset, $limit";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;

    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {

        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['datetime'] = $row['datetime'];
        $rows[] = $tempRow;
        }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'winners') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND c.name like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR c.date like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);

    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);

    }        
    $sql = "SELECT COUNT(*) as `total` FROM `results` r " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT c.id AS id,c.coins *2 AS coins,cl.name AS name,u.mobile AS mobile,c.datetime AS date
    FROM challenges c
    JOIN colors cl ON c.color_id = cl.id
    JOIN users u ON c.user_id = u.id
    WHERE c.color_id IN (
      SELECT color_id
      FROM results
      WHERE DATE_FORMAT(results.datetime, '%Y-%m-%d') = DATE_FORMAT(c.datetime, '%Y-%m-%d')
    ) $where ORDER BY $sort $order LIMIT $offset, $limit ";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;

    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
            $tempRow['id'] = $row['id'];
            $tempRow['mobile'] = $row['mobile'];
            $tempRow['name'] = $row['name'];
            $tempRow['date'] = $row['date'];
            $tempRow['coins'] = $row['coins'];
            $rows[] = $tempRow;

    }        
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
//withdrawals table goes here
if (isset($_GET['table']) && $_GET['table'] == 'withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND w.status=$status ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($fn->xss_clean($_GET['search']));
            $where .= "AND (u.mobile LIKE '%" . $search . "%' OR u.name LIKE '%" . $search . "%') ";
        }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);

    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);

    }        
    $join = "WHERE w.user_id = u.id ";

    $sql = "SELECT COUNT(u.id) as `total` FROM `withdrawals` w,`users` u $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT w.id AS id,w.*,u.mobile,u.upi,u.account_num,u.holder_name,u.bank,u.branch,u.ifsc,u.earn,w.status AS status FROM `withdrawals` w,`users` u $join 
          $where ORDER BY $sort $order LIMIT $offset, $limit";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;

    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $amount = $row['amount'];
        $tempRow['column'] = $checkbox;
        $tempRow['id'] = $row['id'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['upi'] = $row['upi'];
        $tempRow['account_num'] = ','.$row['account_num'].',';
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $amount = $row['amount'];

        if ($amount < 250) {
            $taxRate = 0.05; // 5% tax rate
        } elseif ($amount <= 500) {
            $taxRate = 0.1; // 10% tax rate
        } elseif ($amount <= 1000) {
            $taxRate = 0.15; // 15% tax rate
        } else {
            $taxRate = 0.2; // 20% tax rate
        }
        
        $taxAmount = $amount * $taxRate;
        $pay_amount = $amount - $taxAmount;
        $tempRow['pay_amount'] = $pay_amount;
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        if($row['status']==1)
                $tempRow['status']="<p class='text text-success'>Paid</p>";        
        elseif($row['status']==0)
                 $tempRow['status']="<p class='text text-primary'>Unpaid</p>"; 
        else
               $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        $rows[] = $tempRow;
        }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//notifications table goes here
if (isset($_GET['table']) && $_GET['table'] == 'notifications') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE id like '%" . $search . "%' OR title like '%" . $search . "%' OR description like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `notifications`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM notifications " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;

    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {

        $operate = ' <a class="text text-danger" href="delete-notification.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['title'] = $row['title'];
        $tempRow['description'] = $row['description'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'daily_bonus') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR l.reason like '%" . $search . "%' OR l.id like '%" . $search . "%'  OR l.date like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR l.type like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
   
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `daily_bonus` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile  FROM `daily_bonus` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $tempRow = array();
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['coins'] = $row['coins'];
        $tempRow['datetime'] = $row['datetime'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


if (isset($_GET['table']) && $_GET['table'] == 'users_task') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR l.reason like '%" . $search . "%' OR l.id like '%" . $search . "%'  OR l.date like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR l.type like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
   
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `users_task` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile  FROM `users_task` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $tempRow = array();
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['task_id'] = $row['task_id'];
        $tempRow['result'] = $row['result'];
        $tempRow['time'] = $row['time'];
        $tempRow['datetime'] = $row['datetime'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    
    if ((isset($_GET['type']) && $_GET['type'] != '')) {
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= " AND l.type = '$type'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

       

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($fn->xss_clean($_GET['search']));
            $where .= "AND (u.mobile LIKE '%" . $search . "%' OR u.name LIKE '%" . $search . "%') ";
        }
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
   
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `transactions` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile  FROM `transactions` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $tempRow = array();
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['type'] = $row['type'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['orders'] = $row['orders'];
        $tempRow['datetime'] = $row['datetime'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'generate_coins') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    
    if ((isset($_GET['type']) && $_GET['type'] != '')) {
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= " AND l.is_scratched = '$type'";
    }
    
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= " AND l.date = '$date'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($fn->xss_clean($_GET['search']));
            $where .= "AND (u.mobile LIKE '%" . $search . "%' OR u.name LIKE '%" . $search . "%') ";
        }
        
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
   
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `generate_coins` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile  FROM `generate_coins` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {

       // $operate = '<a href="edit-scratch_cards.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
       // $operate .= ' <a class="text text-danger" href="delete-scratch_cards.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['coin_count'] = $row['coin_count'];
        $tempRow['start_time'] = $row['start_time'];
        $tempRow['end_time'] = $row['end_time'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'languages') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($_GET['search']);
        $where .= "WHERE id like '%" . $search . "%' OR title like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `languages` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM languages " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        $operate = ' <a href="edit-languages.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-languages.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
//branches table goes here
if (isset($_GET['table']) && $_GET['table'] == 'branches') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE name like '%" . $search . "%' OR short_code like '%" . $search . "%' OR min_withdrawal like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `branches`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM branches " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
         $operate = '<a href="edit-branches.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-branches.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['short_code'] = $row['short_code'];
        $tempRow['support_lan'] = $row['support_lan'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
//staffs table goes here
if (isset($_GET['table']) && $_GET['table'] == 'staffs') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND s.status='$status' ";
    }

    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($fn->xss_clean($_GET['search']));
            $where .= "AND (s.mobile LIKE '%" . $search . "%' OR s.name LIKE '%" . $search . "%') ";
        }
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
  
    $join = "LEFT JOIN `branches` b ON s.branch_id = b.id WHERE s.id IS NOT NULL ";

    $sql = "SELECT COUNT(s.id) as total FROM `staffs` s $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT s.id AS id,s.*,b.short_code AS branch FROM `staffs` s $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $staff_id = $row['id'];
        $operate = '<a href="edit-staff.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-staff.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
    
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['email'] = $row['email'];
        $tempRow['branch'] = $row['branch'];
        $sql = "SELECT id FROM `incentives` WHERE DATE(datetime) = '$currentdate' AND amount = 50 AND staff_id = $staff_id GROUP BY user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $direct_join = $num;
        $sql = "SELECT id FROM `incentives` WHERE DATE(datetime) = '$currentdate' AND amount = 7.50 AND staff_id = $staff_id GROUP BY user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $today_refer_joins = $num;

        $sql = "SELECT COUNT(id) AS total FROM `users` WHERE support_id = $staff_id AND status = 1 AND today_orders != 0";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $res[0]['total'];
        $today_active_users = $num;
        $tempRow['today_direct_joins'] = $direct_join;
        $tempRow['today_refer_joins'] = $today_refer_joins;
        $tempRow['today_active_users'] = $today_active_users;
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'staff_withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'w.id';
    $order = 'DESC';
    if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
        $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
        $where .= "AND w.staff_id = '$user_id'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND s.mobile like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "WHERE w.staff_id = s.id ";

    $sql = "SELECT COUNT(w.id) as total FROM `staff_withdrawals` w,`staffs` s $join ". $where ."";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $sql = "SELECT w.id AS id,w.*,s.name,s.mobile,s.balance,u.mobile,s.branch,s.bank_name,s.bank_account_number,s.ifsc_code FROM `staff_withdrawals` w,`staffs` s $join
                        $where ORDER BY $sort $order LIMIT $offset, $limit";
             $db->sql($sql);
        }
        else{
            $sql = "SELECT w.id AS id,w.*,s.name,s.balance,s.mobile,s.branch,s.bank_name,s.bank_account_number,s.ifsc_code FROM `staff_withdrawals` w,`staffs` s $join
                    $where ORDER BY $sort $order LIMIT $offset, $limit";
             $db->sql($sql);
        }
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = ' <a class="text text-danger" href="delete-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        // $operate .= ' <a href="edit-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['bank_account_number'] = ','.$row['bank_account_number'].',';
        $tempRow['bank_name'] = $row['bank_name'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['ifsc_code'] = $row['ifsc_code'];
        $tempRow['column'] = $checkbox;
        if($row['status']==1)
            $tempRow['status'] ="<p class='text text-success'>Paid</p>";
        elseif($row['status']==0)
            $tempRow['status']="<p class='text text-primary'>Unpaid</p>";
        else
            $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//staff transactions table goes here
if (isset($_GET['table']) && $_GET['table'] == 'staff_transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));
    if (isset($_GET['type']) && !empty($_GET['type'])){
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= "AND t.type = '$type' ";
      
    }
    if (isset($_GET['staff']) && !empty($_GET['staff'])) {
        $staff = $db->escapeString($fn->xss_clean($_GET['staff']));
        $where .= "AND s.id = '$staff' ";
    }
    
    

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND s.name like '%" . $search . "%' OR t.amount like '%" . $search . "%' OR t.id like '%" . $search . "%'  OR t.type like '%" . $search . "%' OR s.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `staffs` s ON t.staff_id = s.id WHERE t.id IS NOT NULL ";

    
    $sql = "SELECT COUNT(t.id) as total FROM `staff_transactions` t $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT t.id AS id,t.*,s.name,s.mobile FROM `staff_transactions` t $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['type'] = $row['type'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'stores') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($_GET['search']);
        $where .= "WHERE id like '%" . $search . "%' OR title like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `stores` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM stores " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        $operate = ' <a href="edit-stores.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-stores.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['min_qty'] = $row['min_qty'];
        $tempRow['max_qty'] = $row['max_qty'];
        $tempRow['per_order_cost'] = $row['per_order_cost'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//leaves table goes here
if (isset($_GET['table']) && $_GET['table'] == 'leaves') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    if ((isset($_GET['type']) && $_GET['type'] != '')) {
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= "AND l.type = '$type'";
    }
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= " AND l.date = '$date'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR l.reason like '%" . $search . "%' OR l.id like '%" . $search . "%'  OR l.date like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR l.type like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `leaves` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile,l.status AS status FROM `leaves` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {

        $operate = '<a href="edit-leave.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-leave.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['date'] = $row['date'];
        $tempRow['type'] = $row['type'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['reason'] = $row['reason'];
        if($row['status']==0){
            $tempRow['status']="<p class='text text-primary'>Pending</p>";        
        }
        elseif($row['status']==1){
            $tempRow['status']="<p class='text text-success'>Approved</p>";        
        }
        else{
            $tempRow['status']="<p class='text text-danger'>Not-Approved</p>";        
        }
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
$db->disconnect();
