<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

$sql = "SELECT id,name FROM branches ORDER BY id ASC";
$db->sql($sql);
$res = $db->getResult();

?>
<?php
if (isset($_POST['btnAdd'])) {


    $name = $db->escapeString(($_POST['name']));
    $short_code = $db->escapeString(($_POST['short_code']));
    $support_lan = $db->escapeString(($_POST['support_lan']));
    $mobile = $db->escapeString(($_POST['mobile']));
    $error = array();
   
    if (empty($name)) {
        $error['name'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($short_code)) {
        $error['short_code'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($support_lan)) {
        $error['support_lan'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($mobile)) {
        $error['mobile'] = " <span class='label label-danger'>Required!</span>";
    }
   
   
    if (!empty($name) && !empty($short_code) && !empty($support_lan)&& !empty($mobile)) 
    {
           
            $sql_query = "INSERT INTO branches (name,short_code,support_lan,mobile)VALUES('$name','$short_code','$support_lan','$mobile')";
            $db->sql($sql_query);
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }
            if ($result == 1) {
                
                $error['add_branches'] = "<section class='content-header'>
                                                <span class='label label-success'>Branch Added Successfully</span> </section>";
            } else {
                $error['add_branches'] = " <span class='label label-danger'>Failed</span>";
        }
        }
}
?>
    
<section class="content-header">
    <h1>Add New branches <small><a href='branches.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Branches</a></small></h1>

    <?php echo isset($error['add_branches']) ? $error['add_branches'] : ''; ?>
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
                <form url="add_branches_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                       <div class="row">
                            <div class="form-group">
                                <div class='col-md-10'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i><?php echo isset($error['name']) ? $error['name'] : ''; ?>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                               <div class='col-md-10'>
                                    <label for="exampleInputEmail1">Mobile</label> <i class="text-danger asterik">*</i><?php echo isset($error['mobile']) ? $error['mobile'] : ''; ?>
                                    <input type="text" class="form-control" name="mobile" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                               <div class='col-md-10'>
                                    <label for="exampleInputEmail1">Short Code</label> <i class="text-danger asterik">*</i><?php echo isset($error['short_code']) ? $error['short_code'] : ''; ?>
                                    <input type="text" class="form-control" name="short_code" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                               <div class='col-md-10'>
                                    <label for="exampleInputEmail1">Support Lan</label> <i class="text-danger asterik">*</i><?php echo isset($error['support_lan']) ? $error['support_lan'] : ''; ?>
                                    <select id='support_lan' name="support_lan" class='form-control'>
                                    <option value=''>--select--</option>
                                    <option value='tamil'>Tamil</option>
                                      <option value='kannada'>Kannada</option>
                                      <option value='telugu'>Telugu</option>
                                      <option value='hindi'>Hindi</option>
                                      <option value='english'>English</option>
                                    </select>
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
    $('#add_branches_form').validate({

        ignore: [],
        debug: false,
        rules: {
            name: "required",
            short_code: "required",

        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>
<script>
    var changeCheckbox = document.querySelector('#trial_earning_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#trial_earnings').val(1);

        } else {
            $('#trial_earnings').val(0);
        }
    };
</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>