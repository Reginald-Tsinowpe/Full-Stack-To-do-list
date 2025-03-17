<?php

define("DB_HOSTNAME", getenv('DB_HOST'));
define("DB_USERNAME", getenv('DB_USER'));
define("DB_PASSWORD", getenv('DB_PASS'));
define("DB_NAME", getenv('DB_NAME'));
define("DB_PORT", getenv('DB_PORT') ?: "3306");



//define("DB_HOSTNAME", "localhost");
//define("DB_USERNAME", "root");
//define("DB_PASSWORD", "");
//define("DB_NAME", "db_to_do_list");
//define("DB_PORT", "3306");


$conn = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    
?>
