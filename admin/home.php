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
</style>

<div class="containe-fluid">
	<div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_name']."!"  ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-body bg-primary">
                                    <div class="card-body text-white">
                                        <span class="float-right summary_icon"> <i class="fa fa-home "></i></span>
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM apartments")->num_rows ?>
                                        </b></h4>
                                        <p><b>Total Apartments</b></p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="index.php?page=houses" class="text-primary float-right">View List <span class="fa fa-angle-right"></span></a>
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
                                        <h4><b>
                                            <?php echo $conn->query("SELECT * FROM users where user_type = 2 ")->num_rows ?>
                                        </b></h4>
                                        <p><b>Total Tenants</b></p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="index.php?page=tenants" class="text-primary float-right">View List <span class="fa fa-angle-right"></span></a>
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
                                    <i class="fa fa-bell fa-fw"></i><strong>Recent Payments</strong>
                                </div>
                                <!-- /.panel-heading -->
                                <div class="panel-body">
                                    <div class="list-group">
                                        <div class="card-body">
                                            <table class="table table-condensed table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th class="">Name</th>
                                                        <th class="">Apartment Rented</th>
                                                        <th class="">Monthly Rate</th>
                                                        <th class="">Outstanding Balance</th>
                                                        <th class="">Last Payment</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
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
                                                    LIMIT 3
                                                    ");
                                                    while($row=$tenant->fetch_assoc()):
                                                        $months = abs(strtotime(date('Y-m-d')." 23:59:59") - strtotime($row['date_in']." 23:59:59"));
                                                        $months = floor(($months) / (30*60*60*24));
                                                        $payable = $row['price'] * $months;
                                                        $paid = $conn->query("SELECT SUM(amount) as paid FROM payments where tenant_id =".$row['tenant_id']);
                                                        $last_payment = $conn->query("SELECT * FROM payments where tenant_id =".$row['tenant_id']." order by unix_timestamp(date_created) desc limit 1");
                                                        $paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
                                                        $last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['date_created'])) : 'N/A';
                                                        $outstanding = $payable - $paid * 0;
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $i++ ?></td>
                                                        <td>
                                                            <?php echo $row['tenant_firstname'] ?> <?php echo $row['tenant_middlename'] ?> <?php echo $row['tenant_lastname'] ?>
                                                        </td>
                                                        <td class="">
                                                             <p> <b><?php echo $row['apartment_no'] ?></b></p>
                                                        </td>
                                                        <td class="">
                                                             <p> <b><?php echo number_format($row['price'],2) ?></b></p>
                                                        </td>
                                                        <td class="text-right">
                                                             <p> <b><?php echo number_format($outstanding,2) ?></b></p>
                                                        </td>
                                                        <td class="">
                                                             <p><b><?php echo  $last_payment ?></b></p>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <a href="index.php?page=payment_report" class="btn btn-success btn-block">View All Payments</a>
                                </div>
                            </div>
                        </div><!--/row ends-->
                        <div class="col-md-4 mb-3">
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <i class="fa fa-bell fa-fw"></i> <strong>Rent Payment Reminder</strong>
                                </div>
                                <hr>
                                <div class="panel-body">
                                    <div class="list-group">
                                        <div class="checkbox">
                                            <!-- <i class='fa fa-danger fa-fw'></i><label for='checkbox' style='color: red; font-weight: bold;'>Payment due today</label> -->
                                            <div class='panel-footer'>
                                                <div class='input-group'>
                                                    <span class='input-group-btn'>
                                                        <a href='index.php?page=send_reminders' class='btn btn-primary btn-sm btn-md' id='btn-todo'>Send Reminders</a>
                                                    </span>
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
        </div>
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