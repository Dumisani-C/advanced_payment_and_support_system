<?php 
include 'db_connect.php'; 
if(isset($_GET['id'])){
$qry = $conn->query("
    SELECT
        u.*,ta.*
    FROM
        users u
    INNER JOIN
        tenantapartment ta ON u.id = ta.tenant_id
    WHERE
        u.id = ".$_GET['id']
);

// $qry = $conn->query("SELECT * FROM users where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<form action="" id="manage-tenant">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="row form-group">
			<div class="col-md-4">
				<label for="" class="control-label">First Name</label>
				<input type="text" class="form-control" name="firstname"  value="<?php echo isset($firstname) ? $firstname :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Middle Name</label>
				<input type="text" class="form-control" name="middlename"  value="<?php echo isset($middlename) ? $middlename :'' ?>">
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Last Name</label>
				<input type="text" class="form-control" name="lastname"  value="<?php echo isset($lastname) ? $lastname :'' ?>" required>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Email</label>
				<input type="email" class="form-control" name="email"  value="<?php echo isset($email) ? $email :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Contact #</label>
				<input type="text" class="form-control" name="contact"  value="<?php echo isset($contact) ? $contact :'' ?>" required>
			</div>				
		</div>
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Apartment No</label>
				<select name="apartment_id" id="" class="custom-select select2">
					<option value=""></option>
					<?php 
					$apartment = $conn->query("SELECT * FROM apartments where id not in (SELECT apartment_id from tenantapartment where status = 1) ".(isset($apartment_id)? " or id = $apartment_id": "" )." ");
					while($row= $apartment->fetch_assoc()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($apartment_id) && $apartment_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['apartment_no'] ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Registration Date</label>
				<input type="date" class="form-control" name="date_in"  value="<?php echo isset($date_in) ? date("Y-m-d",strtotime($date_in)) :'' ?>" required>
			</div>
		</div>
	</form>
</div>
<script>
	$('#manage-tenant').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_tenant',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Tenant successfully updated.",'success')
						setTimeout(function(){
							location.reload()
					},1000)
				}
			}
		})
	})
</script>