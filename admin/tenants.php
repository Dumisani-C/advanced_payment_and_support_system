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
						<b>List of Tenants</b>
						<span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="index.php?page=register_tenant">
					<i class="fa fa-plus"></i> New Tenant
				</a></span>
					</div>
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
									<th class="text-center">Action</th>
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
								    users ON tenantapartment.tenant_id = users.id;
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
									<td class="text-center">
										<button class="btn btn-sm btn-outline-primary view_payment" type="button" data-id="<?php echo $row['tenant_id'] ?>" >View</button>
										<button class="btn btn-sm btn-outline-primary edit_tenant" type="button" data-id="<?php echo $row['tenant_id'] ?>" >Edit</button>
										<button class="btn btn-sm btn-outline-danger delete_tenant" type="button" data-id="<?php echo $row['tenant_id'] ?>">Delete</button>
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
	
	$('#new_tenant').click(function(){
		uni_modal("New Tenant","manage_tenant.php","mid-large")
		
	})

	$('.view_payment').click(function(){
		uni_modal("Tenants Payments","view_payment.php?id="+$(this).attr('data-id'),"large")
		
	})
	$('.edit_tenant').click(function(){
		uni_modal("Manage Tenant Details","manage_tenant.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_tenant').click(function(){
		_conf("Are you sure to delete this Tenant?","delete_tenant",[$(this).attr('data-id')])
	})
	
	function delete_tenant($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_tenant',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Tenant successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>