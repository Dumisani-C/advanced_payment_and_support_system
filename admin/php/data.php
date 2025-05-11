<?php
error_reporting(0);
while ($row = mysqli_fetch_assoc($query)) {
    $sql2 = "SELECT * FROM chat WHERE (incoming_msg_id = {$row['id']}
            OR outgoing_msg_id = {$row['id']}) AND (outgoing_msg_id = {$outgoing_id} 
            OR incoming_msg_id = {$outgoing_id}) ORDER BY chat_id DESC LIMIT 1"; // Get only the last message
    $query2 = mysqli_query($connection, $sql2);
    $row2 = mysqli_fetch_assoc($query2);
    $msg = $row2['msg'];
    $date_sent = $row2['date_sent'];

    if (isset($row2['outgoing_msg_id'])) {
        ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
    } else {
        $you = "";
    }
    ($row['status'] == "Offline") ? $offline = "offline" : $offline = "";
    ($outgoing_id == $row['id']) ? $hid_me = "hide" : $hid_me = "";

    $output .= '<a href="index.php?page=chat_forum&id=' . $row['id'] . '">
        <div class="content">
            <img src="php/profile.jpg" alt="" style="width:50px; height: 50px;">
            <div class="details">
                <span>' . $row['firstname'] . ' ' . $row['lastname'] . '</span>
                <p>' . $you . $msg . '<br><span style="font-size: 12px;">'. $date_sent . '</span></p>
            </div>
        </div>
        <div class="status-dot ' . $offline . '"><i class="fa fa-circle"></i></div>
    </a>';
}
?>
