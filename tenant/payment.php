<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
		<div class="row">
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>List of Payments</b>
						<span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="index.php?page=pay">
					<i class="fa fa-plus"></i> Pay
				</a></span>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Tenant</th>
									<th class="">Apartment #</th>
									<th class="">Amaount Paid</th>
									<th class="">Payment Date</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
						    <?php 
							    $user_id = $_SESSION['login_id'];
							    $i = 1;
							    $payments = $conn->query("SELECT 
                                    a.apartment_no,
                                    u.firstname AS tenant_firstname,
                                    u.middlename AS tenant_middlename,
                                    u.lastname AS tenant_lastname,
                                    p.amount,
                                    p.date_created,
                                    p.id AS payment_id
                                FROM 
                                    payments p
                                INNER JOIN 
                                    users u ON p.tenant_id = u.id
                                INNER JOIN 
                                    apartments a ON p.apartment_no = a.apartment_no
                                WHERE u.id = '$user_id'");
							    while($row = $payments->fetch_assoc()):
							        ?>
							        <tr>
							            <td class="text-center"><?php echo $i++ ?></td>
							            <td class="">
							                 <p> <b><?php echo $row['tenant_firstname'] ?> <?php echo $row['tenant_middlename'] ?> <?php echo $row['tenant_lastname'] ?></b></p>
							            </td>
							            <td class="">
							                 <p> <b><?php echo $row['apartment_no'] ?></b></p>
							            </td>
							            <td class="text-right">
							                 <p> <b><?php echo number_format($row['amount'], 2) ?></b></p>
							            </td>
							            <td class="">
							                 <p><b><?php echo $row['date_created'] ?></b></p>
							            </td>
							            <td class="text-center">
							                <button class="btn btn-sm btn-outline-primary view_payment" type="button" data-id="<?php echo $row['payment_id'] ?>" >View</button>
							            </td>
							        </tr>
							    <?php endwhile; ?>
							</tbody>

						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p{
		margin: unset
	}
	img{
		max-width:100px;
		max-height: :150px;
	}
</style>
<script>
	$(document).ready(function(){
		$('table').dataTable()
	})
	
	$('#new_payment').click(function(){
		uni_modal("New payment","manage_payment.php","mid-large")
		
	})
	$('.edit_payment').click(function(){
		uni_modal("Manage payment Details","manage_payment.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.view_payment').click(function(){
		uni_modal("Tenant Payments","view_payment.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_payment').click(function(){
		_conf("Are you sure to delete this payment?","delete_payment",[$(this).attr('data-id')])
	})
	
	function delete_payment($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_payment',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>