<section class="content-header">
    <h1>Manage Leaves /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

    <ol class="breadcrumb">
        <a class="btn btn-block btn-default" href="add-leave.php"><i class="fa fa-plus-square"></i> Add New Leave</a>
    </ol>

</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <h4 class="box-title">Filter by Type</h4>
                            <select id='type' name="type" class='form-control'>
                                <option value="">--select--</option>
                                <option value="user_leave">User Leave</option>
                                <option value="common_leave">Common Leave</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h4 class="box-title">Filter by Date</h4>
                            <input type="date" class="form-control" id="date" name="date" value="<?php echo (isset($_GET['date'])) ? $_GET['date'] : "" ?>"></input>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=leaves" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="date" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "yellow app leaves-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true">ID</th>
                                <th data-field="date" data-sortable="true">Date</th>
                                <th data-field="type" data-sortable="true">Leave Type</th>
                                <th data-field="name" data-sortable="true">User</th>
                                <th data-field="mobile" data-sortable="true">Mobile Number</th>
                                <th data-field="reason" data-sortable="true">Reason</th>
                                <th data-field="status" data-sortable="true">Status</th>
                                <th data-field="operate">Action</th>
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
    <!-- /.row (main row) -->
</section>
<script>
    // $('#seller_id').on('change', function() {
    //     $('#products_table').bootstrapTable('refresh');
    // });
    
    $('#type').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });

    $('#date').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });

    function queryParams(p) {
        return {
            "type": $('#type').val(),
            "date": $('#date').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>
