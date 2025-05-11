<?php
  if(isset($_GET)){
?>
<?php include('db_connect.php');?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="container-fluid">
    
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div> 
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                     <div class="card-header">
                        <span class="float:right"><button type="button" onclick="goback()" class="back btn btn-primary btn-sm">Go Back</button></span>
                    </div>
                    <div class="form-container" style="padding-left: 10px;">
                        <form autocomplete="off" action="checkout-charge.php" method="POST">
                            <?php
                                $user_id = $_SESSION['login_id'];
                                $query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
                                $row = mysqli_fetch_array($query);
                            ?>
                            <div>
                                <label>Tenant Fullname</label><br>
                                <input type="text" disabled name="c_name" value="<?php echo $row['firstname'] ?> <?php echo $row['middlename'] ?> <?php echo $row['lastname'] ?>" required/>
                            </div>
                            <div>
                                <label>Contact number</label><br>
                                <input type="number" disabled id="ph" name="phone" pattern="\d{10}" maxlength="10" required value="<?php echo $row['contact'] ?>" />
                                <input type="hidden" name="user_id" required value="<?php echo $user_id ?>" />
                            </div>
                            <div>
                                <label>Apartment Description</label><br>
                                <textarea name="product_name" disabled required><?php echo $_GET["item_name"]?></textarea>
                            </div>
                            <div>
                                <label>Price (MKW)</label><br>
                                <input type="text"  name="price" value="<?php echo $_GET["price"]?>" disabled required/>
                            </div>
                           
                                <input type="hidden" name="amount" value="<?php echo $_GET["price"]?>">
                                <input type="hidden" name="apartment_no" value="<?php echo $_GET["apartment_no"]?>"><br>
                            
                            <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="pk_test_REDACTED"
                            data-amount=<?php echo str_replace(",","",$_GET["price"]) * 100?>
                            data-name="<?php echo $_GET["item_name"]?>"
                            data-description="<?php echo $_GET["item_name"]?>"
                            data-image="<?php echo $_GET["image"]?>"
                            data-currency="mk"
                            data-locale="auto">
                            </script>
                        </form>
                        <br><br><br>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="checkout-container">
                    <h4>Apartment No&nbsp;:&nbsp;<?php echo $_GET["apartment_no"]?></h4>
                    <img src="<?php echo $_GET["image"]?>" width="300" height="300"/><br>
                    <span>Price &nbsp;:&nbsp;<?php  echo $_GET["price"]?></span>
                </div>
            </div>
        </div> 
    </div>
</div>

<?php
  }
?>
<script>
    function goback(){
        window.history.go(-1);
    }

    $('#ph').on('keypress',function(){
         var text = $(this).val().length;
         if(text > 9){
              return false;
         }else{
            $('#ph').text($(this).val());
         }
         
    });
</script>
</body>
</html>