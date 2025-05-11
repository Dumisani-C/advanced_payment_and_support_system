<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
			<form action="" id="manage-apartment">
				<div class="card">
					<div class="card-header">
						    Apartment Form
				  	</div>
					<div class="card-body">
							<div class="form-group" id="msg"></div>
							<input type="hidden" name="id">
							<div class="form-group">
								<label class="control-label">Apartment No</label>
								<input type="text" class="form-control" name="apartment_no" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Category</label>
								<select name="category_id" id="" class="custom-select" required>
									<?php 
									$categories = $conn->query("SELECT * FROM categories order by name asc");
									if($categories->num_rows > 0):
									while($row= $categories->fetch_assoc()) :
									?>
									<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
								<?php endwhile; ?>
								<?php else: ?>
									<option selected="" value="" disabled="">Please check the category list.</option>
								<?php endif; ?>
								</select>
							</div>
							<div class="form-group">
								<label for="" class="control-label">Description</label>
								<textarea name="description" id="" cols="30" rows="4" class="form-control" required></textarea>
							</div>
							<div class="form-group">
								<label class="control-label">Price</label>
								<input type="number" class="form-control text-right" name="price" step="any" required="">
							</div>
					</div>
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Save</button>
								<button class="btn btn-sm btn-default col-sm-3" type="reset" > Cancel</button>
							</div>
						</div>
					</div>
				</div>
			</form>
			</div>
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<b>Apartment List</b>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Apartment</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$apartment = $conn->query("SELECT p.*,c.name as cname FROM apartments p inner join categories c on c.id = p.category_id order by id DESC");
								while($row=$apartment->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p>Apartment #: <b><?php echo $row['apartment_no'] ?></b></p>
										<p><small>Apartment Type: <b><?php echo $row['cname'] ?></b></small></p>
										<p><small>Description: <b><?php echo $row['description'] ?></b></small></p>
										<p><small>Price: <b><?php echo number_format($row['price'],2) ?></b></small></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_apartment" type="button" data-id="<?php echo $row['id'] ?>"  data-apartment_no="<?php echo $row['apartment_no'] ?>" data-description="<?php echo $row['description'] ?>" data-category_id="<?php echo $row['category_id'] ?>" data-price="<?php echo $row['price'] ?>" >Edit</button>
										<button class="btn btn-sm btn-danger delete_apartment" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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
	td p {
		margin: unset;
		padding: unset;
		line-height: 1em;
	}
</style>
<script>
	$('#manage-apartment').on('reset',function(e){
		$('#msg').html('')
	})
	$('#manage-apartment').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_apartment',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Apartment successfully updated",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
				else if(resp==2){
					$('#msg').html('<div class="alert alert-danger">Apartment number already exist.</div>')
					end_load()
				}
			}
		})
	})
	$('.edit_apartment').click(function(){
		start_load()
		var cat = $('#manage-apartment')
		cat.get(0).reset()
		cat.find("[name='id']").val($(this).attr('data-id'))
		cat.find("[name='apartment_no']").val($(this).attr('data-apartment_no'))
		cat.find("[name='description']").val($(this).attr('data-description'))
		cat.find("[name='price']").val($(this).attr('data-price'))
		cat.find("[name='category_id']").val($(this).attr('data-category_id'))
		end_load()
	})
	$('.delete_apartment').click(function(){
		_conf("Are you sure to delete this apartment?","delete_apartment",[$(this).attr('data-id')])
	})
	function delete_apartment($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_apartment',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Apartment successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
	$('table').dataTable()
</script>