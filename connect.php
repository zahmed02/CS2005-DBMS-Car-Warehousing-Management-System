<?php
session_start();
// Insert data into the database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "db_project";

// Create a database connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date_time = date('Y-m-d H:i:s');

?>