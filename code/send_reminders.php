<?php
require_once '../code/db_connect.php';
require_once '../code/vendors/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if(isset($_POST['send_reminders'])){
    // Get due payment amount from form
    $due_payment = isset($_POST['due_payment']) ? $_POST['due_payment'] : '';

    // Prepare and execute SQL query to get tenants
    $sql = "SELECT * FROM users WHERE user_type = 2";
    $result = $conn->query($sql);

    // Check if there are tenants
    if ($result && $result->num_rows > 0) {
        // Initialize PHPMailer
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dodma265@gmail.com';          // SMTP username
            $mail->Password   = 'acpwexlgrlqieiug';            // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;// Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 587;                           // TCP port to connect to
            
            // Set email sender
            $mail->setFrom('dodma265@gmail.com', 'ARK Apartments Rent Reminder');
            $mail->isHTML(true);

            // Prepare notification insertion query
            $insert_notification_query = "INSERT INTO notifications (tenant_id, apartment_id, payment_due) VALUES (?, ?, ?)";
            $insert_notification_stmt = $conn->prepare($insert_notification_query);

            // Fetch and process each tenant
            while($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $email = $row['email'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];

                // Fetch apartment details
                $get_apart = $conn->query("SELECT * FROM tenantapartment WHERE tenant_id='$id'");
                $get_details = $get_apart->fetch_assoc();
                $apartment_id = $get_details['apartment_id'];

                $get_apartment = $conn->query("SELECT * FROM apartments WHERE id='$apartment_id'");
                $get_details_apat = $get_apartment->fetch_assoc();
                $apartment_no = $get_details_apat['apartment_no'];

                // Bind parameters for notification insertion
                $insert_notification_stmt->bind_param("iis", $id, $apartment_id, $due_payment);

                // Compose email message
                $mail->Subject = 'Tenant Apartment Payment Reminder';
                $mail->Body = '<p><span style="font-weight: bold; font-size: 16px;">Tenant Apartment Payment Reminder</span><br><br>
                            Dear '.$firstname.' '.$lastname.',<br><br>
                            This is a reminder that you have to pay for your apartment '.$apartment_no.'.<br><br>
                            Best regards,<br><br>
                            ARK Apartments.</p>
                            <br>Call Appie Mbewe';

                // Add recipient
                $mail->addAddress($email);
                
                // Send email
                if ($mail->send()) {
                    // Email sent successfully, proceed with notification insertion
                    $insert_notification_stmt->execute();
                } else {
                    // Error sending email
                    echo "Error: Unable to send email to $email";
                }
                
                // Clear recipients for next iteration
                $mail->clearAddresses();
            }

            // Alert success and redirect
            echo "<script>alert('Payment Reminders sent successfully!');</script>";
            echo "<script>window.location.replace(document.referrer);</script>";
        } catch (Exception $e) {
            // Handle mailer error
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // Alert no tenants found and redirect
        echo "<script>alert('No tenants with balances found.');</script>";
        echo "<script>window.location.replace(document.referrer);</script>";
    }
}
?>
