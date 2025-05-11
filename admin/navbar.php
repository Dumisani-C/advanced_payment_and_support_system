
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
		<a href="index.php?page=apartments" class="nav-item nav-apartments"><span class='icon-field'><i class="fa fa-home "></i></span> Apartments</a>
		<a href="index.php?page=tenants" class="nav-item nav-tenants"><span class='icon-field'><i class="fa fa-user-friends "></i></span> Tenants</a>
		<a href="index.php?page=chat" class="nav-item nav-chat"><span class='icon-field'><i class="fa fa-chat "></i></span> Chat</a>
		<a href="index.php?page=reports" class="nav-item nav-reports"><span class='icon-field'><i class="fa fa-list-alt "></i></span> Reports</a>
		<?php if($_SESSION['login_user_type'] == 1): ?>
		<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users "></i></span> Users</a>
	<?php endif; ?>
	</div>
</nav>
<script>
	$('.nav_collapse').click(function(){
		console.log($(this).attr('href'))
		$($(this).attr('href')).collapse()
	})
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>
