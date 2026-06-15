<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $incident_id = "INC-" . strtoupper(uniqid());
    $incident_title = mysqli_real_escape_string($conn, $_POST['incident_title']);
    $incident_type = mysqli_real_escape_string($conn, $_POST['incident_type']);
    $severity = mysqli_real_escape_string($conn, $_POST['severity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $reporter_name = mysqli_real_escape_string($conn, $_SESSION['full_name']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "INSERT INTO incidents (incident_id, incident_title, incident_type, severity, description, reporter_name, status) 
            VALUES ('$incident_id', '$incident_title', '$incident_type', '$severity', '$description', '$reporter_name', '$status')";

    if (mysqli_query($conn, $sql)) {
        $success = "Incident registered successfully! Incident ID: $incident_id";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Incident</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Register Security Incident</h1>
        <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | <a href="logout.php">Logout</a></p>
        <a href="index.php">Back to Menu</a>
        <?php
        if (isset($success)) echo "<p style='color:green;'>$success</p>";
        if (isset($error)) echo "<p style='color:red;'>$error</p>";
        ?>
        <form method="post">
            <label>Incident Title:</label>
            <input type="text" name="incident_title" required>

            <label>Incident Type:</label>
            <select name="incident_type" required>
                <option value="">Select Type</option>
                <option value="Phishing">Phishing</option>
                <option value="Malware">Malware</option>
                <option value="DDoS">DDoS Attack</option>
                <option value="Unauthorized Access">Unauthorized Access</option>
                <option value="Data Breach">Data Breach</option>
                <option value="Social Engineering">Social Engineering</option>
                <option value="Other">Other</option>
            </select>

            <label>Severity:</label>
            <select name="severity" required>
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
                <option value="Critical">Critical</option>
            </select>

            <label>Description:</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Status:</label>
            <select name="status">
                <option value="Open">Open</option>
                <option value="Investigating">Investigating</option>
                <option value="Resolved">Resolved</option>
                <option value="Closed">Closed</option>
            </select>

            <button type="submit">Register Incident</button>
        </form>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #7f8c8d; font-size: 12px;">
        &copy; 2026 CP 222 Open Source Technologies | Cyber Security & Digital Forensics Engineering
    </div>
</body>
</html>