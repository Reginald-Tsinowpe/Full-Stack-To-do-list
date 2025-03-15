<?php   
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // User is not authenticated, redirect to login page
    header("Location: ./../index.html");
    exit;
}

    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $db_name = 'db_to_do_list';

    $conn = new mysqli($hostname, $username, $password, $db_name);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

// Get the logged-in user's data
$uname = $_SESSION['username'];



// FUNCTION TO INSERT INTO TABLE RUN BY JS
function Create_New_Task($uname, $conn, $task_title, $task_expiry, $task_tags){
    $task_tags_json = json_encode($task_tags);

    $stmt = $conn->prepare("INSERT INTO `tbl_user_task_data` (`uname`, `title`, `expiry_date`, `tags`)
     VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $uname, $task_title, $task_expiry, $task_tags_json);
    $stmt->execute();
    
    $stmt->close();
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['task_title'], $_POST['task_expiry'], $_POST['tags'])) {
        $task_title = trim($_POST['task_title']);
        $task_expiry = $_POST['task_expiry'];
        $task_tags = $_POST['tags']; // This is an array

        Create_New_Task($uname, $conn, $task_title, $task_expiry, $task_tags);

        echo json_encode(["success" => true, "message" => "Task added successfully"]);
            exit;
    }
}



//echo "<h2>Welcome, " . htmlspecialchars($username) . "!</h2>";

?>