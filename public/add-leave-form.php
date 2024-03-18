<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

$sql = "SELECT id, name FROM categories ORDER BY id ASC";
$db->sql($sql);
$res = $db->getResult();

?>
<?php

if (isset($_POST['btnSearch'])) {

    $mobile = $db->escapeString(($_POST['mobile']));
    $error = array();
   
    if (empty($mobile)) {
        $error['mobile'] = " <span class='label label-danger'>Required!</span>";
    }
   
   if (!empty($mobile)) 
   {
    $sql = "SELECT id,name,refer_code FROM users WHERE mobile ='$mobile'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num == 1) {
        $name = $res[0]['name'];
        $refer_code = $res[0]['refer_code'];
        $user_id = $res[0]['id'];
    }

    }
    }
if (isset($_POST['btnAdd'])) {

        $date = $db->escapeString(($_POST['date']));
        $type = $db->escapeString(($_POST['type']));
        $user_id = (isset($_POST['user_id']) && !empty($_POST['user_id'])) ? trim($db->escapeString($fn->xss_clean($_POST['user_id']))) : "";
        $reason = $db->escapeString(($_POST['reason']));
        $error = array();
       
        if (empty($date)) {
            $error['date'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($type)) {
            $error['type'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($user_id)) {
            $error['user_id'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($reason)) {
            $error['reason'] = " <span class='label label-danger'>Required!</span>";
        }
       
       if (!empty($date) && !empty($reason) && !empty($type)) 
       {
            if($type=='user_leave'){
                $sql_query = "INSERT INTO leaves (type,user_id,date,reason,status)VALUES('$type','$user_id','$date','$reason',1)";
                $db->sql($sql_query);
            }
            elseif($type=='common_leave'){
                $sql_query = "INSERT INTO leaves (type,user_id,date,reason,status)VALUES('$type','','$date','$reason',1)";
                $db->sql($sql_query);
            }
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }
            if ($result == 1) {
                
                $error['add_leave'] = "<section class='content-header'>
                                                <span class='label label-success'>Leave Added Successfully</span> </section>";
            } else {
                $error['add_leave'] = " <span class='label label-danger'>Failed</span>";
           }
        }
        }
        $name = (isset($name)) ? $name : "";
        $refer_code = (isset($refer_code)) ? $refer_code : "";
        $user_id = (isset($user_id)) ? $user_id : "";
        
?>
<section class="content-header">
    <h1>Add New Leave <small><a href='leaves.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Leaves</a></small></h1>

    <?php echo isset($error['add_leave']) ? $error['add_leave'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
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
                <form url="add_leave_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                    <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Mobile</label> <i class="text-danger asterik">*</i>
                                    
                                    <input type="number" class="form-control" placeholder="Enter Mobile" name="mobile" required>
                                </div>

                            </div>
                        </div>
                    </div>
                  
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnSearch">Search</button>
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
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
                <form url="add_leave_form" method="post" enctype="multipart/form-data">
                    <input type="hidden" class="form-control" name="user_id" value = "<?php echo $user_id?>">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-12'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value = "<?php echo $name?>" required readonly>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-12'>
                                    <label for="exampleInputEmail1">Refer Code</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="refer_code" value = "<?php echo $refer_code?>" required readonly>
                                </div>

                            </div>
                        </div>
                        <br>
                       <div class="row">
                            <div class="form-group">
                                <div class='col-md-12'>
                                    <label for="exampleInputEmail1">Date</label> <i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="date" required>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-12'>
                                <label for="exampleInputEmail1">Leave Type</label><i class="text-danger asterik">*</i><br>
                                    <input class="form-check-input" type="radio" name="type" value="user_leave" checked>
                                    <label  for="exampleRadios1">User Leave</label><br>
                                    <input class="form-check-input" type="radio" name="type" value="common_leave">
                                    <label  for="exampleRadios2">Common Leave</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-12'>
                                    <label for="exampleInputEmail1">Reason</label> <i class="text-danger asterik">*</i>
                                    <textarea type="text" rows="3" class="form-control" name="reason" required></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                  
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                        <input type="reset" onClick="refreshPage()" class="btn-warning btn" value="Clear" />
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#add_leave_form').validate({

        ignore: [],
        debug: false,
        rules: {
        reason: "required",
            date: "required",
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>