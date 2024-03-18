<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<section class="content-header">
    <h1>Bulk Quantity<small></small></h1>
</section>

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
                <form id='add_form' method="post" action="public/db-operation.php" enctype="multipart/form-data">
                    <input type="hidden" id="bulk_quantity" name="bulk_quantity" required="" value="1" aria-required="true">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                <label for="">CSV File</label>
                                <input type="file" name="upload_file" class="form-control" accept=".csv" />
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">

<div id="result" style="display: none;"></div>
</div>

                    </div>
                    <!-- /.box-body -->

                   

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="submit_btn" name="btnAdd">Upload</button>
                        <input type="reset" class="btn-warning btn" value="Clear" />

                        <a class='btn btn-info' id='sample' href='#' download> <em class='fa fa-download'></em> Download Sample File</a>

                    </div>
                </form>
                <div id="result"></div>

            </div><!-- /.box -->
        </div>
    </div>
</section>
<script>
  
    $('#type').on('change', function(e) {
        var type = $('#type').val();
        $("#type1").val(type);
    });
    $('.box-footer > #sample').click(function(e) {
        e.preventDefault(); //stop the browser from following
        //whenever you click off an input element
        // type1 = $("#type1").val();
        // if (type1 != 'products' ) {
        //   alert('Please select type.');
        // }
        // if (type1 == 'products') {
        window.location.href = 'library/quantity.csv';
        // } 

    });
 
</script>

<script>
    $('#add_form').validate({
        rules: {
            upload_file: "required",
            type: "required"
        }
    });
</script>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#add_form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if ($("#add_form").validate().form()) {
            if (confirm('Are you sure?Want to upload')) {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    beforeSend: function() {
                        $('#submit_btn').html('Please wait..').attr('disabled', 'true');
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        $('#result').html(result);
                        $('#result').show().delay(6000).fadeOut();
                        $('#submit_btn').html('Upload').removeAttr('disabled');
                        $('#add_form')[0].reset();
                    }
                });
            }
        }
    });
</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>