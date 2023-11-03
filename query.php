<?php
session_start(); // Start the session
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$res = [];
$user_id = NULL;

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT id FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $userData = $db->getResult();

    if (!empty($userData)) {
        $user_id = $userData[0]['id']; 

        $sql_query = "SELECT query.*, users.name FROM query LEFT JOIN users ON query.user_id = users.id WHERE users.mobile = '$mobile'";
        $db->sql($sql_query);
        $res = $db->getResult();
    } else {
        echo 'User not found.';
    }
}

if (isset($_POST['btnAdd'])) {
    $title = $db->escapeString($_POST['title']);
    $description = $db->escapeString($_POST['description']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id !== null) {
        $checkstatus = null;
        $sql_query = "SELECT status FROM query WHERE user_id = '$user_id' AND status = '0'";
        $db->sql($sql_query);
        $checkstatus = $db->getResult();

        if (!empty($checkstatus)) {
            echo "<script>alert('You already have a pending query. Please wait.');</script>";
        } else {
            $sql_query = "INSERT INTO query (user_id, title, description) VALUES ('$user_id', '$title', '$description')";
            $db->sql($sql_query);

            $sql_query = "SELECT * FROM query WHERE title = '$title' AND description = '$description' AND user_id = '$user_id'";
            $db->sql($sql_query);
            $insertedData = $db->getResult();
        }
    } else {
        $errorMessage = 'User not found. Query insertion failed.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form class="form-inline" method="GET">
                <div class="form-group mb-3">
                        <label for="mobileNumber" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter your mobile number" required>
                        <!-- Custom validation message -->
                        <div class="invalid-feedback" id="mobileValidationMessage">Mobile number is required.</div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="viewButton">View</button>
                    <button type="button" class="btn btn-success" id="addQueryButton">Add Query</button>
                </form>
                <div class="card mt-3">
                    <div class="card-body">
                        <?php
                        if ($res) {
                            foreach ($res as $row) {
                                echo '<h5 class="card-title">' . $row['title'] . '</h5>';
                                echo '<p class="card-text">Description: ' . $row['description'] . '</p>';
                                echo '<p class="card-text">' . getStatusLabel($row['status']) . '</p>';
                                echo '<p class="card-text">Date and Time: ' . $row['datetime'] . '</p>';
                                echo '<hr>';
                            }
                        } 
                        function getStatusLabel($status) {
                            // Define status labels based on the status values.
                            $statusLabels = array(
                                '0' => '<span class="text-primary">Processing</span>',
                                '1' => '<span class="text-success">Fixed</span>',
                                '2' => '<span class="text-danger">Rejected</span>',
                            );
                        
                            // Check if the status exists in the array, and return the label.
                            if (isset($statusLabels[$status])) {
                                return $statusLabels[$status];
                            } else {
                                return 'Unknown Status';
                            }
                        }
                        
                        ?>
                    </div>
                </div>
                <?php if (!empty($insertedData)): ?>
        <div class="card mt-3">
            <div class="card-body">
            <h4>Request Query Details</h4>
                    <h5 class="card-title"><?php echo $insertedData[0]['title']; ?></h5>
                    <p class="card-text">Description: <?php echo $insertedData[0]['description']; ?></p>
                    <p class="card-text">Status: 
                    <?php
                      $status = $insertedData[0]['status'];
                       if ($status == 0) {
                          echo '<span class="text-primary">Processing</span>';
                       } elseif ($status == 1) {
                           echo '<<span class="text-success">Fixed</span>';
                       } elseif ($status == 2) {
                            echo '<span class="text-danger">Rejected</span>';
                        } else {
                            echo 'Unknown Status';
                          }
                        ?>
                  <p class="card-text">DateTime: <?php echo $insertedData[0]['datetime']; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addQueryModal" tabindex="-1" role="dialog" aria-labelledby="addQueryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQueryModalLabel" >Add Query</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalButton" required>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form name="add_query_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Title</label>
                               <select class="form-control" id="title" name="title" required>
                               <option value="">select</option>
                               <option value="Register Issue">Register Issue</option>
                               <option value="Otp Issue">Otp Issue</option>
                               <option value="Ads Issue">Ads Issue</option>
                               <option value="Withdrawal Issue">Withdrawal Issue</option>
                               <option value="Refer Bonus Issue">Refer Bonus Issue</option>
                               <option value="Other Issue">Other Issue</option>
                           </select>
                         </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="clearFormButton">Clear</button>
                    <button type="submit" class="btn btn-primary" name="btnAdd">Request Query</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.min.js"></script>
<script>

$(document).ready(function () {
    // Function to open the "Add Query" modal
    function openAddQueryModal() {
        $('#addQueryModal').modal('show');
    }

    $('#addQueryButton').click(function () {
        // Check if the Mobile Number field is not empty
        var mobileNumber = $('#mobile').val();
        if (mobileNumber !== '') {
          

            // Check if the mobile number is registered
            $.ajax({
                url: 'check_mobile.php?mobile=' + mobileNumber,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.registered) {
                        // Mobile number is registered, open the "Add Query" modal
                        openAddQueryModal();
                    } else {
                        // Mobile number is not registered, display an error message
                        alert('Mobile number is not registered.');
                    }
                },
                error: function () {
                    // Handle any errors here
                    alert('Error checking mobile number.');
                }
            });
        } else {
            // If mobile number is empty, show validation message
            $('#mobileValidationMessage').show();
        }
    });

    // Function to handle the "View" button click
    $('#viewButton').click(function () {
        var mobileNumber = $('#mobile').val();
        if (mobileNumber !== '') {
            $('#mobileValidationMessage').hide();

            // Check if the mobile number is registered
            $.ajax({
                url: 'check_mobile.php?mobile=' + mobileNumber,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.registered) {
                        // Mobile number is registered, open the "Add Query" modal
                        openAddQueryModal();
                    } else {
                        // Mobile number is not registered, display an error message
                        alert('Mobile number is not registered.');
                    }
                },
                error: function () {
                    // Handle any errors here
                    alert('Error checking mobile number.');
                }
            });
        } else {
            // If mobile number is empty, show validation message
            $('#mobileValidationMessage').show();
        }
    });


    $('#clearFormButton').click(function () {
        $('#title').val('');
        $('#description').val('');
    });

    $('#closeModalButton').click(function () {
        $('#mobile').prop('required', false);
        $('#addQueryModal').modal('hide');
    });

    $('#mobile').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
   
    
});


</script>

</body>
</html>