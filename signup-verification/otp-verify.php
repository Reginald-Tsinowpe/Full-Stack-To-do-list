<?php

    $hostname = 'localhost';
    $hostusername = 'root';
    $hostpassword = '';
    $db_name = 'db_to_do_list';

    $conn = new mysqli($hostname, $hostusername, $hostpassword, $db_name);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    if (isset($_GET['username'])) {
        $username = htmlspecialchars($_GET['username']); // Prevent XSS attacks
    } else {
        echo "No username provided!";
        exit;
    }

    if (isset($_GET['vtype'])) {
        $vtype = htmlspecialchars($_GET['vtype']); // Prevent XSS attacks
    } else{
        echo "Access type unspecified";
        exit;
    }

    $user_otp = $_POST['otp-code'];

    if ($vtype == "signup"){
        $stmt = $conn->prepare("SELECT * FROM `temporary_user_signup` WHERE `uname` = ?");
    } else if($vtype == "login"){
        $stmt = $conn->prepare("SELECT * FROM `tbl_user_credentials` WHERE `uname` = ?");
    }
    
    $stmt->bind_param('s', $username);
    $stmt->execute();

    $result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_info = $result->fetch_assoc();

    $stored_otp = $user_info['otp_code']; // Get stored OTP
    $expiry_time = $user_info['otp_expiry'];



    // Verify OTP
    if ($user_otp == $stored_otp) {
        if (strtotime($expiry_time) > time()) {

            if($vtype == "signup"){
    // ✅ Step 1: Insert into `users` table
                $insert_stmt = $conn->prepare("INSERT INTO `tbl_user_credentials` (`uname`, `fname`, `lname`, `email`, `password`, `account_creation_date`) 
                VALUES (?, ?, ?, ?, ?, NOW())");
                $insert_stmt->bind_param("sssss", 
                    $user_info['uname'], 
                    $user_info['fname'], 
                    $user_info['lname'], 
                    $user_info['email'], 
                    $user_info['password']
                );
                $insert_stmt->execute();
                $insert_stmt->close();
    
                // ✅ Step 2: Clear OTP fields in `temporary_user_signup`
                $update_stmt = $conn->prepare("UPDATE `temporary_user_signup` SET `otp_code` = NULL, `otp_expiry` = NULL WHERE `uname` = ?");
                $update_stmt->bind_param('s', $username);
                $update_stmt->execute();
                $update_stmt->close();
    
                // ✅ (Optional) Step 3: Delete user from `temporary_user_signup`
                // Uncomment the following lines if you want to remove the user from temporary table

                $delete_stmt = $conn->prepare("DELETE FROM `temporary_user_signup` WHERE `uname` = ?");
                $delete_stmt->bind_param('s', $username);
                $delete_stmt->execute();
                $delete_stmt->close();

            } else if ($vtype == "login"){
                $update_stmt = $conn->prepare("UPDATE `tbl_user_credentials` SET `otp_code` = NULL, `otp_expiry` = NULL WHERE `uname` = ?");
                $update_stmt->bind_param('s', $username);
                $update_stmt->execute();
                $update_stmt->close();
            }
                

    
                // ✅ Redirect to user page
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;

            header("Location: ./../user-page/index.html");//open with the user's username
            exit;
        } else {
            echo "OTP Expired!";
        }
    } else {
        echo "Invalid OTP!";
    }
} else {
    echo json_encode(["error" => "User not found"]);
}



$stmt->close();
$conn->close();

?>