<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';



// Function to send OTP email
function sendOTPEmail($recipient_email, $otp_code) {
    $mail = new PHPMailer(true); // Enable exceptions

    try {
        // SMTP Configuration
        //$mail->SMTPDebug = 3; // Use 2 or 3 for debugging
        //$mail->Debugoutput = 'html';
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change if using a different provider
        $mail->SMTPAuth = true;
        $mail->Username = 'tsinowpe.business@gmail.com'; // Replace with your email
        $mail->Password = 'uzld pxsp xzpr fzpp'; // Replace with your app password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //$mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;


        // Email Headers
        $mail->setFrom('tsinowpe.business@gmail.com', 'To Do List');
        $mail->addAddress($recipient_email);
        $mail->Subject = 'Your OTP Verification Code';

        // Email Body
        $mail->Body = "Your OTP verification code is: <b>$otp_code</b>. It expires in 10 minutes.";

        $mail->isHTML(true); // Send as HTML email

        // Send the email
        if ($mail->send()) {
            //echo "success: "."OTP sent successfully to $recipient_email";

            // TO DO: CREATE HTML FILE FOR OTP LOGIN, THEN LINK THAT FILE TO OTP-VERIFY
            return ["success" => "OTP sent successfully to $recipient_email"];
        } else {
            return ["error" => "Failed to send OTP"];
        }
    } catch (Exception $e) {
        //echo "erorr: "."Mailer Error: " . $mail->ErrorInfo;
        return ["erorr" => "Mailer Error: " . $mail->ErrorInfo];
    }
}


?>