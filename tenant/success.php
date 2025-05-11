<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>success</title>
    <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap");
        .success-container {
            width:50%;
            position:absolute;
            top:30%;
            left:50%;
            transform:translate(-50%,-50%);
            color:#bdc3c7;
            font-weight:bold;
            font-family: "Poppins", sans-serif;
        }
    </style>
</head>
<body>
      <div class="success-container">
        <?php
           if(isset($_GET["amount"]) && !empty($_GET["amount"])){
               ?>
            <h3 style="color: green;">Your MKW<?php echo $_GET["amount"] ?> Transaction has been Successfully Completed.</h3>
          <?php
           }
        ?>
        <span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="index.php?page=pay">
            <i class="fa fa-plus"></i> Go Back
        </a></span>
      </div>
</body>
</html>