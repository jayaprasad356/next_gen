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

		$pin = $db->escapeString(($_POST['pin']));
		$name = $db->escapeString(($_POST['name']));
		$email = $db->escapeString(($_POST['email']));
		$mobile = $db->escapeString(($_POST['mobile']));
        $status = $db->escapeString(($_POST['status']));
		$error = array();

		if (!empty($name) && !empty($email) && !empty($mobile)) {
             $sql_query = "UPDATE managers SET pin='$pin', name='$name',email='$email',mobile='$mobile',status='$status' WHERE id =  $ID";
			 $db->sql($sql_query);
             $update_result = $db->getResult();
			if (!empty($update_result)) {
				$update_result = 0;
			} else {
				$update_result = 1;
			}

			// check update result
			if ($update_result == 1) {
				$error['update_user'] = " <section class='content-header'><span class='label label-success'>User updated Successfully</span></section>";
			} else {
				$error['update_user'] = " <span class='label label-danger'>Failed to Update</span>";
			}
		}
	} 


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM managers WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
	<script>
		window.location.href = "managers.php";
	</script>
<?php } ?>
<section class="content-header">
	<h1>
		Edit Managers<small><a href='managers.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Managers</a></small></h1>
	<small><?php echo isset($error['update_user']) ? $error['update_user'] : ''; ?></small>
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
				<form id="edit_user_form" method="post" enctype="multipart/form-data">
					<div class="box-body">
					<input type="hidden" id="old_image" name="old_image"  value="<?= $res[0]['image']; ?>">
						<div class="row">
							<div class="form-group">
							<div class="col-md-6">
								   <label for="exampleInputEmail1">Pin</label><i class="text-danger asterik">*</i>
							       <input type="text" class="form-control" name="pin" value="<?php echo $res[0]['pin']; ?>">
								</div>
								<div class="col-md-6">
								   <label for="exampleInputEmail1">Name</label><i class="text-danger asterik">*</i>
							       <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
								</div>
								<div class="col-md-6">
								   <label for="exampleInputEmail1">Email Id:</label><i class="text-danger asterik">*</i>
							       <input type="email" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>">
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="form-group">
								<div class="col-md-6">
								   <label for="exampleInputEmail1">Mobile Number</label><i class="text-danger asterik">*</i>
							       <input type="number" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
								</div>
								<div class="col-md-4">
										<label class="control-label">Status</label><i class="text-danger asterik">*</i>
										<div id="status" class="btn-group">
											<label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
												<input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Not-Verified
											</label>
											<label class="btn btn-success" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
												<input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Verified
											</label>
										</div>
                                 </div>
							</div>
						</div>
						<br>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
</script>
