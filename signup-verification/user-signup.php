<?php


require_once "../config/config.php"; 

$uname = $_POST['username'];
$fname = $_POST['first-name'];
$lname = $_POST['last-name'];
$email = $_POST['email'];
$password = $_POST['password'];
$curr_d_t = date("Y-m-d H:i:s");

// Password length validation
$pass_len = strlen($password);
if ($pass_len < 8 || $pass_len > 16) {
    exit(json_encode(["error" => "Form submission failed. Password must be 8-16 characters long."]));
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit(json_encode(["error" => "Invalid email address"]));
}

// Generate OTP and expiry time
$randigit = mt_rand(100000, 999999);
$expiry_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$check_user_stmt = $conn->prepare("SELECT * FROM `temporary_user_signup` WHERE `uname`=?");
$check_user_stmt->bind_param('s', $uname);
$check_user_stmt->execute();
$concurrent_user = $check_user_stmt->get_result();

if ($concurrent_user->num_rows > 0) {
    $delete_stmt = $conn->prepare("DELETE FROM `temporary_user_signup` WHERE `uname` = ?");
    $delete_stmt->bind_param('s', $uname);
    $delete_stmt->execute();
    $delete_stmt->close();
}
$stmt = $conn->prepare("INSERT INTO `temporary_user_signup` (`uname`, `fname`, `lname`, `email`, `password`, `otp_code`, `otp_expiry`) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssss", $uname, $fname, $lname, $email, $password, $randigit, $expiry_time);
$stmt->execute();
$stmt->close();



// SEND OTP EMAIL

//     DISPLAY LOADING ANIMATION
require 'send-otp.php';
$result = sendOTPEmail($email, $randigit);

$conn->close();

// CHECK IF EMAIL WAS SENT SUCCESSFULLY
if (isset($result['success'])) {
    echo json_encode(["success" => true, "username" => $uname]);
} else {
    echo json_encode(["error" => $result['error']]);
}
?>
