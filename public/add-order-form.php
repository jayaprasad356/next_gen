<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

$sql = "SELECT id, name FROM categories ORDER BY id ASC";
$db->sql($sql);
$res = $db->getResult();
date_default_timezone_set('Asia/Kolkata');
?>
<?php
 $ID = $db->escapeString($_GET['id']);
if (isset($_POST['btnAdd'])) {
        $orders = $db->escapeString(($_POST['orders']));
        $error = array();
       
        if (empty($orders)) {
            $error['orders'] = " <span class='label label-danger'>Required!</span>";
        }
       
            if (!empty($orders)) 
            {
                $datetime = date('Y-m-d H:i:s');
                $type = 'ad_bonus';
                $per_code_cost = 0.125;
                $amount = $orders * $per_code_cost;

                $sql = "INSERT INTO transactions (`user_id`,`orders`,`amount`,`datetime`,`type`)VALUES('$ID','$orders','$amount','$datetime','$type')";
                $db->sql($sql);
                $res = $db->getResult();
            
                $sql = "UPDATE `users` SET  `today_orders` = today_orders + $orders,`total_orders` = total_orders + $orders,`earn` = earn + $amount,`balance` = balance + $amount WHERE `id` = $ID";
                $db->sql($sql);
                 $result = $db->getResult();
                 if (!empty($result)) {
                     $result = 0;
                 } else {
                     $result = 1;
                 }
     
                 if ($result == 1) {
                     $error['add_orders'] = "<section class='content-header'>
                                                     <span class='label label-success'>orders Added Successfully</span> </section>";
                 }
                 }

        }
?>
<section class="content-header">
    <h1>Add orders <small><a href='users.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small></h1>
    <?php echo isset($error['add_orders']) ? $error['add_orders'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form name="add_orders_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-8'>
                                    <label for="exampleInputEmail1">orders</label> <i class="text-danger asterik">*</i><?php echo isset($error['orders']) ? $error['orders'] : ''; ?>
                                    <input type="number" class="form-control" name="orders" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>

<?php $db->disconnect(); ?>