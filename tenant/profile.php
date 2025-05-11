<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
									
		<div class="row">
			<div class="col-md-10">
				<div class="card">
                    <div class="card-body">	
					     <div class="panel panel-default">
							<h4>Your Profile</h4>
							<div class="panel-body">
								<form class="form-horizontal" action="" method="post">
									<fieldset>
										<div class="form-group">
											<label class="col-md-3 control-label" for="name">Fullname</label>
											<div class="col-md-9">
											<input id="name" name="fullname" type="text" readonly="" placeholder="Your name" value="<?php echo $_SESSION['login_firstname']; ?> <?php echo $_SESSION['login_middlename']; ?> <?php echo $_SESSION['login_lastname']; ?>"  class="form-control">
											</div>
										</div>
										<!-- Email input-->
										<div class="form-group">
											<label class="col-md-3 control-label" for="email">Your E-mail</label>
											<div class="col-md-9">
												<input id="email" name="email" readonly type="text" placeholder="Your email" value="<?php echo $_SESSION['login_email']; ?>" class="form-control">
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="email">Your Phone</label>
											<div class="col-md-9">
												<input id="email" name="phone" readonly type="text" placeholder="Your email" value="<?php echo $_SESSION['login_contact']; ?>" class="form-control">
											</div>
										</div>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div><!--/.col-->
		</div><!--/.row-->
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
</script>
