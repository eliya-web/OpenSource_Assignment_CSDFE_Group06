<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "incident_db";
$port = 3307;

$conn = @mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    echo "Error: " . mysqli_connect_error() . " (errno: " . mysqli_connect_errno() . ")";
} else {
    echo "SUCCESS: Connected to MySQL!";
    mysqli_close($conn);
}
?>
