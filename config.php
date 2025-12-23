<?php
$host = "localhost";
$user = "root";   // change if needed
$pass = "";       // default XAMPP password
$db   = "smriti"; // database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// session_start();
?>
