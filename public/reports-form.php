<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>

<?php
if (isset($_POST['btnUpdate'])) {
    $branch_id = $db->escapeString($_POST['branch_id']);
    $from_date = $db->escapeString($_POST['from_date']);
    $to_date = $db->escapeString($_POST['to_date']);



    // Generate and set assumed values
    $total_joints = 1000;
    $total_withdrawal = 200;

    if (!empty($branch_id) && !empty($from_date) && !empty($to_date)) {
        $sql_query = "UPDATE `reports` SET `branch_id` = '$branch_id',`from_date` = '$from_date',`to_date` = '$to_date' WHERE `app_settings`.`id` = 1;";
        $db->sql($sql_query);
        $result = $db->getResult();
        if (!empty($result)) {
            $result = 0;
        } else {
            $result = 1;
        }

        if ($result == 1) {
            $error['update_reports'] = "<section class='content-header'>
                                                <span class='label label-success'>Updated Successfully</span> 
                                              </section>";
        } else {
            $error['update_reports'] = " <span class='label label-danger'>Failed</span>";
        }
    }
}

$sql_query = "SELECT * FROM reports ";
$db->sql($sql_query);
$res = $db->getResult();
?>

<section class="content-header">
    <h1>Reports</h1>
    <?php echo isset($error['update_reports']) ? $error['update_reports'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>

<section class="content">
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary">
                <div class="box-header with-border"></div>

                <form name="update_reports_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
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
                                        <option value='<?= $value['id'] ?>' <?= $value['id'] == $res[0]['branch_id'] ? 'selected="selected"' : ''; ?>><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label for="exampleInputEmail1">From Date</label> <i class="text-danger asterik">*</i>
                                    <?php echo isset($error['from_date']) ? $error['from_date'] : ''; ?>
                                    <input type="date" class="form-control" name="from_date" value="<?php echo  $res[0]['from_date'] ?>" required>
                                </div>

                                <div class="col-md-5">
                                    <label for="exampleInputEmail1">To Date</label> <i class="text-danger asterik">*</i>
                                    <?php echo isset($error['to_date']) ? $error['to_date'] : ''; ?>
                                    <input type="date" class="form-control" name="to_date" value="<?php echo  $res[0]['to_date'] ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnUpdate">Submit</button>
                    </div>
                    <?php if (isset($_POST['btnUpdate'])) : ?>
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-5">
                                        <label for="exampleInputEmail1">Total Joints</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="total_joints" value="<?php echo $total_joints; ?>" readonly>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="exampleInputEmail1">Total Withdrawal</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="total_withdrawal" value="<?php echo $total_withdrawal; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#update_reports_form').validate({
        ignore: [],
        debug: false,
        rules: {
            name: "required",
            role: "required",
            category_image: "required",
            mobile: "required",
        }
    });

    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#blah').attr('src', e.target.result).width(150).height(200);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<script>
    function refreshPage() {
        window.location.reload();
    }
</script>

<?php $db->disconnect(); ?>
