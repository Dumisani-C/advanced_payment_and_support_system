<?php
require_once 'vendors/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// User when clicks sign in button
if (isset($_POST['register_tenant'])) {
    include 'db_connect.php';

    try {
        $conn->begin_transaction(); // Start transaction

        // Get form data
        $lastname = isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : '';
        $firstname = isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '';
        $middlename = isset($_POST['middlename']) ? htmlspecialchars($_POST['middlename']) : '';
        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $contact = isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : ''; // Hash the password
        $date_in = isset($_POST['date_in']) ? $_POST['date_in'] : '';
        $apartment_id = isset($_POST['apartment_id']) ? $_POST['apartment_id'] : '';
        $user_type = 2;

        // Validate email existence
        $sqlCheckemail = "SELECT email FROM users WHERE email = ?";
        $stmtCheckemail = $conn->prepare($sqlCheckemail);
        $stmtCheckemail->bind_param("s", $email);
        $stmtCheckemail->execute();
        $stmtCheckemail->store_result();
        $emailExists = $stmtCheckemail->num_rows > 0;
        $stmtCheckemail->close();

        if (!$emailExists) {
            // Insert new tenant
            $sqlInsertTenant = "INSERT INTO users (lastname, firstname, middlename, email, contact, password, user_type) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertTenant = $conn->prepare($sqlInsertTenant);
            $stmtInsertTenant->bind_param("ssssssi", $lastname, $firstname, $middlename, $email, $contact, $password, $user_type);
            $stmtInsertTenant->execute();
            $lastInsertedId = $stmtInsertTenant->insert_id; // Get last inserted ID
            $stmtInsertTenant->close();

            // Insert tenant apartment info
            $sqlInsertTenantApartment = "INSERT INTO tenantapartment (tenant_id, date_in, apartment_id) 
                                         VALUES (?, ?, ?)";
            $stmtInsertTenantApartment = $conn->prepare($sqlInsertTenantApartment);
            $stmtInsertTenantApartment->bind_param("iss", $lastInsertedId, $date_in, $apartment_id);
            $stmtInsertTenantApartment->execute();
            $stmtInsertTenantApartment->close();

            $conn->commit(); // Commit transaction

            // Send email to the tenant
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dodma265@gmail.com';
            $mail->Password   = 'acpwexlgrlqieiug';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('dodma265@gmail.com', 'ARK Apartments Tenant Registration');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Welcome to ARK Apartments';
            $mail->Body    = '<p><span style="font-weight: bold; font-size: 20px;">Welcome to Tenant Payment and Support System</span><br> 
                              <p style="font-size: 16px;">Dear Mr/Mrs/Miss '.$firstname.' '.$lastname.'.<br>You have been successfully registered to Tenant Payment and Support System.<br>Your email <span style="font-weight: bold;">'.$email.'</span> and password <span style="font-weight: bold;">'.$_POST['password'].'</span>, use it to login to your account.<br>You have been assigned to apartment number: '.$apartment_id.' on a registered date: '.$date_in.'</p>
                              <br><span style="font-weight: bold; font-size: 20px;">Need Help?</span><br>for more info: ARK APartments';
            $mail->send();

            // Redirect after successful registration
            echo "<script>alert('You have successfully registered tenants account. Please tell the tenant to use details sent to their email to login');</script>";
            echo "<script>window.location.href='index.php?page=tenants'</script>";
            exit();
        } else {
            // Email already exists, display alert and redirect
            echo '<script>alert("Email already exists! Use another email");</script>';
            echo '<script>window.location.replace("index.php?page=register_tenant");</script>';
            exit();
        }

        $conn->close(); // Close connection
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction
        echo "Error: " . $e->getMessage();
    }
}
?>
