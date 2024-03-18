<?php
session_start();
include_once('includes/crud.php');
$db = new Database();

if (!$db->connect()) {
    die("Database connection error: " . $db->getErrorMessage());
}

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the current time has exceeded the session timeout; if yes, redirect to the login page
$currentTime = time() + 25200; // Adjust the time zone offset as needed
$expired = 720000; // Adjust the session timeout as needed

if ($currentTime > $_SESSION['timeout']) {
    header("Location: login.php");
    exit;
}

unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;
?>
<?php
if (isset($_POST['btnPaidAll'])) {
    $sql = "UPDATE withdrawals SET status = 1 WHERE status = 0";
    $db->sql($sql);
    $result = $db->getResult();
    header("Location: withdrawals.php");
exit;
}
if (isset($_POST['btnNo'])) {
    header("Location: withdrawals.php");
    exit;
}

if (isset($_POST['btncancel'])) {
    header("Location: withdrawals.php");
    exit;
}

?>
<?php include "header.php"; ?>
<html>

<head>
    <title>Paid All User | - Dashboard</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <h1>Confirm Action</h1>
        <hr />
        <form method="post">
            <p>Are you sure you want to paid all users?</p>
            <input type="submit" class="btn btn-primary" value="Pay All" name="btnPaidAll" />
            <input type="submit" class="btn btn-danger" value="Cancel" name="btnNo" />
            <input type="submit" class="btn btn-warning" value="Back" name="btncancel" />
        </form>
    </div><!-- /.content-wrapper -->
</body>

</html>
<?php include "footer.php"; ?>
