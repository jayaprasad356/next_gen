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
    

    $device_id = $db->escapeString(($_POST['device_id']));
    $joined_date = $db->escapeString($_POST['joined_date']);
    $total_orders = $db->escapeString(($_POST['total_orders']));
    $today_orders = $db->escapeString($_POST['today_orders']);
    $support_id = $db->escapeString(($_POST['support_id']));
    $branch_id = $db->escapeString(($_POST['branch_id']));
    $store_id = $db->escapeString(($_POST['store_id']));
    $total_referrals = $db->escapeString(($_POST['total_referrals']));
    $orders_time = $db->escapeString(($_POST['orders_time']));
    $worked_days = $db->escapeString(($_POST['worked_days']));
    $description = $db->escapeString(($_POST['description']));
    $order_available = $db->escapeString(($_POST['order_available']));
    $average_orders = $db->escapeString(($_POST['average_orders']));
    $description = $db->escapeString(($_POST['description']));
    $hiring_earings = $db->escapeString(($_POST['hiring_earings']));
    $orders_earnings = $db->escapeString(($_POST['orders_earnings']));
    $password = $db->escapeString(($_POST['password']));
    $min_qty = $db->escapeString(($_POST['min_qty']));
    $max_qty = $db->escapeString(($_POST['max_qty']));
    $enroll_date = $db->escapeString(($_POST['enroll_date']));

    
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
   
    if (empty($support_id)) {
        $error['update_users'] = " <span class='label label-danger'> Support Required!</span>";
    }
    if (empty($branch_id)) {
        $error['update_users'] = " <span class='label label-danger'> Branch Required!</span>";
    }
    if (empty($store_id)) {
        $error['update_users'] = " <span class='label label-danger'> Store Required!</span>";
    }
    
    
            

    if (!empty($mobile) && 
    !empty($support_id) && 
    !empty($branch_id) && 
    !empty($store_id)) {

        $refer_bonus_sent = $fn->get_value('users','refer_bonus_sent',$ID);
 
        if(!empty($enroll_date) && !empty($referred_by) && $refer_bonus_sent != 1){
           
            
            $sql_query = "SELECT * FROM users WHERE refer_code =  '$referred_by'";
            $db->sql($sql_query);
            $res = $db->getResult();
            $num = $db->numRows($res);

            
            if ($num == 1){
                $user_status = $res[0]['status'];
                $user_id = $res[0]['id'];
                if($user_status == 1){
                    $refer_orders = 500;
                    $referral_bonus = 600;

                    $sql_query = "UPDATE users SET `total_referrals` = total_referrals + 1,`total_orders` = total_orders + $refer_orders,`hiring_earings` = hiring_earings + $referral_bonus  WHERE id =  $user_id";
                    $db->sql($sql_query);
                    $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
                    $db->sql($sql_query);
                                        
                    $sql_query = "UPDATE users SET refer_bonus_sent = 1 WHERE id =  $ID";
                    $db->sql($sql_query);


                }
              
 
            }
            
        }
        $register_bonus_sent = $fn->get_value('users','register_bonus_sent',$ID);
            if (!empty($enroll_date) && $register_bonus_sent != 1 ) {
                $sql_query = "UPDATE users SET register_bonus_sent = 1 WHERE id =  $ID";
                $db->sql($sql_query);
        
                $sql_query = "UPDATE settings SET vacancies = vacancies - 1";
                $db->sql($sql_query);

                $joined_date = $date;
                if(strlen($referred_by) < 4){
                    $incentives = 50;
                }else{
                    $incentives = 7.5;
                    
                }

            }


            $sql_query = "UPDATE users SET mobile='$mobile',earn='$earn',balance='$balance',referred_by='$referred_by',refer_code='$refer_code',withdrawal_status='$withdrawal_status',min_withdrawal='$min_withdrawal',joined_date = '$joined_date', device_id='$device_id', total_orders='$total_orders', today_orders='$today_orders',status=$status,support_id='$support_id',branch_id='$branch_id',orders_time='$orders_time',worked_days = '$worked_days',blocked = '$blocked',refer_bonus_sent = '$refer_bonus_sent',description = '$description',order_available = '$order_available',store_id = '$store_id',total_referrals = '$total_referrals',average_orders = '$average_orders', description = '$description',orders_earnings = '$orders_earnings',hiring_earings = '$hiring_earings',password = '$password',min_qty = '$min_qty',max_qty = '$max_qty',enroll_date = '$enroll_date'  WHERE id =  $ID";
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

$sql_query = "SELECT * FROM users WHERE id = $ID";
$db->sql($sql_query);
$res = $db->getResult();

$sql_query = "SELECT * FROM users WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

$refer_code = $res[0]['refer_code'];
$referred_by = isset($_POST['referred_by']) ? $_POST['referred_by'] : $res[0]['referred_by'];



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
                                <a class="btn btn-block btn-primary" href="add-order.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add Orders</a>
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
                               </div>
                             </div>
                          <br>
                          <div class="row">
                              <div class="form-group">
                                  <div class="col-md-5">
                                            <label for="exampleInputEmail1">Description</label> <i class="text-danger asterik">*</i><?php echo isset($error['description']) ? $error['description'] : ''; ?>
                                            <textarea  type="text" rows="1" class="form-control" name="description"><?php echo $res[0]['description']?></textarea>
                                    </div>
                               </div>
                             </div>
                          <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1"> Refer Code</label> <i class="text-danger asterik">*</i><?php echo isset($error['refer_code']) ? $error['refer_code'] : ''; ?>
                                    <input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>">
                                </div>
                                
                            <div class="col-md-3">
                                      <label for="exampleInputEmail1">Enroll Date</label> <i class="text-danger asterik">*</i><?php echo isset($error['enroll_date']) ? $error['enroll_date'] : ''; ?>
                                       <?php
                                         if (empty($res[0]['enroll_date']) || $res[0]['enroll_date'] == '0000-00-00') {
                                          ?>
                                      <input type="date" class="form-control" name="enroll_date" value="">
                                       <?php
                                        } else {
                                        ?>
                                       <input type="date" class="form-control" name="enroll_date" value="<?php echo $res[0]['enroll_date']; ?>" readonly>
                                        <?php
                                          }
                                         ?>
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
                            <div class="col-md-4">
                                    <label for="exampleInputEmail1">Earn</label> <i class="text-danger asterik">*</i><?php echo isset($error['earn']) ? $error['earn'] : ''; ?>
                                    <input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1"> Balance</label> <i class="text-danger asterik">*</i><?php echo isset($error['balance']) ? $error['balance'] : ''; ?>
                                    <input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Worked Days</label> <i class="text-danger asterik">*</i><?php echo isset($error['worked_days']) ? $error['worked_days'] : ''; ?>
                                    <input type="number" class="form-control" name="worked_days" value="<?php echo $res[0]['worked_days']; ?>">
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
                                <div class="form-group col-md-3">
                                    <label for="exampleInputEmail1">Select Stores</label> <i class="text-danger asterik">*</i>
                                    <select id='store_id' name="store_id" class='form-control'>
                                                <?php
                                                $sql = "SELECT * FROM `stores`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['store_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
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
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">orders Time</label> <i class="text-danger asterik">*</i><?php echo isset($error['orders_time']) ? $error['orders_time'] : ''; ?>
                                    <input type="number" class="form-control" name="orders_time" value="<?php echo $res[0]['orders_time']; ?>">
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
                    </div>
                    <br>
                        <div class="row">
                        <div class="col-md-3">
                                    <label for="exampleInputEmail1">Total Referrals</label> <i class="text-danger asterik">*</i><?php echo isset($error['total_referrals']) ? $error['total_referrals'] : ''; ?>
                                    <input type="text" class="form-control" name="total_referrals" value="<?php echo $res[0]['total_referrals']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Average Orders</label> <i class="text-danger asterik">*</i><?php echo isset($error['average_orders']) ? $error['average_orders'] : ''; ?>
                                    <input type="number" class="form-control" name="average_orders" value="<?php echo $res[0]['average_orders']; ?>">
                                </div>
                                <div class="col-md-3">
                                        <label for="exampleInputEmail1"> Password</label> <i class="text-danger asterik">*</i><?php echo isset($error['password']) ? $error['password'] : ''; ?>
                                        <input type="text" class="form-control" name="password" value="<?php echo $res[0]['password']; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1"> Hiring Earnings</label> <i class="text-danger asterik">*</i><?php echo isset($error['hiring_earings']) ? $error['hiring_earings'] : ''; ?>
                                        <input type="number" class="form-control" name="hiring_earings" value="<?php echo $res[0]['hiring_earings']; ?>">
                                    </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Orders Earnings</label> <i class="text-danger asterik">*</i><?php echo isset($error['orders_earnings']) ? $error['orders_earnings'] : ''; ?>
                                    <input type="number" class="form-control" name="orders_earnings" value="<?php echo $res[0]['orders_earnings']; ?>">
                                </div>
                                <div class="col-md-3">
                                        <label for="exampleInputEmail1">Min Qty</label> <i class="text-danger asterik">*</i><?php echo isset($error['min_qty']) ? $error['min_qty'] : ''; ?>
                                        <input type="number" class="form-control" name="min_qty" value="<?php echo $res[0]['min_qty']; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="exampleInputEmail1">Max Qty</label> <i class="text-danger asterik">*</i><?php echo isset($error['max_qty']) ? $error['max_qty'] : ''; ?>
                                        <input type="number" class="form-control" name="max_qty" value="<?php echo $res[0]['max_qty']; ?>">
                                    </div>
                            </div>
                         <br>
                     
                         </div><!-- /.box-body -->
                         <br>
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


