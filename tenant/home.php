<?php include 'db_connect.php' ?>
<style>
   span.float-right.summary_icon {
    font-size: 3rem;
    position: absolute;
    right: 1rem;
    top: 0;
}
.imgs{
		margin: .5em;
		max-width: calc(100%);
		max-height: calc(100%);
	}
	.imgs img{
		max-width: calc(100%);
		max-height: calc(100%);
		cursor: pointer;
	}
	#imagesCarousel,#imagesCarousel .carousel-inner,#imagesCarousel .carousel-item{
		height: 60vh !important;background: black;
	}
	#imagesCarousel .carousel-item.active{
		display: flex !important;
	}
	#imagesCarousel .carousel-item-next{
		display: flex !important;
	}
	#imagesCarousel .carousel-item img{
		margin: auto;
	}
	#imagesCarousel img{
		width: auto!important;
		height: auto!important;
		max-height: calc(100%)!important;
		max-width: calc(100%)!important;
	}
*   {
    box-sizing: border-box;
}

.fab-wrapper {
    position: fixed;
    bottom: 3rem;
    right: 3rem;
}
.fab-checkbox {
/*    display: none;*/
}
.fab {
  position: absolute;
  bottom: -1rem;
  right: -1rem;
  width: 4rem;
  height: 4rem;
  background: blue;
  border-radius: 50%;
  background: #126ee2;
  box-shadow: 0px 5px 20px #81a4f1;
  transition: all 0.3s ease;
  z-index: 1;
  border-bottom-right-radius: 6px;
  border: 1px solid #0c50a7;
}

.fab:before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  left: 0;
  top: 0;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.1);
}
.fab-checkbox:checked ~ .fab:before {
  width: 90%;
  height: 90%;
  left: 5%;
  top: 5%;
  background-color: rgba(255, 255, 255, 0.2);
}
.fab:hover {
  background: #2c87e8;
  box-shadow: 0px 5px 20px 5px #81a4f1;
}

.fab-dots {
  position: absolute;
  height: 8px;
  width: 8px;
  background-color: white;
  border-radius: 50%;
  top: 50%;
  transform: translateX(0%) translateY(-50%) rotate(0deg);
  opacity: 1;
  animation: blink 3s ease infinite;
  transition: all 0.3s ease;
}

.fab-dots-1 {
  left: 15px;
  animation-delay: 0s;
}
.fab-dots-2 {
  left: 50%;
  transform: translateX(-50%) translateY(-50%);
  animation-delay: 0.4s;
}
.fab-dots-3 {
  right: 15px;
  animation-delay: 0.8s;
}

.fab-checkbox:checked ~ .fab .fab-dots {
  height: 6px;
}

.fab .fab-dots-2 {
  transform: translateX(-50%) translateY(-50%) rotate(0deg);
}

.fab-checkbox:checked ~ .fab .fab-dots-1 {
  width: 32px;
  border-radius: 10px;
  left: 50%;
  transform: translateX(-50%) translateY(-50%) rotate(45deg);
}
.fab-checkbox:checked ~ .fab .fab-dots-3 {
  width: 32px;
  border-radius: 10px;
  right: 50%;
  transform: translateX(50%) translateY(-50%) rotate(-45deg);
}

@keyframes blink {
  50% {
    opacity: 0.25;
  }
}

.fab-checkbox:checked ~ .fab .fab-dots {
  animation: none;
}

.fab-wheel {
  position: absolute;
  bottom: 0;
  right: 0;
  /*border: 1px solid #fff;*/
  width: 12rem;
  height: 12rem;
  transition: all 0.3s ease;
  transform-origin: bottom right;
  transform: scale(0);
}

.fab-checkbox:checked ~ .fab-wheel {
  transform: scale(1);
}
.fab-action {
  position: absolute;
  background: #0f1941;
  width: 4.5rem;
  height: 4.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 0.1rem 1rem rgba(24, 66, 154, 0.82);
  transition: all 1s ease;
  opacity: 0;
}

.fab-checkbox:checked ~ .fab-wheel .fab-action {
  opacity: 1;
}

.fab-action:hover {
  background-color: #f16100;
}

.fab-wheel .fab-action-1 {
  right: -1rem;
  top: 0;
}

.fab-wheel .fab-action-2 {
  /*right: 2.4rem;*/
  /*bottom: 3.5rem;*/
  right: 2.3rem;
  top: 3.5rem;
}
.fab-wheel .fab-action-3 {
  left: 0.5rem;
  bottom: 3.4rem;
}
.fab-wheel .fab-action-4 {
  /*left: 0;
  bottom: -1rem;*/
  left: 0.5rem;
  bottom: 3.4rem;
}
</style>

<div class="containe-fluid">
	<div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_firstname']." ". $_SESSION['login_lastname']."!"  ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-body bg-primary">
                                    <div class="card-body text-white">
                                        <span class="float-right summary_icon"> <i class="fa fa-message "></i></span>
                                        <h4><b>
                                            1
                                        </b></h4>
                                        <p><b>My Apartment Details</b></p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="index.php?page=apartment_details" class="text-primary float-right">View List <span class="fa fa-angle-right"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body bg-warning">
                                    <div class="card-body text-white">
                                        <span class="float-right summary_icon"> <i class="fa fa-user-friends "></i></span>
                                        <h4><b>.
                                        </b></h4>
                                        <p><b>Chat</b></p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="index.php?page=chat" class="text-primary float-right">View List <span class="fa fa-angle-right"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body bg-success">
                                    <div class="card-body text-white">
                                        <span class="float-right summary_icon"> <i class="fa fa-file-invoice "></i></span>
                                        <h4><b>
                                            <?php 
                                             $payment = $conn->query("SELECT sum(amount) as paid FROM payments where date(date_created) = '".date('Y-m-d')."' "); 
                                             echo $payment->num_rows > 0 ? number_format($payment->fetch_array()['paid'],2) : 0;
                                             ?>
                                        </b></h4>
                                        <p><b>Payments This Month</b></p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="index.php?page=invoices" class="text-primary float-right">View Payments <span class="fa fa-angle-right"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-bell fa-fw"></i><strong>Your Recent Payments</strong>
                                </div>
                                <!-- /.panel-heading -->
                                <div class="panel-body">
                                    <div class="list-group">
                                        <div class="card-body">
                                            <table class="table table-condensed table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th class="">Apartment No Rented</th>
                                                        <th class="">Monthly Rate</th>
                                                        <th class="">Outstanding Balance</th>
                                                        <!-- <th class="">Payment Date</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $user_id = $_SESSION['login_id'];
                                                    $i = 1;
                                                    $i = 1;
                                                    $tenant = $conn->query("
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
                                                    LIMIT 3
                                                    ");
                                                    while($row=$tenant->fetch_assoc()):
                                                        $months = abs(strtotime(date('Y-m-d')." 23:59:59") - strtotime($row['date_in']." 23:59:59"));
                                                        $months = floor(($months) / (30*60*60*24));
                                                        $payable = $row['price'] * $months;
                                                        $paid = $conn->query("SELECT SUM(amount) as paid FROM payments where tenant_id =".$row['tenant_id']);
                                                        $last_payment = $conn->query("SELECT * FROM payments where tenant_id =".$row['tenant_id']." order by unix_timestamp(date_created) desc limit 1");
                                                        $paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
                                                        // $last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['date_created'])) : 'N/A';
                                                        $outstanding = $payable - $paid * 0;
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $i++ ?></td>
                                                        <td class="">
                                                             <p> <b><?php echo $row['apartment_no'] ?></b></p>
                                                        </td>
                                                        <td class="">
                                                             <p> <b><?php echo number_format($row['price'],2) ?></b></p>
                                                        </td>
                                                        <td class="text-right">
                                                             <p> <b><?php echo number_format($outstanding,2) ?></b></p>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <a href="payments_view.php" class="btn btn-success btn-block">View All Payments</a>
                                </div>
                            </div>
                        </div><!--/row ends-->
                        <div class="col-md-4 mb-3">
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <i class="fa fa-bell fa-fw"></i> <strong>Payment Reminder</strong>
                                </div>
                                <hr>
                                <div class="panel-body">
                                    <div class="list-group">
                                        <div class="checkbox">
                                        <?php
                                        $user_id = $_SESSION['login_id'];
                                        $tenant_result = $conn->query("SELECT * FROM notifications WHERE status = 'Not Paid' AND tenant_id = '$user_id'");
                                        if ($tenant_result->num_rows == 1) {
                                            $row = $tenant_result->fetch_assoc(); 
                                            
                                            // Convert the database date to a timestamp
                                            $payment_due_timestamp = strtotime($row['payment_due']);
                                            
                                            // Get the current timestamp
                                            $current_timestamp = time();
                                            
                                            // Calculate the difference in seconds
                                            $difference_seconds = $payment_due_timestamp - $current_timestamp;
                                            
                                            // Convert the difference to days
                                            $difference_days = floor($difference_seconds / (60 * 60 * 24));
                                            
                                            if ($difference_days > 0) {
                                                echo "<i class='fa fa-danger fa-fw'></i><label for='checkbox' style='color: red; font-weight: bold;'>$difference_days days remaining</label>
                                                <div class='panel-footer'>
                                                    <div class='input-group'>
                                                        <span class='input-group-btn'>
                                                            <a href='index.php?page=pay' class='btn btn-primary btn-md' id='btn-todo'>I WANT TO PAY</a>
                                                        </span>
                                                    </div>
                                                </div>";
                                            } elseif ($difference_days < 0) {
                                                echo "<i class='fa fa-danger fa-fw'></i><label for='checkbox' style='color: red; font-weight: bold;'>" . abs($difference_days) . " days exceeded</label>
                                                <div class='panel-footer'>
                                                    <div class='input-group'>
                                                        <span class='input-group-btn'>
                                                            <a href='index.php?page=pay' class='btn btn-primary btn-md' id='btn-todo'>I WANT TO PAY</a>
                                                        </span>
                                                    </div>
                                                </div>";
                                            } else {
                                                echo "<i class='fa fa-danger fa-fw'></i><label for='checkbox' style='color: red; font-weight: bold;'>Payment due today</label>
                                                <div class='panel-footer'>
                                                    <div class='input-group'>
                                                        <span class='input-group-btn'>
                                                            <a href='index.php?page=pay' class='btn btn-primary btn-sm btn-md' id='btn-todo'>PAY</a>
                                                        </span>
                                                    </div>
                                                </div>";
                                            }
                                        } else {
                                            echo "<i class='fa fa-check fa-fw'></i><label for='checkbox' style='color: green; font-weight: bold;'>You paid your rent</label>";
                                        }
                                        ?>


  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>      			
        </div>
    </div>
    <div class="fab-wrapper">
        <input id="fabCheckbox" type="checkbox" class="fab-checkbox" />
        <label class="fab" for="fabCheckbox">
            <span class="fab-dots fab-dots-1"></span>
            <span class="fab-dots fab-dots-2"></span>
            <span class="fab-dots fab-dots-3"></span>
        </label>
    </div>
</div>
<script>
	$('#manage-records').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                resp=JSON.parse(resp)
                if(resp.status==1){
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function(){
                        location.reload()
                    },800)

                }
                
            }
        })
    })
    $('#tracking_id').on('keypress',function(e){
        if(e.which == 13){
            get_person()
        }
    })
    $('#check').on('click',function(e){
            get_person()
    })
    function get_person(){
            start_load()
        $.ajax({
                url:'ajax.php?action=get_pdetails',
                method:"POST",
                data:{tracking_id : $('#tracking_id').val()},
                success:function(resp){
                    if(resp){
                        resp = JSON.parse(resp)
                        if(resp.status == 1){
                            $('#name').html(resp.name)
                            $('#address').html(resp.address)
                            $('[name="person_id"]').val(resp.id)
                            $('#details').show()
                            end_load()

                        }else if(resp.status == 2){
                            alert_toast("Unknow tracking id.",'danger');
                            end_load();
                        }
                    }
                }
            })
    }
</script>