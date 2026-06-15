<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = null;
if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $sql = "SELECT * FROM incidents WHERE status = '$status' ORDER BY date_reported DESC";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Filter Incidents</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Filter Incidents by Status</h1>
        <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | <a href="logout.php">Logout</a></p>
        <a href="index.php">Back to Menu</a>
        <form method="get">
            <label>Select Status:</label>
            <select name="status" required>
                <option value="">Select Status</option>
                <option value="Open">Open</option>
                <option value="Investigating">Investigating</option>
                <option value="Resolved">Resolved</option>
                <option value="Closed">Closed</option>
            </select>
            <button type="submit">Filter</button>
        </form>
        <?php if ($result !== null) { ?>
            <?php if (mysqli_num_rows($result) > 0) { ?>
            <table>
                <tr>
                    <th>Incident ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Severity</th>
                    <th>Description</th>
                    <th>Reporter</th>
                    <th>Date Reported</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['incident_id']; ?></td>
                    <td><?php echo $row['incident_title']; ?></td>
                    <td><?php echo $row['incident_type']; ?></td>
                    <td><?php echo $row['severity']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['reporter_name']; ?></td>
                    <td><?php echo $row['date_reported']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                </tr>
                <?php } ?>
            </table>
            <?php } else { echo "<p>No incidents found with status: $status</p>"; } ?>
        <?php } ?>
    </div>
    <div style="text-align: center; margin-top: 20px; color: #7f8c8d; font-size: 12px;">
        © 2026 CP 222 Open Source Technologies | Cyber Security & Digital Forensics Engineering
    </div>
</body>
</html>