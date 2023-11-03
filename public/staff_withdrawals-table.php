<?php
$currentdate = date('Y-m-d');
// if (isset($_POST['btnUnpaid']) && isset($_POST['enable'])) {
//     for ($i = 0; $i < count($_POST['enable']); $i++) {
        
    
//         $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
//         $sql = "UPDATE withdrawals SET status=0 WHERE id = $enable";
//         $db->sql($sql);
//         $result = $db->getResult();
//     }
// }
if (isset($_POST['btnPaid'])  && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
    
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
        $sql = "UPDATE staff_withdrawals SET status=1 WHERE id = $enable";
        $db->sql($sql);
        $result = $db->getResult();
    }
       
}
if (isset($_POST['btnCancel'])  && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));

        $sql = "SELECT * FROM `staff_withdrawals` WHERE id = $enable AND status != 2";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num >= 1) {
            $sql = "UPDATE staff_withdrawals SET status=2 WHERE id = $enable";
            $db->sql($sql);
            $sql = "SELECT * FROM `staff_withdrawals` WHERE id = $enable";
            $db->sql($sql);
            $res = $db->getResult();
            $staff_id= $res[0]['staff_id'];
            $amount= $res[0]['amount'];
            $sql = "UPDATE staffs SET balance= balance + $amount WHERE id = $staff_id";
            $db->sql($sql);
            }

        }
}

?>
<?php
if (isset($_POST['export_all'])) {
	$join = "WHERE w.staff_id = s.id AND w.status= 0";
	$sql = "SELECT w.id AS id,w.*,s.name,s.balance,s.mobile,s.email,s.branch,s.bank_name,s.bank_account_number,s.ifsc_code FROM `staff_withdrawals` w,`staffs` s $join";
	$db->sql($sql);
	$developer_records = $db->getResult();
	
	$filename = "staff_withdrawals-data".date('Ymd') . ".xls";			
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"$filename\"");	
	$show_coloumn = false;
	if(!empty($developer_records)) {
	  foreach($developer_records as $record) {
		if(!$show_coloumn) {
		  // display field/column names in first row
		  echo implode("\t", array_keys($record)) . "\n";
		  $show_coloumn = true;
		}
		echo implode("\t", array_values($record)) . "\n";
	  }
	}
	exit;  
}
?>

<section class="content-header">
    <h1>Staff Withdrawals /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <form name="withdrawal_form" method="post" enctype="multipart/form-data">
            <div class="row">
                <!-- Left col -->
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                                <div class="row">
                                        <div class="form-group col-md-3">
                                            <h4 class="box-title">Filter by Name </h4>
                                                <select id='user_id' name="user_id" class='form-control'>
                                                <option value=''>All</option>
                                                
                                                        <?php
                                                        $sql = "SELECT id,first_name FROM `staffs`";
                                                        $db->sql($sql);
                                                        $result = $db->getResult();
                                                        foreach ($result as $value) {
                                                        ?>
                                                            <option value='<?= $value['id'] ?>'><?= $value['first_name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <a href="export-withdrawals.php" class="btn btn-primary"><i class="fa fa-download"></i> Export All Withdrawals</a>
                                      
                                        </div>
                                        <div class="form-group col-md-3">
                                            <a href="export-unpaid-withdrawals.php" class="btn btn-primary"><i class="fa fa-download"></i> Export Unpaid Withdrawals</a>
                                        </div>

                                        
                                </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                                <div class="row">
                                    <?php 
                                    if($_SESSION['role'] == 'Super Admin'){?>
                                        <div class="text-left col-md-2">
                                            <input type="checkbox" onchange="checkAll(this)" name="chk[]" > Select All</input>
                                        </div> 
                                        <div class="col-md-3">
                                                <!-- <button type="submit" class="btn btn-primary" name="btnUnpaid">Unpaid</button> -->
                                                <button type="submit" class="btn btn-success" name="btnPaid">Paid</button>
                                                <button type="submit" class="btn btn-danger" name="btnCancel">Cancelled</button>
                                                
                                        </div>
                                    <?php } ?>
                                </div>
                            <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=staff_withdrawals" data-page-list="[5, 10, 20, 50, 100, 200,500,700,1000]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="w.id" data-show-footer="true" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                                "fileName": "Yellow app-withdrawals-list-<?= date('d-m-Y') ?>",
                                "ignoreColumn": ["operate"] 
                            }'>
                                <thead>
                                    <tr>
                                        <th data-field="column"> All</th>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="true" data-visible="true" data-footer-formatter="totalFormatter">Name</th>
                                        <th data-field="amount" data-sortable="true" data-visible="true" data-footer-formatter="priceFormatter">Amount</th>
                                        <th data-field="balance" data-sortable="true">Balance</th>
                                        <th data-field="status" data-sortable="true">Status</th>
                                        <th data-field="date" data-sortable="true">Date</th>
                                        <th data-field="bank_account_number" data-sortable="true">Account Number</th>
                                        <th data-field="bank_name" data-sortable="true">Bank Name</th>
                                        <th data-field="branch" data-sortable="true">Branch</th>
                                        <th data-field="ifsc_code" data-sortable="true">IFSC Code</th>
                                        <th data-field="mobile" data-sortable="true">Mobile</th>

                                        <!-- <th  data-field="operate" data-events="actionEvents">Action</th> -->
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <div class="separator"> </div>
            </div>
        </form>

        <!-- /.row (main row) -->
    </section>
<script>
 function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
    
</script>
<script>
        $('#user_id').on('change', function() {
            id = $('#user_id').val();
            $('#users_table').bootstrapTable('refresh');
        });

    function queryParams(p) {
        return {
            "user_id": $('#user_id').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
    function totalFormatter() {
        return '<span style="color:green;font-weight:bold;font-size:large;">TOTAL</span>'
    }

    var total = 0;

    function priceFormatter(data) {
        var field = this.field
        return '<span style="color:green;font-weight:bold;font-size:large;"> ' + data.map(function(row) {
                return +row[field]
            })
            .reduce(function(sum, i) {
                return sum + i
            }, 0);
    }
</script>
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>

