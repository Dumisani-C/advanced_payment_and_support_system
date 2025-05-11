<?php
  $hostname = "localhost";
  $username = "root";
  $password = "";
  $dbname = "ark_apartment_db";

  $connection = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$connection){
    echo "Database connection error".mysqli_connect_error();
  }
?>
