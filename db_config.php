<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

$host   = '127.0.0.1';
$port   = 3307;
$user   = 'root';
$pass   = '';
$dbname = 'incident_db';

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    error_log("DB connection failed: " . mysqli_connect_error());
    die("System temporarily unavailable. Please try again later.");
}
?>
