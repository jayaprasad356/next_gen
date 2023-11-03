<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

if (isset($_GET['mobile']) && isset($_POST['title']) && isset($_POST['description'])) {
    $mobile = $db->escapeString($_GET['mobile']);
    $title = $db->escapeString($_POST['title']);
    $description = $db->escapeString($_POST['description']);

    // Check if the mobile number is registered
    $sql_query = "SELECT id FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $userResult = $db->getResult();

    if (count($userResult) > 0) {
        // A user with the provided mobile exists
        $user_id = $userResult[0]['id'];

        // Insert the query with the user_id
        $sql_query = "INSERT INTO query (user_id, title, description) VALUES ('$user_id', '$title', '$description')";
        $db->sql($sql_query);

        echo json_encode(array('inserted' => true));
    } else {
        echo json_encode(array('error' => 'User with provided mobile not found.'));
    }
}
?>
