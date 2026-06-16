<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: landing.php");
    exit();
}

$total    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM incidents"))['c'];
$open     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM incidents WHERE status = 'Open'"))['c'];
$resolved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM incidents WHERE status = 'Resolved'"))['c'];
$critical = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM incidents WHERE severity = 'Critical'"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-content" id="pageContent">
        <h1 style="margin-bottom:20px;"><i class="fas fa-compass"></i> Welcome, <?php echo $_SESSION['full_name']; ?></h1>

        <div class="stats">
            <div class="stat">
                <div class="stat-label"><i class="fas fa-list"></i> Total Incidents</div>
                <div class="stat-value"><i class="fas fa-layer-group"></i><span class="stat-num" data-count="<?php echo $total; ?>"><?php echo $total; ?></span></div>
            </div>
            <div class="stat">
                <div class="stat-label"><i class="fas fa-folder-open"></i> Open</div>
                <div class="stat-value"><i class="fas fa-circle" style="color:var(--success);font-size:14px;"></i> <span class="stat-num" data-count="<?php echo $open; ?>"><?php echo $open; ?></span></div>
            </div>
            <div class="stat">
                <div class="stat-label"><i class="fas fa-check-circle"></i> Resolved</div>
                <div class="stat-value"><i class="fas fa-check" style="color:var(--warning);font-size:20px;"></i> <span class="stat-num" data-count="<?php echo $resolved; ?>"><?php echo $resolved; ?></span></div>
            </div>
            <div class="stat">
                <div class="stat-label"><i class="fas fa-exclamation-circle"></i> Critical</div>
                <div class="stat-value"><i class="fas fa-exclamation" style="color:var(--danger);font-size:22px;"></i> <span class="stat-num" data-count="<?php echo $critical; ?>"><?php echo $critical; ?></span></div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-cogs"></i> Quick Actions</h2>
            <div class="menu-grid">
                <a href="register_incident.php" class="menu-item">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <span class="title">Report Incident</span>
                    <span class="desc">Submit a new security incident</span>
                </a>
                <a href="display_incidents.php" class="menu-item">
                    <span class="icon"><i class="fas fa-file-alt"></i></span>
                    <span class="title">View Incidents</span>
                    <span class="desc">Browse all reported incidents</span>
                </a>
                <a href="search_incident.php" class="menu-item">
                    <span class="icon"><i class="fas fa-search"></i></span>
                    <span class="title">Search Incidents</span>
                    <span class="desc">Search by title or reporter</span>
                </a>
                <a href="filter_status.php" class="menu-item">
                    <span class="icon"><i class="fas fa-filter"></i></span>
                    <span class="title">Filter by Status</span>
                    <span class="desc">Filter incidents by status</span>
                </a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
