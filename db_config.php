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
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self';");

// ===== Session Inactivity Timeout (30 min) =====
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        $_SESSION = [];
        session_destroy();
        session_start();
        $_SESSION['expired'] = "Your session has expired. Please sign in again.";
        header("Location: login.php");
        exit();
    }
    $_SESSION['last_activity'] = time();
}

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
