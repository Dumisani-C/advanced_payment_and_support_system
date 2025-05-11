<?php
require_once 'vendors/autoload.php';
include("config.php");
include("db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; 

$token = $_POST["stripeToken"];
$contact_name = $_POST["c_name"];
$token_card_type = $_POST["stripeTokenType"];
$email           = $_POST["stripeEmail"];
$amount          = $_POST["amount"]; 
$desc            = $_POST["product_name"];
$user_id         = $_POST["user_id"];
$apartment_no    = $_POST["apartment_no"];

//Save to stripe database
$charge = \Stripe\Charge::create([
  "amount" => str_replace(",","",$amount) * 100,
  "currency" => 'inr',
  "description"=>$desc,
  "source" => $token,
]);

$dateNow = date('Y-m-d');

$payment_query = mysqli_query($conn, "INSERT INTO payments (tenant_id, apartment_no, amount, date_created) VALUES ('$user_id', '$apartment_no','$amount','$dateNow')");

$payment_query2 = mysqli_query($conn, "UPDATE notifications SET status = 'Paid' WHERE tenant_id = '$user_id' AND status = 'Not Paid'");

if ($payment_query && $payment_query2) {
  $mail = new PHPMailer(true);
  try {
    //Server settings
    // SMTP - Simple Mail Transfer Protocol - A feature used to send and receive emails.
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                    //Enable verbose debug output
    $mail->isSMTP();                                       //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                  //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                              //Enable SMTP authentication
    $mail->Username   = 'dodma265@gmail.com';      //SMTP username
    $mail->Password   = 'acpwexlgrlqieiug';                //Enable SMTP authentication
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    //Enable implicit TLS encryption
    $mail->Port       = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients   
    $mail->setFrom('dodma265@gmail.com', 'Rent Payment Success');
    $mail->addAddress($email);  

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = 'Payment Successful!';
    $mail->Body    = '<p><span style="font-weight: bold; font-size: 20px;">Welcome to ARK APartment Rentals</span><br> 
      <p style="font-size: 16px;">Dear Mr/Mrs/Miss '.$contact_name.',<br><br>
      You have successfully paid your apartment rent, apartment no '.$apartment_no.' with the amount of MKW'.$amount.'.<br><br>
      With thanks,<br>
      ARK Apartments</p><br>
      <span style="font-weight: bold; font-size: 16px;">Need Help?</span><br>Call Appie Mbewe';
    $mail->send();
    } 
    catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  // Message for successful payment
  header("Location: success.php?amount=$amount");
}else{
  // Message for unsuccessfull payment
  echo "<script>window.alert('Something went wrong! Try Again!')</script>";
  // echo "<script>window.location.href='checkout.php';</script>";
}
?>