<?php
$host   = '127.0.0.1';
$port   = 3307;
$user   = 'root';
$pass   = '';
$dbname = 'incident_db';

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
