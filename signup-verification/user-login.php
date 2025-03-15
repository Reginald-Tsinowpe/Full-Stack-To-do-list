<?php
header('Content-Type: application/json');


    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $db_name = 'db_to_do_list';

    $conn = new mysqli($hostname, $username, $password, $db_name);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }


    $uname = $_POST['uname'];
    $upass = $_POST['upassword'];

    $stmt = $conn->prepare("SELECT * FROM `tbl_user_credentials` WHERE `uname`=?");
    $stmt->bind_param('s', $uname);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_info = $result->fetch_assoc();

        $dpass = $user_info['password'];
        $email = $user_info['email'];
        if($upass == $dpass){
            $randigit = mt_rand(100000, 999999);

            
            require 'send-otp.php';
            $result = sendOTPEmail($email, $randigit);

            $expiry_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $otp_stmt = $conn->prepare("UPDATE `tbl_user_credentials` SET  `otp_code`=?, `otp_expiry`=?
            WHERE `uname`=?");

            $otp_stmt->bind_param("sss", $randigit, $expiry_time, $uname);
            $otp_stmt->execute();
            $otp_stmt->close();
            
            //otp verify
            header("Location: ./../verify-otp.html?username=$uname&vtype=login");

            exit;
        }else{
            echo "Incorrect Password";
            exit;
        }

    }else{
        die("Username does not exist!");
    }

    $stmt->close();
    $conn->close();

?>