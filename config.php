<?php
$host = '192.168.1.99'; // MySQL server IP
$port = 3306;
$user = 'root'; // MySQL user
$password = 'Qwer1234'; // MySQL password
$dbname = 'pts_db'; // Database name

// Create a connection
$mysqli = new mysqli($host, $user, $password, $dbname, $port);

// Check the connection
if ($mysqli->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $mysqli->connect_error]));
}

// Log success (optional, tidak mengganggu output API)
error_log("Successfully connected to the database '$dbname' on server '$host'.");

?>
