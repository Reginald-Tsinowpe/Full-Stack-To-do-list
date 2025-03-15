<?php
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "<h1 style='color: red; text-align: center;'>Unauthorized Access. Please <a href='./../index.html'>Login</a> first.</h1>";
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

// Get the logged-in user's username
$uname = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM `tbl_user_task_data` WHERE `uname`=? ORDER BY `expiry_date` ASC");
$stmt->bind_param('s', $uname);
$stmt->execute();

$result = $stmt->get_result();
$tasks = [];

while ($row = $result->fetch_assoc()) {
    $row['tags'] = json_decode($row['tags']); // Decode JSON tags
    $tasks[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($tasks);
?>

