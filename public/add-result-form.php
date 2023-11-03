<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
date_default_timezone_set('Asia/Kolkata');


?>
<?php
if (isset($_POST['btnAdd'])) {

    $color_id = $db->escapeString(($_POST['color_id']));
    // $date = date('Y-m-d');
    $date = $db->escapeString(($_POST['date']));


    if (empty($color_id)) {
        $error['color_id'] = " <span class='label label-danger'>Required!</span>";
    }
  
    if (!empty($color_id)) {
        $sql_query = "INSERT INTO results (color_id,date)VALUES('$color_id','$date')";
        $db->sql($sql_query);
        $result = $db->getResult();
        if (!empty($result)) {
            $result = 0;
        } else {
            $result = 1;
        }
        if ($result == 1) {

            $error['add_result'] = "<section class='content-header'>
                                                <span class='label label-success'>Result Added Successfully</span> </section>";
        } else {
            $error['add_result'] = " <span class='label label-danger'>Failed</span>";
        }
    }
}
?>
<section class="content-header">
    <h1>Add Result <small><a href='result.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Results</a></small></h1>
    <?php echo isset($error['add_result']) ? $error['add_result'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-8">

            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form name="add_project_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-8">
                                    <label for="">Select Color</label> <i class="text-danger asterik">*</i>
                                    <select id='color_id' name="color_id" class='form-control' required>
                                        <option value="">select</option>
                                        <?php
                                        $sql = "SELECT id,name FROM `colors`";
                                        $db->sql($sql);
                                        $result = $db->getResult();
                                        foreach ($result as $value) {
                                        ?>
                                            <option value='<?= $value['id'] ?>'><?= $value['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>      
                        </div>                  
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-8">
                                    <label for="">Select Color</label> <i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="date" required>
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
    $('#add_project_form').validate({

        ignore: [],
        debug: false,
        rules: {
            color_id: "required",
        
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>

<!--code for page clear-->
<script>
    function refreshPage() {
        window.location.reload();
    }
</script>

<?php $db->disconnect(); ?>