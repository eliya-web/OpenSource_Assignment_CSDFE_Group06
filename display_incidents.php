<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM incidents ORDER BY date_reported DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Incidents</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>All Security Incidents</h1>
        <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | <a href="logout.php">Logout</a></p>
        <a href="index.php">Back to Menu</a>
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
        <?php } else { echo "<p>No incidents reported yet.</p>"; } ?>
    </div>
</body>
</html>