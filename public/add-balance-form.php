<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php
 $ID = $db->escapeString($_GET['id']);
if (isset($_POST['btnAdd'])) {
        $balance = $db->escapeString(($_POST['balance']));
        $error = array();
       
        if (empty($balance)) {
            $error['balance'] = " <span class='label label-danger'>Required!</span>";
        }
       
            if (!empty($balance)) 
            {
                $datetime = date('Y-m-d H:i:s');
                $type = 'admin_credit_balance';
                $sql = "INSERT INTO transactions (`user_id`,`amount`,`datetime`,`type`)VALUES('$ID','$balance','$datetime','$type')";
                $db->sql($sql);
                 $sql_query = "UPDATE users SET balance=balance+ $balance WHERE id=$ID";
                 $db->sql($sql_query);
                 $result = $db->getResult();
                 if (!empty($result)) {
                     $result = 0;
                 } else {
                     $result = 1;
                 }
     
                 if ($result == 1) {
                     $error['add_balance'] = "<section class='content-header'>
                                                     <span class='label label-success'>Balance Added Successfully</span> </section>";
                 }else{
                    $error['add_balance'] = "<section class='content-header'>
                                                     <span class='label label-danger'>Failed</span> </section>";
                 }
                 
                 }

        }
?>
<section class="content-header">
    <h1>Add Balance <small><a href='users.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small></h1>
    <?php echo isset($error['add_balance']) ? $error['add_balance'] : ''; ?>
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
                <form name="add_balance_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-8'>
                                    <label for="exampleInputEmail1">Balance</label> <i class="text-danger asterik">*</i><?php echo isset($error['balance']) ? $error['balance'] : ''; ?>
                                    <input type="number" class="form-control" name="balance" required>
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