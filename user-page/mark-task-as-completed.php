<?php
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "<h1 style='color: red; text-align: center;'>Unauthorized Access. Please <a href='./../index.html'>Login</a> first.</h1>";
    exit;
}

require_once "../config/config.php"; 

// Get the logged-in user's username
$uname = $_SESSION['username'];
$data = json_decode(file_get_contents("php://input"), true);
$task_id = intval($data['task_id']);
$today = date("Y-m-d");

$stmt = $conn->prepare("UPDATE tbl_user_task_data SET task_completed = TRUE, `expiry_date` = ? WHERE task_id = ?");
$stmt->bind_param("si", $today, $task_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task marked as completed."]);
} else {
    echo json_encode(["error" => "Failed to update task: "]);
}


$stmt->close();
$conn->close();

?>