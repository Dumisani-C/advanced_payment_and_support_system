
<style>
	.collapse a{
		text-indent:10px;
	}
	nav#sidebar{
		/*background: url(assets/uploads/<?php echo $_SESSION['system']['cover_img'] ?>) !important*/
	}
</style>

<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
		<div class="sidebar-list">
				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-tachometer-alt "></i></span> Dashboard</a>
				<a href="index.php?page=chat" class="nav-item nav-chat"><span class='icon-field'><i class="fa fa-chat "></i></span> Chat</a>
				<a href="index.php?page=payment" class="nav-item nav-invoices"><span class='icon-field'><i class="fa fa-file-invoice "></i></span> Payments</a>
				<a href="index.php?page=profile" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users "></i></span> My Profile</a>
				<a href="index.php?page=change_password" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-cogs "></i></span> Change Password</a>
		</div>

</nav>
<script>
	$('.nav_collapse').click(function(){
		console.log($(this).attr('href'))
		$($(this).attr('href')).collapse()
	})
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>
