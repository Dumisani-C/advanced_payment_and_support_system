<?php include('db_connect.php');?>
<link rel="stylesheet" href="style.css">
<div class="container-fluid">
    
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div>

        <div class="row">
        <div class="col-lg-12 col-md-6 mb-20">
			    <div class="wrapper">
			      <section class="chat-area">
			        <header>
			          <?php 
			            $user_id = mysqli_real_escape_string($conn, $_GET['id']);
			            $sql = mysqli_query($conn, "SELECT * FROM users WHERE id = {$user_id}");
			            if(mysqli_num_rows($sql) > 0){
			              $row = mysqli_fetch_assoc($sql);
			            }else{
			              header("location: index.php?page=chat");
			            }
			          ?>
			          <a href="index.php?page=chat" class="back-icon"><i class="fa fa-arrow-left"></i></a>
			          <img src="php/profile.jpg" alt="" style="width:50px; height: 50px;">
			          <div class="details">
			            <span><?php echo $row['firstname']; ?> <?php echo $row['lastname']; ?></span>
			            <p><?php echo $row['status']; ?></p>
			          </div>
			        </header>
			        <div class="chat-box">

			        </div>
			        <form action="#" class="typing-area">
			          <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
			          <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off" required>
			          <button><span class="fa fa-arrow-right"></span></button>
			        </form>
			      </section>
			    </div>
        </div>
      </div>
    </div>
</div>

	<script src="javascript/chat.js"></script>
	<script src="javascript/users.js"></script>
</body>
</html>