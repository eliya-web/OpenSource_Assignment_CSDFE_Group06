<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid form submission. Please try again.";
    } else {
    $title     = $_POST['incident_title'];
    $type      = $_POST['incident_type'];
    $severity  = $_POST['severity'];
    $desc      = $_POST['description'];
    $reporter  = $_POST['reporter_name'];
    $incident_id = "INC-" . strtoupper(substr(md5(uniqid()), 0, 8));

    $stmt = mysqli_prepare($conn, "INSERT INTO incidents (incident_id, incident_title, incident_type, severity, description, reporter_name, status) VALUES (?, ?, ?, ?, ?, ?, 'Open')");
    mysqli_stmt_bind_param($stmt, "ssssss", $incident_id, $title, $type, $severity, $desc, $reporter);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        $success = "Incident reported successfully! Reference ID: $incident_id";
    } else {
        mysqli_stmt_close($stmt);
        $error = "Something went wrong. Please try again.";
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Incident | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-content" id="pageContent">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-exclamation-triangle"></i> Report Security Incident</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-green"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-red"><i class="fas fa-times-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-heading"></i> Incident Title</label>
                        <input type="text" name="incident_title" placeholder="e.g. Unauthorised server access" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Incident Type</label>
                        <select name="incident_type" required>
                            <option value="">Select type</option>
                            <option>Phishing</option>
                            <option>Malware</option>
                            <option>Unauthorized Access</option>
                            <option>Data Breach</option>
                            <option>DDoS</option>
                            <option>Social Engineering</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-exclamation-circle"></i> Severity</label>
                        <select name="severity" required>
                            <option value="">Select severity</option>
                            <option>Low</option>
                            <option>Medium</option>
                            <option>High</option>
                            <option>Critical</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Reporter Name</label>
                        <input type="text" name="reporter_name" placeholder="Your full name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" placeholder="Describe the incident in detail..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Incident</button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>
