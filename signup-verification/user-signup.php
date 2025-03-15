<?php


$hostname = 'localhost';
$username = 'root';
$password = '';
$db_name = 'db_to_do_list';

$conn = new mysqli($hostname, $username, $password, $db_name);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

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
