<?php 
if(isset($_SESSION['login_id']))
header("location:tenant/index.php?page=home");

?>
<!DOCTYPE html>
<html lang="en">

<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Meta Tags -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon" href="images/favicon.ico">

<!--	Fonts
	========================================================-->
<link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">

<!--	Css Link
	========================================================-->
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap-slider.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="css/layerslider.css">
<link rel="stylesheet" type="text/css" href="css/color.css">
<link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="fonts/flaticon/flaticon.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/login.css">
<!-- Vendor CSS Files -->
<!-- <link href="tenant/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="tenant/assets/vendor/icofont/icofont.min.css" rel="stylesheet">
<link href="tenant/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="tenant/assets/vendor/venobox/venobox.css" rel="stylesheet">
<link href="tenant/assets/vendor/animate.css/animate.min.css" rel="stylesheet">
<link href="tenant/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
<link href="tenant/assets/vendor/owl.carousel/assets/owl.carousel.min.css" rel="stylesheet">
<link href="tenant/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="tenant/assets/DataTables/datatables.min.css" rel="stylesheet">
<link href="tenant/assets/css/jquery.datetimepicker.min.css" rel="stylesheet">
<link href="tenant/assets/css/select2.min.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="assets/css/jquery-te-1.4.0.css">
<script src="tenant/assets/vendor/jquery/jquery.min.js"></script>
<script src="tenant/assets/DataTables/datatables.min.js"></script>
<script src="tenant/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="tenant/assets/vendor/jquery.easing/jquery.easing.min.js"></script>
<script src="tenant/assets/vendor/php-email-form/validate.js"></script>
<script src="tenant/assets/vendor/venobox/venobox.min.js"></script>
<script src="tenant/assets/vendor/waypoints/jquery.waypoints.min.js"></script>
<script src="tenant/assets/vendor/counterup/counterup.min.js"></script>
<script src="tenant/assets/vendor/owl.carousel/owl.carousel.min.js"></script>
<script src="tenant/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="tenant/assets/js/select2.min.js"></script>
<script type="text/javascript" src="tenant/assets/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="tenant/assets/font-awesome/js/all.min.js"></script>
<script type="text/javascript" src="tenant/assets/js/jquery-te-1.4.0.min.js" charset="utf-8"></script>
<!-- FOR MORE PROJECTS visit: codeastro.com -->
<!--	Title
	=========================================================-->
<title>Advanced Tenant Payment & Support System</title>
<?php //include('tenant/header.php'); ?>
</head>
<body>

<div id="page-wrapper">
    <div class="row"> 
        <!--	Header start  -->
		<?php include("include/header.php");?>	 		 
        <div class="page-wrappers login-body full-row bg-gray">
            <div class="login-wrapper">
            	<div class="container">
            	<h3  class="register-heading">HELP</h3>
                    <form method="post" action="">
                        <div class="row register-form">
                        	<div class="col-md-6">
                    			<h4  class="register-heading">Admin Account</h4>
                                <div class="form-group">
                                    <h5 style="font-weight: bold;">Admin Login</h5>
                                    <p>On the home page/landing page, click on  <span style="font-weight: bold;">Admin Login</span>. In the login section, put your username and password. Then click <span style="font-weight: bold;">Login</span></p>
                                    <h5 style="font-weight: bold;">Add Apartment</h5>
                                    <p>On the menu bar, click <span style="font-weight: bold;">Apartments</span>, then enter details of an apartment. Click <span style="font-weight: bold;">Save</span></p>
                                    <h5 style="font-weight: bold;">Add Tenants</h5>
                                    <p>On the menu bar, click <span style="font-weight: bold;">Tenants</span>, the page displayed shows list of all tenants. Click <span style="font-weight: bold;">New Tenant</span> then enter details of an tenant. Click <span style="font-weight: bold;">Register</span></p>
                                    <h5 style="font-weight: bold;">Chat</h5>
                                    <p>On the menu bar, click <span style="font-weight: bold;">Chat</span>, then click user to chat with. Write text in the text area click send button.</p>
                                    <h5 style="font-weight: bold;">View Reports</h5>
                                    <p>On the menu bar, click <span style="font-weight: bold;">Reports</span>, then click <span style="font-weight: bold;">Monthly Payment Receipts</span> to view monthly payments. On the other hand, click <span style="font-weight: bold;">Statistical/Analysis Reports</span> to view statistical reports.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                    			<h4  class="register-heading">Tenant Account</h4>
                                <div class="form-group">
                                    <h5 style="font-weight: bold;">Tenant Login</h5>
                                    <p>On the home page/landing page, click on  <span style="font-weight: bold;">Tenant Login</span>. In the login section, put your registered email and password credentials which were sent to your email account. Then click <span style="font-weight: bold;">Login</span></p>
                                    <h5 style="font-weight: bold;">Rent Payment</h5>
                                    <p>On the tenants dashboard, click <span style="font-weight: bold;">Pay</span>, then the page follows, Click <span style="font-weight: bold;">Proceed</span>, then the payment button will appear; click <span style="font-weight: bold;">Pay with Card</span>. Enter your VISA card details then click <span style="font-weight: bold;">Pay</span></p>
                                    <h5 style="font-weight: bold;">Chat</h5>
                                    <p>On the menu bar, click <span style="font-weight: bold;">Chat</span>, then click user to chat with. Write text in the text area click send button.</p>
                                    <h5 style="font-weight: bold;">View Payment History</h5>
                                    <p>On the menu bar, click <span style="font-weight: bold;">Payments</span>, then click view on the selected payament.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	<!--	login  -->
        
        
        <!--	Footer   start--><!-- FOR MORE PROJECTS visit: codeastro.com -->
		<?php include("include/footer.php");?>
		<!--	Footer   start-->
        
        <!-- Scroll to top --> 
        <a href="#" class="bg-secondary text-white hover-text-secondary" id="scroll"><i class="fas fa-angle-up"></i></a> 
        <!-- End Scroll To top --> 
    </div>
</div>
<!-- Wrapper End --> 
<script>
	$('#login-form').submit(function(e){
		e.preventDefault()
		$('#login-form button[type="button"]').attr('disabled',true).html('Logging in...');
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'admin/ajax.php?action=login',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
		$('#login-form button[type="button"]').removeAttr('disabled').html('Login');

			},
			success:function(resp){
				if(resp == 1){
					location.href ='admin/index.php?page=home';
				}else{
					$('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>')
					$('#login-form button[type="button"]').removeAttr('disabled').html('Login');
				}
			}
		})
	})
</script>
<!--	Js Link
============================================================--> 
<script src="js/jquery.min.js"></script> 
<!--jQuery Layer Slider --> 
<script src="js/greensock.js"></script> 
<script src="js/layerslider.transitions.js"></script> 
<script src="js/layerslider.kreaturamedia.jquery.js"></script> 
<!--jQuery Layer Slider --> 
<script src="js/popper.min.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script src="js/owl.carousel.min.js"></script> 
<script src="js/tmpl.js"></script> 
<script src="js/jquery.dependClass-0.1.js"></script> 
<script src="js/draggable-0.1.js"></script> 
<script src="js/jquery.slider.js"></script> 
<script src="js/wow.js"></script> 
<script src="js/custom.js"></script>
</body>
</html>