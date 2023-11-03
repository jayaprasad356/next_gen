<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>

<?php
if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
} else {
    // $ID = "";
    return false;
    exit(0);
}

if (isset($_POST['btnEdit'])) {

    $datetime = date('Y-m-d H:i:s');
    $date = date('Y-m-d');
    $mobile = $db->escapeString($_POST['mobile']);

    $earn = $db->escapeString($_POST['earn']);
    $balance = $db->escapeString($_POST['balance']);
    $referred_by = $db->escapeString($_POST['referred_by']);
    $refer_code= $db->escapeString($_POST['refer_code']);
    $withdrawal_status = $db->escapeString($_POST['withdrawal_status']);
    $blocked = $db->escapeString($_POST['blocked']);
    $refer_bonus_sent = $db->escapeString($_POST['refer_bonus_sent']);
    $min_withdrawal = $db->escapeString($_POST['min_withdrawal']);
    $status = $db->escapeString($_POST['status']);
    
    $account_num = $db->escapeString(($_POST['account_num']));
    $holder_name = $db->escapeString(($_POST['holder_name']));
    $bank = $db->escapeString(($_POST['bank']));
    $branch = $db->escapeString(($_POST['branch']));
    $ifsc = $db->escapeString(($_POST['ifsc']));
    $device_id = $db->escapeString(($_POST['device_id']));
    $joined_date = $db->escapeString($_POST['joined_date']);
    $total_orders = $db->escapeString(($_POST['total_orders']));
    $today_orders = $db->escapeString($_POST['today_orders']);
    $lead_id = $db->escapeString(($_POST['lead_id']));
    $support_id = $db->escapeString(($_POST['support_id']));
    $branch_id = $db->escapeString(($_POST['branch_id']));


    $plan = $db->escapeString(($_POST['plan']));
    $plan_type = $db->escapeString(($_POST['plan_type']));
    $total_referrals = $db->escapeString(($_POST['total_referrals']));
    $orders_time = $db->escapeString(($_POST['orders_time']));
    $orders_cost = isset($_POST['orders_cost']) ? $db->escapeString($_POST['orders_cost']) : 0;
    $old_plan = $db->escapeString(($_POST['old_plan']));
    $worked_days = $db->escapeString(($_POST['worked_days']));
    $description = $db->escapeString(($_POST['description']));
    $ratings = $db->escapeString(($_POST['ratings']));
    $order_available = $db->escapeString(($_POST['order_available']));

    
    $error = array();

    if (empty($mobile)) {
        $error['mobile'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($upi)) {
        $error['upi'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($balance)) {
        $error['balance'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($earn)) {
        $error['earn'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($languages)) {
        $error['languages'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($lead_id)) {
        $error['update_users'] = " <span class='label label-danger'> Lead Required!</span>";
    }
    if (empty($support_id)) {
        $error['update_users'] = " <span class='label label-danger'> Support Required!</span>";
    }
    if (empty($branch_id)) {
        $error['update_users'] = " <span class='label label-danger'> Branch Required!</span>";
    }
    
    
            

    if (!empty($mobile) && !empty($lead_id)  && 
    !empty($support_id) && 
    !empty($branch_id)) {

        $refer_bonus_sent = $fn->get_value('users','refer_bonus_sent',$ID);
 
        if($status == 1 && !empty($referred_by) && $refer_bonus_sent != 1){
           
            
            $sql_query = "SELECT * FROM users WHERE refer_code =  '$referred_by'";
            $db->sql($sql_query);
            $res = $db->getResult();
            $num = $db->numRows($res);

            
            if ($num == 1){
                $user_status = $res[0]['status'];
                $user_id = $res[0]['id'];
                $user_current_refers = $res[0]['current_refers'];
                $user_target_refers = $res[0]['target_refers'];
                $user_old_plan = $res[0]['old_plan'];
                $user_plan = $res[0]['plan'];
                $join = '';
                if($user_status == 1){
                    if($plan == 'A2'){
                        //$join = ',`current_refers` = current_refers + 1';
                        if($user_plan == 'A2'){
                            $referral_bonus = 300;
                            $sql_query = "INSERT INTO premium_refer_bonus (user_id,refer_user_id,status,amount,datetime)VALUES($user_id,$ID,0,700,'$datetime')";
                            $db->sql($sql_query);
    
                        }else{
                            $referral_bonus = 500;
                        
                        }

                    }else{
                        $today_orders = 0;
                        $total_orders = 0;
                        if($user_old_plan == 1){
                            $referral_bonus = 150;

                        }else{
                            $orders = 1200;
                            $referral_bonus = 150;
                            //$join = ',`today_orders` = today_orders + $orders,`total_orders` = total_orders + $orders';
                            
                            
                        }
                        

                    }
                    if($plan == 'A1' && $user_old_plan == 0){
                        $sql_query = "UPDATE users SET `total_referrals` = total_referrals + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus ,`today_orders` = today_orders + $orders,`total_orders` = total_orders + $orders WHERE id =  $user_id";
                        $db->sql($sql_query);
                        $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type,orders)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus',$orders)";
                        $db->sql($sql_query);

                    }else{
                        $sql_query = "UPDATE users SET `total_referrals` = total_referrals + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus  WHERE id =  $user_id";
                        $db->sql($sql_query);
                        $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
                        $db->sql($sql_query);

                    }

                                        

    
                    $sql_query = "UPDATE users SET refer_bonus_sent = 1 WHERE id =  $ID";
                    $db->sql($sql_query);


                    if($user_current_refers >= $user_target_refers && $user_plan == 'A1' && $old_plan == 1){
                        $sql_query = "SELECT id FROM transactions WHERE type =  'target_bonus' AND user_id = $user_id";
                        $db->sql($sql_query);
                        $res = $db->getResult();
                        $num = $db->numRows($res);
                        if ($num == 0){
                            $sql_query = "UPDATE users SET `earn` = earn + 500,`balance` = balance + 500 WHERE id =  $user_id";
                            $db->sql($sql_query);
                            $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,500,'$datetime','target_bonus')";
                            $db->sql($sql_query);

                        }

                        $sql = "SELECT * FROM monthly_target WHERE user_id = $user_id AND status = 0";
                        $db->sql($sql);
                        $res = $db->getResult();
                        $num = $db->numRows($res);
                        if ($num >= 1){
                            $user_premium_wallet = $res[0]['premium_wallet'];
                            $monthly_id = $res[0]['id'];
                            $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'premium_wallet','$datetime',$user_premium_wallet)";
                            $db->sql($sql);
                            $sql = "UPDATE users SET current_refers = 0,target_refers = 5,balance= balance + $user_premium_wallet,earn = earn + $user_premium_wallet,premium_wallet = premium_wallet - $user_premium_wallet WHERE id=" . $user_id;
                            $db->sql($sql);
                            $sql = "UPDATE monthly_target SET status = 1 WHERE id=" . $monthly_id;
                            $db->sql($sql);
            
                        }


                    }

                }
              
 
            }
            
        }
        $register_bonus_sent = $fn->get_value('users','register_bonus_sent',$ID);
            if ($status == 1 && $register_bonus_sent != 1 ) {
                $sql_query = "UPDATE users SET register_bonus_sent = 1 WHERE id =  $ID";
                $db->sql($sql_query);
        
                $joined_date = $date;
                $today_orders = 0;
                $total_orders = 0;
                $premium_wallet = 0;
                $current_refers = 0;
                $target_refers = 0;
               

                if(strlen($referred_by) < 4){
                    $incentives = 50;
                }else{
                    $incentives = 7.5;
                    
                }

                $sql_query = "UPDATE staffs SET incentives = incentives + $incentives,earn = earn + $incentives,balance = balance + $incentives,supports = supports + 1 WHERE id =  $support_id";
                $db->sql($sql_query);
    
                $sql_query = "UPDATE staffs SET incentives = incentives + $incentives,earn = earn + $incentives,balance = balance + $incentives,leorders = leorders + 1 WHERE id =  $lead_id";
                $db->sql($sql_query);
                
                $sql_query = "INSERT INTO incentives (user_id,staff_id,amount,datetime,type)VALUES($ID,$support_id,$incentives,'$datetime','support')";
                $db->sql($sql_query);
    
                $sql_query = "INSERT INTO incentives (user_id,staff_id,amount,datetime,type)VALUES($ID,$lead_id,$incentives,'$datetime','lead')";
                $db->sql($sql_query);
    
                $sql_query = "INSERT INTO staff_transactions (staff_id,amount,datetime,type)VALUES($support_id,$incentives,'$datetime','incentives')";
                $db->sql($sql_query);
    
                $sql_query = "INSERT INTO staff_transactions (staff_id,amount,datetime,type)VALUES($lead_id,$incentives,'$datetime','incentives')";
                $db->sql($sql_query);

            }
            if($plan == 'A1'){
                $min_withdrawal = 150;
                $orders_cost = 0.125;
                $orders_time = 20;
            }else{
                $min_withdrawal = 150;
            }

            if($plan == 'A2' && $plan_type == 'shift'){
                $current_refers = 0;
                $target_refers = 0;
                $earn = 0;
                $joined_date = $date;
                $today_orders = 0;
                $total_orders = 0;
                $total_referrals = 0;
                //$premium_wallet = 0;

            }
            if($plan == 'A1' && $plan_type == 'new_plan'){
                $orders_cost = 0.125;
                $current_refers = 0;
                $target_refers = 0;
                $earn = 0;
                $joined_date = $date;
                $today_orders = 0;
                $total_orders = 0;
                $total_referrals = 0;
                $premium_wallet = 0;
                $old_plan = 0;
                $orders_time = 20;
            }
    
            $sql_query = "UPDATE users SET mobile='$mobile',earn='$earn',balance='$balance',referred_by='$referred_by',refer_code='$refer_code',withdrawal_status='$withdrawal_status',min_withdrawal='$min_withdrawal',joined_date = '$joined_date',account_num='$account_num', holder_name='$holder_name', bank='$bank', branch='$branch', ifsc='$ifsc', device_id='$device_id', total_orders='$total_orders', today_orders='$today_orders',status=$status,lead_id='$lead_id',support_id='$support_id',branch_id='$branch_id',plan = '$plan',total_referrals = $total_referrals,orders_time='$orders_time',orders_cost='$orders_cost',old_plan = '$old_plan',worked_days = '$worked_days',blocked = '$blocked',refer_bonus_sent = '$refer_bonus_sent',description = '$description',ratings = '$ratings',order_available = '$order_available' WHERE id = $ID";
            $db->sql($sql_query);
            $update_result = $db->getResult();
    
            if (!empty($update_result)) {
                $update_result = 0;
            } else {
                $update_result = 1;
            }
    
            // check update result
            if ($update_result == 1) {
                $error['update_users'] = " <section class='content-header'><span class='label label-success'>User Details updated Successfully</span></section>";
            } else {
                $error['update_users'] = " <span class='label label-danger'>Failed to update</span>";
            }
        }
    }


 
$data = array();

$sql_query = "SELECT *, DATE_FORMAT(joined_date, '%Y-%m-%d') AS joined_date FROM users WHERE id = $ID";
$db->sql($sql_query);
$res = $db->getResult();

$sql_query = "SELECT * FROM users WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

$refer_code = $res[0]['refer_code'];
$referred_by = isset($_POST['referred_by']) ? $_POST['referred_by'] : $res[0]['referred_by'];

$refer_name = '';
$refer_mobile = '';

if (!empty($referred_by)) {
    $sql_query = "SELECT name, mobile FROM users WHERE refer_code = '$referred_by'";
    $db->sql($sql_query);
    $result = $db->getResult();
    if (!empty($result)) {
        $refer_name = isset($result[0]['name']) ? $result[0]['name'] : '';
        $refer_mobile = isset($result[0]['mobile']) ? $result[0]['mobile'] : '';
    }
}


if (isset($_POST['btncheck'])) {

    $refer_code = $res[0]['refer_code'];
    $referred_by = isset($_POST['referred_by']) ? $_POST['referred_by'] : $res[0]['referred_by'];
    
    $refer_name = '';
    $refer_mobile = '';
    
    if (!empty($referred_by)) {
        $sql_query = "SELECT name, mobile FROM users WHERE refer_code = '$referred_by'";
        $db->sql($sql_query);
        $result = $db->getResult();
        if (!empty($result)) {
            $refer_name = isset($result[0]['name']) ? $result[0]['name'] : '';
            $refer_mobile = isset($result[0]['mobile']) ? $result[0]['mobile'] : '';
        }
    }
}

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "users.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Users<small><a href='users.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to users</a></small></h1>
    <small><?php echo isset($error['update_users']) ? $error['update_users'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-11">

            <!-- general form elements -->
            <div class="box box-primary">
               <div class="box-header with-border">
                           <div class="form-group col-md-3">
                                <h4 class="box-title"> </h4>
                                <a class="btn btn-block btn-primary" href="add-ad.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add orders</a>
                            </div>
                             <div class="form-group col-md-3">
                                <h4 class="box-title"> </h4>
                                <a class="btn btn-block btn-success" href="add-balance.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i>  Add Balance</a>
                            </div> 
                </div>
                <!-- /.box-header -->
                <form id="edit_project_form" method="post" enctype="multipart/form-data">
                <input type="hidden" class="form-control" name="total_referrals" value="<?php echo $res[0]['total_referrals']; ?>">
                <div class="box-body">
                        <div class="row">
                              <div class="form-group">
                              <div class="col-md-3">
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i<?php echo isset($error['name']) ? $error['name'] : ''; ?>>
                                     <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                  </div>
                                 <div class="col-md-3">
                                    <label for="exampleInputEmail1"> Mobile Number</label> <i class="text-danger asterik">*</i<?php echo isset($error['mobile']) ? $error['mobile'] : ''; ?>>
                                     <input type="text" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
                                  </div>
                               <div class="col-md-3">
                                    <label for="exampleInputEmail1"> Refered By</label> <i class="text-danger asterik">*</i<?php echo isset($error['referred_by']) ? $error['referred_by'] : ''; ?>>
                                    <input type="text" class="form-control" name="referred_by" value="<?php echo $res[0]['referred_by']; ?>">
                                 </div>  
                               <div class="col-md-2">
                                    <label for="exampleInputEmail1">Check Button</label><i class="text-danger asterisk">*</i>
                                    <button type="submit" class="btn btn-danger"  name="btncheck">Check</button>
                                  </div>
                               </div>
                             </div>
                          <br>
                          <div class="row">
                              <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1"> Refer Name</label><i class="text-danger asterisk">*</i>
                                    <input type="text" class="form-control" name="refer_name" value="<?php echo $refer_name; ?>" readonly>
                                 </div>
                               <div class="col-md-3">
                                    <label for="exampleInputEmail1"> Refer Mobile</label><i class="text-danger asterisk">*</i>
                                    <input type="text" class="form-control" name="refer_mobile" value="<?php echo $refer_mobile; ?>" readonly>
                                  </div>
                                  <div class="col-md-5">
                                            <label for="exampleInputEmail1">Description</label> <i class="text-danger asterik">*</i><?php echo isset($error['description']) ? $error['description'] : ''; ?>
                                            <textarea  type="text" rows="3" class="form-control" name="description"><?php echo $res[0]['description']?></textarea>
                                    </div>
                               </div>
                             </div>
                          <br>
                        <div class="row">
                            <div class="form-group">
                            <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Plan</label> <i class="text-danger asterik">*</i>
                                    <select id='plan' name="plan" class='form-control'>
                                     <option value='A1' <?php if ($res[0]['plan'] == 'A1') echo 'selected'; ?>>A1</option>
                                      <option value='A2' <?php if ($res[0]['plan'] == 'A2') echo 'selected'; ?>>A2</option>
                                      
                                    </select>
                            </div>

                                <div class="col-md-4">
                                    <label for="exampleInputEmail1"> Refer Code</label> <i class="text-danger asterik">*</i><?php echo isset($error['refer_code']) ? $error['refer_code'] : ''; ?>
                                    <input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Select Lead</label> <i class="text-danger asterik">*</i>
                                    <select id='lead_id' name="lead_id" class='form-control' style="background-color: #7EC8E3">
                                           <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `staffs`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['lead_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Select Support</label> <i class="text-danger asterik">*</i>
                                    <select id='support_id' name="support_id" class='form-control' style="background-color: #7EC8E3">
                                             <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `staffs`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['support_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                            <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Select Branch</label> <i class="text-danger asterik">*</i>
                                    <select id='branch_id' name="branch_id" class='form-control'>
                                           <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `branches`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['branch_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                            <div class="form-group col-md-4">
                                    <label class="control-label">Status</label><i class="text-danger asterik">*</i><br>
                                    <div id="status" class="btn-group">
                                        <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Not-verified
                                        </label>
                                        <label class="btn btn-success" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Verified
                                        </label>
                                        <label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="2" <?= ($res[0]['status'] == 2) ? 'checked' : ''; ?>> Blocked
                                        </label>
                                    </div>
                                </div>
                        </div>
                        <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                    </div>
                    <hr>
                    <br>
                        <div class="row">
                            <div class="form-group">
                            <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Account Number</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="account_num" value="<?php echo $res[0]['account_num']; ?>">
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Holder Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="holder_name" value="<?php echo $res[0]['holder_name']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                            <div class="col-md-4">
                                    <label for="exampleInputEmail1">IFSC</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="ifsc" value="<?php echo $res[0]['ifsc']; ?>">
                                </div>
                                <div class="col-md-4">
                                <label for="exampleInputEmail1">Bank</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="bank" value="<?php echo $res[0]['bank']; ?>">
                                </div>
                                <div class="col-md-4">
                                <label for="exampleInputEmail1">Branch</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="branch" value="<?php echo $res[0]['branch']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                            <div class="col-md-4">
                                    <label for="exampleInputEmail1">Earn</label> <i class="text-danger asterik">*</i><?php echo isset($error['earn']) ? $error['earn'] : ''; ?>
                                    <input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1"> Balance</label> <i class="text-danger asterik">*</i><?php echo isset($error['balance']) ? $error['balance'] : ''; ?>
                                    <input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Plan Type</label> <i class="text-danger asterik">*</i>
                                    <select id='plan_type' name="plan_type" class='form-control'>
                                     <option value='' >None</option>
                                      <option value='shift' >Shift</option>
                                      <option value='new_plan' >New A1 Plan</option>
                                      
                                    </select>
                            </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Withdrawal Status</label><br>
                                    <input type="checkbox" id="withdrawal_button" class="js-switch" <?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="withdrawal_status" name="withdrawal_status" value="<?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 1 : 0 ?>">
                                </div>
                                <div class="col-md-3">
                                <label for="exampleInputEmail1">Total orders</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="total_orders" value="<?php echo $res[0]['total_orders']; ?>">
                                </div>
                                <div class="col-md-3">
                                <label for="exampleInputEmail1">Today orders</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="today_orders" value="<?php echo $res[0]['today_orders']; ?>">
                                </div>
                                </div>
                        <br>
                        <div class="row">
                        <div class="col-md-3">
                                    <label for="exampleInputEmail1">Joined Date</label><i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="joined_date" value="<?php echo $res[0]['joined_date']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Min Withdrawal</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="min_withdrawal" value="<?php echo $res[0]['min_withdrawal']; ?>">
                                </div>
                                    <div class="col-md-3">
                                    <label for="exampleInputEmail1">Device Id</label> <i class="text-danger asterik">*</i><?php echo isset($error['device_id']) ? $error['device_id'] : ''; ?>
                                    <input type="text" class="form-control" name="device_id" value="<?php echo $res[0]['device_id']; ?>">
                                </div>
						 </div>  
                                 
                                 <br>
                        <div class="row">
                        <div class="col-md-3">
                                    <label for="exampleInputEmail1">Old Plan</label> <i class="text-danger asterik">*</i><?php echo isset($error['old_plan']) ? $error['old_plan'] : ''; ?>
                                    <input type="text" class="form-control" name="old_plan" value="<?php echo $res[0]['old_plan']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">orders Time</label> <i class="text-danger asterik">*</i><?php echo isset($error['orders_time']) ? $error['orders_time'] : ''; ?>
                                    <input type="number" class="form-control" name="orders_time" value="<?php echo $res[0]['orders_time']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">orders Cost</label> <i class="text-danger asterik">*</i><?php echo isset($error['orders_cost']) ? $error['orders_cost'] : ''; ?>
                                    <input type="number" class="form-control" name="orders_cost" value="<?php echo $res[0]['orders_cost']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Worked Days</label> <i class="text-danger asterik">*</i><?php echo isset($error['worked_days']) ? $error['worked_days'] : ''; ?>
                                    <input type="number" class="form-control" name="worked_days" value="<?php echo $res[0]['worked_days']; ?>">
                                </div>
                            </div>
                             <br>
                            <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Blocked</label><br>
                                    <input type="checkbox" id="blocked_button" class="js-switch" <?= isset($res[0]['blocked']) && $res[0]['blocked'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="blocked" name="blocked" value="<?= isset($res[0]['blocked']) && $res[0]['blocked'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Refer Bonus Sent</label><br>
                                    <input type="checkbox" id="refer_button" class="js-switch" <?= isset($res[0]['refer_bonus_sent']) && $res[0]['refer_bonus_sent'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="refer_bonus_sent" name="refer_bonus_sent" value="<?= isset($res[0]['refer_bonus_sent']) && $res[0]['refer_bonus_sent'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Order Available</label><br>
                                    <input type="checkbox" id="order_button" class="js-switch" <?= isset($res[0]['order_available']) && $res[0]['order_available'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="order_available" name="order_available" value="<?= isset($res[0]['order_available']) && $res[0]['order_available'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Ratings</label> <i class="text-danger asterik">*</i><?php echo isset($error['ratings']) ? $error['ratings'] : ''; ?>
                                    <input type="text" class="form-control" name="ratings" value="<?php echo $res[0]['ratings']; ?>">
                                </div>
                               
                    </div><!-- /.box-body -->
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>
<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script>
    var changeCheckbox = document.querySelector('#withdrawal_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#withdrawal_status').val(1);

        } else {
            $('#withdrawal_status').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#blocked_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#blocked').val(1);

        } else {
            $('#blocked').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#refer_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#refer_bonus_sent').val(1);

        } else {
            $('#refer_bonus_sent').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#order_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#order_available').val(1);

        } else {
            $('#order_available').val(0);
        }
    };
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>


