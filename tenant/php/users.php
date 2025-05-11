<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['login_id'];
    $sql = "SELECT * FROM users WHERE NOT id = {$outgoing_id} AND user_type = 1 ORDER BY name ASC";
    $query = mysqli_query($connection, $sql);
    $output = "";
    if(mysqli_num_rows($query) == 0){
        $output .= "No users are available to chat";
    }elseif(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }
    echo $output;
?>