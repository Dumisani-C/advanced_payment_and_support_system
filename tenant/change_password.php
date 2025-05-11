
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Update Password</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/datepicker3.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
<!--Icons-->
<script src="js/lumino.glyphs.js"></script>
</head>
<?php
include('db_connect.php');
   
    if(!ISSET($_SESSION['login_id'])){
        header('location:tenant_login.php');
    }
    $query = mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['login_id']."'") or die();
    $row = mysqli_fetch_array($query);

    if(isset($_POST['update_password'])){
    	$currentPassword = $_POST['currentPassword'];
    	$newPassword = $_POST['newPassword'];
    	$confirmPassword = $_POST['confirmPassword'];

    	$updatePassword = mysqli_query($conn,"SELECT * FROM users WHERE password = '$currentPassword' && id ='".$_SESSION['login_id']."'") or die(mysqli_error());

    	$rows = mysqli_num_rows($updatePassword);
    
        if($rows > 0){
        	if($newPassword === $confirmPassword){
        		$newEncryptedPassword = $newPassword;

        		mysqli_query($conn,"UPDATE users SET password = '$newEncryptedPassword' WHERE id='".$_SESSION['login_id']."'") or die(mysqli_error());
			        $message ='<div style="color: green; font-weight: bolder;">You have updated your password successfully</div>';
		        }else{
			    $message ='<div style="color: red; font-weight: bolder;">Your new password and confirmed password is not matching</div>';
		    }
		}
	else{
		$message = '<div style="color: red; font-weight: bolder;">Your old password is wrong</div>';
	}
}
?>
<div class="container-fluid">
    
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div>
						
		<div class="row">
			<div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    
                	<h3>Change Password</h3>		
				     	<div class="panel-heading" style="color: green; font-weight: bolder;">
							<?php if(isset($message)) { 
								echo $message; 
							} 
							?>	
						</div>
						<div class="panel-body">
							<form class="form-horizontal" action="" method="post">
								<fieldset>
									<div class="form-group">
										<label class="col-md-3 control-label" for="name">Current Password</label>
										<div class="col-md-9">
										<input type="password" name="currentPassword" placeholder="Current Password" class="form-control" required="">
										<span id="currentPassword"></span>
										</div>
									</div>
								    <div class="form-group">
										<label class="col-md-3 control-label" for="name">New Password</label>
										<div class="col-md-9">
										<input type="password" name="newPassword" placeholder="New Password" class="form-control" required="">
										</div>
										<span id="newPassword" class="required"></span>
									</div>
									<!-- Email input-->
									<div class="form-group">
										<label class="col-md-3 control-label" for="email">Confirm Password</label>
										<div class="col-md-9">
											<input type="password" name="confirmPassword" placeholder="Confirm Password" class="form-control" required="">
											<span id="confirmPassword" class="required"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-5 control-label" for="email"></label>
	                                    <input type="submit" name="update_password" class="btn btn-info btn-md" value="Update Password">
	                                </div>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div><!--/.col-->
		</div><!--/.row-->
	</div>	<!--/.main-->
</div>	
</body>

</html>
