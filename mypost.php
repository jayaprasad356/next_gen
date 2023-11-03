<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include('includes/variables.php');
include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$fn = new custom_functions;

if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
    $sql_query = "SELECT * FROM posts WHERE id =" . $ID;
    $db->sql($sql_query);
    $res = $db->getResult();
    
    if (count($res) > 0) {
        $imagePath = DOMAIN_URL.'upload/post/'.$res[0]['image'];
        $caption = $res[0]['caption'];
        $user_id = $res[0]['user_id'];
        
        $sql = "SELECT name,refer_code FROM users WHERE id = $user_id";
        $db->sql($sql);
        $user_result = $db->getResult();
        
        if (count($user_result) > 0) {
            $name = $user_result[0]['name'];
            $refer_code = $user_result[0]['refer_code'];
        } 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<div class="container-fluid vh-100"> 
    <div class="d-flex justify-content-center align-items-center h-150"> 
        <div class="card" style="width: 19rem;">
            <div class="card-body text-center">
                <div class="row">
                    <p class="card-text mt-2"><h6>I earn 10 likes = 1 rupees.<br> Install App and give like to my post.</h6></p>
                    <div class="col-4 offset-3 text-right">
                        <a href="https://play.google.com/store/apps/details?id=com.app.colorchallenge"><img src="https://www.freepnglogos.com/uploads/play-store-logo-png/play-store-logo-nisi-filters-australia-11.png" alt="Play Store" style="width: 130px; height: 60px;"></a>
                    </div>
                </div>
            </div>
            <img src="<?php echo $imagePath; ?>" class="card-img-top" alt="...">
            <div class="card-body text-center">
                <h5 class="card-title">DOG</h5>
                <p class="card-text"><?php echo $caption; ?></p>
            </div>
            <div class="card-footer">
                <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="35" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                     <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                     <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                    <h5 class="card-title mt-2 text-right"><?php echo $name; ?></h5>
                    <h5 class="card-title mt-2 text-right"><?php echo $refer_code; ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
