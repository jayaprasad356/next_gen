
<section class="content-header">
    <h1>Challenges /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>
    <!-- <ol class="breadcrumb">
        <a class="btn btn-block btn-default" href="add-transaction.php"><i class="fa fa-plus-square"></i> Add New Transaction</a>
    </ol> -->
</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                    <div class="form-group col-md-3">
                    <h4 class="box-title">colors </h4>
                    <select id='color_id' name="color_id" class='form-control'>
                                    <option value=''>All</option>
                                    
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
                       <div class="form-group col-md-3">
                            <h4 class="box-title">Date </h4>
                            <input type="date" class="form-control" name="date" id="date" />
                        </div>
                       <div class="form-group col-md-3">
                            <h4 class="box-title">time </h4>
                            <input type="time" class="form-control" name="time" id="time" />
                        </div>
                    </div>
                    
                    <div  class="box-body table-responsive">
                    <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=challenges" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="false" data-export-types='["txt","excel"]' data-export-options='{
                            "fileName": "challenges-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                        <thead>
                                <tr>
                                    
                                    <th  data-field="id" data-sortable="true">ID</th>
                                    <th  data-field="user_name" data-sortable="true">name</th>
                                    <th  data-field="mobile" data-sortable="true">Mobile</th>
                                    <th  data-field="earn" data-sortable="true">Earn</th>
                                    <th  data-field="name" data-sortable="true">Color Name</th>
                                    <th  data-field="code" data-sortable="true">Color Code</th>
                                    <th  data-field="coins" data-sortable="true">Coins</th>
                                    <th  data-field="datetime" data-sortable="true">Datetime</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="separator"> </div>
        </div>
    </section>

<script>

    $('#date').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#time').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#color_id').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    // $('#manager_id').on('change', function() {
    //         id = $('#manager_id').val();
    //         $('#users_table').bootstrapTable('refresh');
    // });

    function queryParams(p) {
        return {
            "date": $('#date').val(),
            "time": $('#time').val(),
            "color_id": $('#color_id').val(),
            // "manager_id": $('#manager_id').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
    
</script>