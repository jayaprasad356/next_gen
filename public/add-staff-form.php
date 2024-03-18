<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php
if (isset($_POST['btnAdd'])) {

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
    
        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($email)) {
            $error['email'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($password)) {
            $error['password'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($mobile)) {
            $error['mobile'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($bank_account_number)) {
            $error['bank_account_number'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($ifsc_code)) {
            $error['ifsc_code'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($earn)) {
            $error['earn'] = " <span class='label label-danger'> Required!</span>";
        }
        if (empty($bank_name)) {
            $error['bank_name'] = " <span class='label label-danger'>  Required!</span>";
        }
        if (empty($branch)) {
            $error['branch'] = " <span class='label label-danger'>  Required!</span>";
        }
        if (empty($branch_id)) {
            $error['branch_id'] = " <span class='label label-danger'>  Required!</span>";
        }
        if (empty($balance)) {
            $error['balance'] = " <span class='label label-danger'>  Required!</span>";
        }
        if (empty($dob)) {
            $error['dob'] = " <span class='label label-danger'>  Required!</span>";
        }
        if (empty($salary)) {
            $error['salary'] = " <span class='label label-danger'>  Required!</span>";
        }
        if (empty($incentives)) {
            $error['incentives'] = " <span class='label label-danger'>  Required!</span>";
        }
   
    if (!empty($name) && !empty($email) && !empty($password)&& !empty($mobile) && !empty($bank_account_number) && !empty($ifsc_code)&& !empty($earn) && !empty($bank_name) && !empty($branch)&& !empty($branch_id) && !empty($balance) && !empty($dob)&& !empty($salary) && !empty($incentives)) 
    {
           
            $sql_query = "INSERT INTO staffs (name,email,password,mobile,bank_account_number,ifsc_code,earn,bank_name,branch,branch_id,balance,dob,salary,incentives)VALUES('$name','$email','$password','$mobile','$bank_account_number','$ifsc_code','$earn','$bank_name','$branch','$branch_id','$balance','$dob','$salary','$incentives')";
            $db->sql($sql_query);
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }
            if ($result == 1) {
                
                $error['add_staff'] = "<section class='content-header'>
                                                <span class='label label-success'>Staffs Added Successfully</span> </section>";
            } else {
                $error['add_staff'] = " <span class='label label-danger'>Failed</span>";
        }
        }
}
?>
    
<section class="content-header">
    <h1>Add New Staff <small><a href='staffs.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Staff</a></small></h1>

    <?php echo isset($error['add_staff']) ? $error['add_staff'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
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
                <form id="add_staff_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i> <i class="text-danger asterik">*</i><?php echo isset($error['name']) ? $error['name'] : ''; ?>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">E-mail</label><i class="text-danger asterik">*</i><?php echo isset($error['email']) ? $error['email'] : ''; ?>
                                    <input type="text" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Mobile Number</label><i class="text-danger asterik">*</i><?php echo isset($error['mobile']) ? $error['mobile'] : ''; ?>
                                    <input type="number" class="form-control" name="mobile" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Password</label><i class="text-danger asterik">*</i><?php echo isset($error['password']) ? $error['password'] : ''; ?>
                                    <input type="text" class="form-control" name="password">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Salary</label><i class="text-danger asterik">*</i><?php echo isset($error['salary']) ? $error['salary'] : ''; ?>
                                    <input type="text" class="form-control" name="salary">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Date of Birth</label><i class="text-danger asterik">*</i><?php echo isset($error['dob']) ? $error['dob'] : ''; ?>
                                    <input type="date" class="form-control" name="dob">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Bank Name</label><i class="text-danger asterik">*</i><?php echo isset($error['bank_name']) ? $error['bank_name'] : ''; ?>
                                    <input type="text" class="form-control" name="bank_name">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Branch</label><i class="text-danger asterik">*</i><?php echo isset($error['branch']) ? $error['branch'] : ''; ?>
                                    <input type="text" class="form-control" name="branch">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Account Number</label><i class="text-danger asterik">*</i><?php echo isset($error['bank_account_number']) ? $error['bank_account_number'] : ''; ?>
                                    <input type="text" class="form-control" name="bank_account_number">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">IFSC Code</label><i class="text-danger asterik">*</i><?php echo isset($error['ifsc_code']) ? $error['ifsc_code'] : ''; ?>
                                    <input type="text" class="form-control" name="ifsc_code">
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
                                                    <option value='<?= $value['id'] ?>'><?= $value['short_code'] ?></option>
                                                <?php } ?>
                                            </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Balance</label> <i class="text-danger asterik">*</i><?php echo isset($error['balance']) ? $error['balance'] : ''; ?>
                                    <input type="text" class="form-control" name="balance" >
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Earn</label> <i class="text-danger asterik">*</i><?php echo isset($error['earn']) ? $error['earn'] : ''; ?>
                                    <input type="text" class="form-control" name="earn" >
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Incentives</label> <i class="text-danger asterik">*</i><?php echo isset($error['incentives']) ? $error['incentives'] : ''; ?>
                                    <input type="text" class="form-control" name="incentives">
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
<?php $db->disconnect(); ?>
