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
    $mobile = $db->escapeString($_POST['mobile']);
    $email = $db->escapeString($_POST['email']);
    $location = $db->escapeString($_POST['location']);
    $datetime = $db->escapeString($_POST['datetime']);

	$error = array();

	if (!empty($name) && !empty($mobile)&& !empty($email)&& !empty($location)&& !empty($datetime))  {
		$sql_query = "UPDATE website_enroll SET name='$name',mobile = '$mobile',email = '$email',location = '$location',datetime = '$datetime' WHERE id =  $ID";
		$db->sql($sql_query);
		$update_result = $db->getResult();
		if (!empty($update_result)) {
			$update_result = 0;
		} else {
			$update_result = 1;
		}

		// check update result
		if ($update_result == 1) {
			$error['update_stores'] = " <section class='content-header'><span class='label label-success'>Website Enroll updated Successfully</span></section>";
		} else {
			$error['update_stores'] = " <span class='label label-danger'>Failed to Update</span>";
		}
	}
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM website_enroll WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
	<script>
		window.location.href = "website_enroll.php";
	</script>
<?php } ?>
<section class="content-header">
	<h1>
		Edit Website Enroll<small><a href='website_enroll.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Website Enroll</a></small></h1>
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
                                 <div class="col-md-4">
									<label for="exampleInputEmail1">Email</label><i class="text-danger asterik">*</i>
									<input type="text" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>">
								 </div>
                               </div>  
                            </div>  
                            <br>
                        <div class="row">
							<div class="form-group">
                                 <div class="col-md-4">
									<label for="exampleInputEmail1">Mobile</label><i class="text-danger asterik">*</i>
									<input type="number" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
								 </div>
                                 <div class="col-md-4">
									<label for="exampleInputEmail1">Location</label><i class="text-danger asterik">*</i>
									<input type="text" class="form-control" name="location" value="<?php echo $res[0]['location']; ?>">
								 </div>
                                 <div class="col-md-4">
									<label for="exampleInputEmail1">DateTime</label><i class="text-danger asterik">*</i>
									<input type="date" class="form-control" name="datetime" value="<?php echo $res[0]['datetime']; ?>">
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