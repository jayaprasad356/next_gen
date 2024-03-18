<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include('includes/variables.php');
include_once('includes/custom-functions.php');
include_once('includes/functions.php');



$sql_query = "SELECT * FROM users";
$db->sql($sql_query);
$res = $db->getResult();
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <style>
        .gradient-background-purple {
            background: linear-gradient(to bottom, #B98CC6, #FFD700);
        }
        .table.table-hover {
            border: none;
        }

        .table.table-hover th,
        .table.table-hover td {
            border: none;
        }

        .table.table-hover thead {
            border-bottom: none;
        } 
    </style>
</head>
<body style="background: linear-gradient(to bottom, #800080, #A020F0);">
<div class="container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                <th data-field="id" data-sortable="true" style="color: white;">ID</th>
                    <th data-field="name" data-sortable="true" style="color: white; text-align: center; width: 40%;">Name</th>
                    <th data-field="today_ads" data-sortable="true" style="color: white; text-align: center;">Today&nbsp;Ads</th>
                    <th data-field="total_ads" data-sortable="true" style="color: white; text-align: center;">Total&nbsp;Ads</th>
                </tr>
            </thead>
            <tbody>
            <?php
$prevName = null;
foreach ($res as $row) {
    $currentName = $row['name'];

    // Check if the current name is different from the previous one
    if ($currentName !== $prevName) {
        echo '<tr class="blank-row"><td></td><td></td><td></td></tr>';
    }
    echo "<tr>";
    echo "<td style='color: white; font-size: 12px;'>" . $row['id'] . "</td>";
    echo "<td class='bg-light text-center' style='border-radius: 30px; padding: 10px; text-transform: uppercase; color: purple; font-weight: bold; font-size: 12px;'>" . $row['name'] . "</td>";
    echo "<td class='gradient-background-purple text-center' style='border-radius: 30px; padding: 10px; font-weight: bold; color: purple; font-size: 12px;'>" . $row['today_ads'] . "</td>";
    echo "<td class='gradient-background-purple text-center' style='border-radius: 30px; padding: 10px; font-weight: bold; color: purple; font-size: 12px;'>" . $row['total_ads'] . "</td>";
    echo "</tr>";
    
    $prevName = $currentName;
}
?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.min.js"></script>
</body>
</html>


