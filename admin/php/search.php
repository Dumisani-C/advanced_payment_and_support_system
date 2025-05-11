<?php
    session_start();
    include_once "config.php";

    $outgoing_id = $_SESSION['login_id'];
    $searchTerm = mysqli_real_escape_string($connection, $_POST['searchTerm']);

    $sql = "SELECT * FROM users WHERE NOT id = {$outgoing_id} AND (firstname LIKE '%{$searchTerm}%')";
    $output = "";
    $query = mysqli_query($connection, $sql);
    if(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }else{
        $output .= 'No user found related to your search term';
    }
    echo $output;
?>