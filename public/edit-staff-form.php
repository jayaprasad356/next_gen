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

            $name = $db->escapeString(($_POST['name']));
            $email = $db->escapeString(($_POST['email']));
            $password = $db->escapeString(($_POST['password']));
            $mobile = $db->escapeString(($_POST['mobile']));
            $bank_account_number = $db->escapeString(($_POST['bank_account_number']));
            $ifsc_code = $db->escapeString(($_POST['ifsc_code']));
            $earn = $db->escapeString(($_POST['earn']));
            $bank_name = $db->escapeString(($_POST['bank_name']));
            $branch = $db->escapeString(($_POST['branch']));
            $branch_id = $db->escapeString(($_POST['branch_id']));
            $balance = $db->escapeString(($_POST['balance']));
            $dob = $db->escapeString(($_POST['dob']));
            $salary = $db->escapeString(($_POST['salary']));
            $incentives = $db->escapeString(($_POST['incentives']));
            $error = array();

            if (!empty($branch_id) && !empty($salary)) {
                $sql_query = "UPDATE staffs SET  name='$name',email='$email',password='$password',mobile='$mobile',email='$email',bank_account_number='$bank_account_number',ifsc_code='$ifsc_code',earn='$earn',bank_name='$bank_name',branch='$branch',branch_id='$branch_id',balance='$balance',dob='$dob',salary='$salary',incentives='$incentives' WHERE id =  $ID";
                $db->sql($sql_query);
                $update_result = $db->getResult();
                if (!empty($update_result)) {
                    $update_result = 0;
                } else {
                    $update_result = 1;
                }
        
                // check update result
                if ($update_result == 1) {
                    $error['update_staff'] = " <section class='content-header'><span class='label label-success'>staff updated Successfully</span></section>";
                } else {
                    $error['update_staff'] = " <span class='label label-danger'>Failed update users</span>";
                }

            }else{
                $error['update_staff'] = " <span class='label label-danger'> All Fields Required!</span>";
            }

}



// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM staffs WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();
if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "staffs.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Staff<small><a href='staffs.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Staffs</a></small></h1>
    <small><?php echo isset($error['update_staff']) ? $error['update_staff'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form id="edit_user_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">E-mail</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Mobile Number</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Password</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="password" value="<?php echo $res[0]['password']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Salary</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="salary" value="<?php echo $res[0]['salary']; ?>" >
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Date of Birth</label><i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="dob" value="<?php echo $res[0]['dob']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Bank Name</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="bank_name" value="<?php echo $res[0]['bank_name']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Branch</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="branch" value="<?php echo $res[0]['branch']; ?>" >
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Account Number</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="bank_account_number" value="<?php echo $res[0]['bank_account_number']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">IFSC Code</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="ifsc_code" value="<?php echo $res[0]['ifsc_code']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Select Branch</label> <i class="text-danger asterik">*</i>
                                    <select id='branch_id' name="branch_id" class='form-control'>
                                           <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT id,short_code FROM `branches`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['branch_id'] ? 'selected="selected"' : '';?>><?= $value['short_code'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Balance</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Earn</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Incentives</label>
                                    <input type="text" class="form-control" name="incentives" value="<?php echo $res[0]['incentives']; ?>">
                                </div>
                        </div>
                            </div>

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>

                    </div>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<?php $db->disconnect(); ?>
