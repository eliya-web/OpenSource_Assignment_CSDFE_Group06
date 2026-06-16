<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = mysqli_query($conn, "SELECT * FROM incidents WHERE id = $id");
$incident = mysqli_fetch_assoc($result);

if (!$incident) {
    header("Location: display_incidents.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Details | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-content" id="pageContent">
        <div style="margin-bottom:16px;">
            <a href="display_incidents.php" style="color:var(--text-light);text-decoration:none;font-size:14px;"><i class="fas fa-arrow-left"></i> Back to Incidents</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-file-alt"></i> Incident Details</h1>
                <span class="st-<?php echo strtolower(str_replace(' ', '-', $incident['status'])); ?>" style="padding:4px 16px;border-radius:99px;font-size:13px;font-weight:600;"><?php echo $incident['status']; ?></span>
            </div>

            <div class="detail-grid">
                <div>
                    <p class="detail-label">Reference ID</p>
                    <p class="detail-value"><code><?php echo $incident['incident_id']; ?></code></p>
                </div>
                <div>
                    <p class="detail-label">Date Reported</p>
                    <p class="detail-value"><?php echo date('d M Y, h:i A', strtotime($incident['date_reported'])); ?></p>
                </div>
                <div>
                    <p class="detail-label">Incident Type</p>
                    <p class="detail-value"><?php echo $incident['incident_type']; ?></p>
                </div>
                <div>
                    <p class="detail-label">Severity</p>
                    <p class="detail-value"><span class="sev-<?php echo strtolower($incident['severity']); ?>" style="padding:2px 12px;border-radius:99px;"><?php echo $incident['severity']; ?></span></p>
                </div>
                <div>
                    <p class="detail-label">Reported By</p>
                    <p class="detail-value"><i class="fas fa-user" style="color:var(--text-light);margin-right:6px;"></i><?php echo $incident['reporter_name']; ?></p>
                </div>
                <div>
                    <p class="detail-label">Status</p>
                    <p class="detail-value"><?php echo $incident['status']; ?></p>
                </div>
            </div>

            <hr class="detail-divider">

            <div>
                <p class="detail-label">Incident Title</p>
                <p class="incident-title"><?php echo $incident['incident_title']; ?></p>
            </div>

            <div>
                <p class="detail-label">Description</p>
                <p class="incident-desc"><?php echo $incident['description']; ?></p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
