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
if (isset($_POST['btnAdd'])) {

        $name = $db->escapeString(($_POST['name']));
        $min_qty = $db->escapeString($_POST['min_qty']);
        $max_qty = $db->escapeString($_POST['max_qty']);
        $error = array();
       
        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($min_qty)) {
            $error['min_qty'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($max_qty)) {
            $error['max_qty'] = " <span class='label label-danger'>Required!</span>";
        }
       
       
       if (!empty($name) && !empty($min_qty) && !empty($max_qty)) 
       {
           
            $sql_query = "INSERT INTO stores (name,min_qty,max_qty)VALUES('$name','$min_qty','$max_qty')";
            $db->sql($sql_query);
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }

            if ($result == 1) {
                
                $error['add_stores'] = "<section class='content-header'>
                                                <span class='label label-success'>Stores Added Successfully</span> </section>";
            } else {
                $error['add_stores'] = " <span class='label label-danger'>Failed</span>";
            }
            }
        }
?>
<section class="content-header">
    <h1>Add New Stores <small><a href='stores.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Stores</a></small></h1>

    <?php echo isset($error['add_stores']) ? $error['add_stores'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-10">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form url="add-stores-form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                       <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                    <label for="exampleInputtitle">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="name" class="form-control" name="name" required>
                                </div>
                            </div>
                         </div>
                         <br>
                     <div class="row">
                        <div class="form-group">
                             <div class='col-md-4'>
                                  <label for="exampleInputtitle">Minimum Quantity</label> <i class="text-danger asterik">*</i>
                                  <input type="number" class="form-control" name="min_qty" required>
                            </div>
                        <div class='col-md-4'>
                                 <label for="exampleInputtitle">Maximum Quantity</label> <i class="text-danger asterik">*</i>
                                 <input type="number" class="form-control" name="max_qty" required>
                            </div>
                        </div>
                    </div>
                       
                        <br>
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