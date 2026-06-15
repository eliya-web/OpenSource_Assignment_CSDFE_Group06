<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Security Incident Reporting System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Security Incident Reporting System</h1>
        <p>Cyber Security & Digital Forensics Engineering</p>
        <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | <a href="logout.php">Logout</a></p>
        <h2>Menu</h2>
        <ul>
            <li><a href="register_incident.php">Register New Incident</a></li>
            <li><a href="display_incidents.php">View All Incidents</a></li>
            <li><a href="search_incident.php">Search Incident by ID</a></li>
        </ul>
    </div>
</body>
</html>