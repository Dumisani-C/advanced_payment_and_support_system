<?php include('db_connect.php');?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="container-fluid">
    
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div>
        <div class="row">
            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                            <div class="card">
                                <?php
                                    // $query = mysqli_query($conn, "SELECT * FROM tenants WHERE id = '".$_SESSION['id']."'");
                                    // $fetch = mysqli_fetch_array($query);
                                    // $query = mysqli_query($conn, "SELECT * FROM apartments LEFT JOIN users ON apartments.id = tenants.apartment_id WHERE tenants.id = 8");
                                    $user_id = $_SESSION['login_id'];
                                    $query = $conn->query("
                                    SELECT
                                        apartments.id AS apartment_id,
                                        apartments.apartment_no,
                                        apartments.category_id,
                                        apartments.description,
                                        apartments.price,
                                        users.id AS tenant_id,
                                        users.name AS tenant_name,
                                        users.username AS tenant_username,
                                        users.firstname AS tenant_firstname,
                                        users.middlename AS tenant_middlename,
                                        users.lastname AS tenant_lastname,
                                        users.email AS tenant_email,
                                        users.contact AS tenant_contact,
                                        users.user_type AS tenant_user_type,
                                        users.status AS tenant_status,
                                        tenantapartment.date_in
                                    FROM
                                        apartments
                                    INNER JOIN
                                        tenantapartment ON apartments.id = tenantapartment.apartment_id
                                    INNER JOIN
                                        users ON tenantapartment.tenant_id = users.id
                                    WHERE users.apartment_status = 1
                                    AND
                                    users.id = '$user_id'
                                    LIMIT 3");
                                    $row = mysqli_fetch_array($query);
                                    $formatted_price = number_format($row['price'], 2)
                                ?>
                               <div class="card-header">Apartment No: <?php echo $row['apartment_no'] ?></div> 
                               <div class="card-body">
                                    <img src="./images/DSC_2566.jpeg" width="300" height="300"/>
                                    <input type="hidden" name="image_src" id="image_src" value="./images/DSC_2566.jpeg"/>
                               </div>    
                               <div class="card-footer">
                               <span>Apartment Description: <?php echo $row['description'] ?></span><br><br>
                                    <span>Price: MKW <?php echo $formatted_price; ?> </span><br><br>
                                    <input type="submit" name="submit" value="check-in" class="pay_now"/>
                                    <input type="hidden" name="price"  id="price" value="<?php echo $formatted_price; ?>"/>
                                    <input type="hidden" name="item_name" id="item_name" value="<?php echo $row['description'] ?>"/> 
                                    <input type="hidden" name="apartment_no" id="apartment_no" value="<?php echo $row['apartment_no'] ?>"/>  
                               </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
</div>
<script>
    $(document).ready(function(){
       $(".pay_now").on('click',function(e){
            e.preventDefault();
            var image_src = $(this).closest(".card").find("#image_src").attr("value");
            var item_name = $(this).closest(".card").find("#item_name").attr("value");
            var price = $(this).closest(".card").find("#price").attr("value");
            var apartment_no = $(this).closest(".card").find("#apartment_no").attr("value");
            var dt = '&image='+image_src+'&item_name='+item_name+'&price='+price+'&apartment_no='+apartment_no;
            var url = 'http://localhost/advanced_tenant_payment_and_support_system/tenant/index.php?page=checkout'+dt; 
            
            $.ajax({
                 url:url,
                 method:'GET',
                 success:function(){
                      window.location.href=url;
                 }
            });      
       });
    });
</script>
</body>
</html>