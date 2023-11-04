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

	$name = $db->escapeString($_POST['name']);
    $min_qty = $db->escapeString($_POST['min_qty']);
    $max_qty = $db->escapeString($_POST['max_qty']);
    $per_order_cost = $db->escapeString($_POST['per_order_cost']);

	$error = array();

	if (!empty($name) && !empty($min_qty)&& !empty($max_qty))  {
		$sql_query = "UPDATE stores SET name='$name',min_qty = '$min_qty',max_qty = '$max_qty',per_order_cost = '$per_order_cost' WHERE id =  $ID";
		$db->sql($sql_query);
		$update_result = $db->getResult();
		if (!empty($update_result)) {
			$update_result = 0;
		} else {
			$update_result = 1;
		}

		// check update result
		if ($update_result == 1) {
			$error['update_stores'] = " <section class='content-header'><span class='label label-success'>Stores updated Successfully</span></section>";
		} else {
			$error['update_stores'] = " <span class='label label-danger'>Failed to Update</span>";
		}
	}
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM stores WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
	<script>
		window.location.href = "stores.php";
	</script>
<?php } ?>
<section class="content-header">
	<h1>
		Edit Stores<small><a href='stores.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Stores</a></small></h1>
	<small><?php echo isset($error['update_stores']) ? $error['update_stores'] : ''; ?></small>
	<ol class="breadcrumb">
		<li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
	</ol>
</section>
<section class="content">
	<!-- Main row -->

	<div class="row">
		<div class="col-md-10">

			<!-- general form elements -->
			<div class="box box-primary">
				<div class="box-header with-border">
				</div><!-- /.box-header -->
				<!-- form start -->
				<form id="edit_Stores_form" method="post" enctype="multipart/form-data">
					<div class="box-body">
						<div class="row">
							<div class="form-group">
                                <div class="col-md-4">
									<label for="exampleInputEmail1">Name</label><i class="text-danger asterik">*</i>
									<input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
								 </div>
                               </div>  
                            </div>  
                            <br>
                        <div class="row">
							<div class="form-group">
                                 <div class="col-md-4">
									<label for="exampleInputEmail1">Minimum Quantity</label><i class="text-danger asterik">*</i>
									<input type="number" class="form-control" name="min_qty" value="<?php echo $res[0]['min_qty']; ?>">
								 </div>
                                 <div class="col-md-4">
									<label for="exampleInputEmail1">Maximum Quantity</label><i class="text-danger asterik">*</i>
									<input type="number" class="form-control" name="max_qty" value="<?php echo $res[0]['max_qty']; ?>">
								 </div>
                                 <div class="col-md-4">
									<label for="exampleInputEmail1">Per Order Cost</label><i class="text-danger asterik">*</i>
									<input type="number" class="form-control" name="per_order_cost" value="<?php echo $res[0]['per_order_cost']; ?>">
								 </div>
                              </div>
                             </div>
                         </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>