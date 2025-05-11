<?php 
    session_start();
    if(isset($_SESSION['login_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['login_id'];
        $incoming_id = mysqli_real_escape_string($connection, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM chat LEFT JOIN users ON users.id = chat.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY date_sent ASC";
        $query = mysqli_query($connection, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'. $row['msg'] .'<br> '. $row['date_sent'] .'</p>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                        <img src="php/profile.jpg" alt="" style="width:50px; height: 50px;">
                        <div class="details">
                            <p>'. $row['msg'] .'<br> '. $row['date_sent'] .'</p>
                        </div>
                    </div>'; 
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../index.php?page=chat");
    }

?>