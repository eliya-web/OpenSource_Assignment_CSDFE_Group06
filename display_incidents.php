<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$cstmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM incidents");
mysqli_stmt_execute($cstmt);
$totalRows = mysqli_fetch_assoc(mysqli_stmt_get_result($cstmt))['c'];
mysqli_stmt_close($cstmt);
$totalPages = ceil($totalRows / $limit);

$stmt = mysqli_prepare($conn, "SELECT * FROM incidents ORDER BY id DESC LIMIT ?, ?");
mysqli_stmt_bind_param($stmt, "ii", $offset, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Incidents | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-content" id="pageContent">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-file-alt"></i> All Incidents</h1>
                <a href="register_incident.php" class="btn btn-primary btn-sm"><i class="fas fa-exclamation-triangle"></i> New Report</a>
            </div>

            <?php if (mysqli_num_rows($result) == 0): ?>
                <div class="empty">
                    <div class="icon"><i class="fas fa-file-alt"></i></div>
                    <h3>No incidents reported yet</h3>
                    <p>Be the first to report a security incident.</p>
                    <a href="register_incident.php" class="btn btn-primary btn-sm"><i class="fas fa-exclamation-triangle"></i> Report Incident</a>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Reporter</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th style="text-align:center;">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><code><?php echo h($row['incident_id']); ?></code></td>
                                    <td><?php echo h($row['incident_title']); ?></td>
                                    <td><?php echo h($row['incident_type']); ?></td>
                                    <td><span class="sev-<?php echo preg_replace('/[^a-z0-9-]/', '', strtolower($row['severity'])); ?>"><?php echo h($row['severity']); ?></span></td>
                                    <td><?php echo h($row['reporter_name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($row['date_reported'])); ?></td>
                                    <td><span class="st-<?php echo preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $row['status']))); ?>"><?php echo h($row['status']); ?></span></td>
                                    <td style="text-align:center;"><a href="view_incident.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="padding:4px 14px;font-size:12px;"><i class="fas fa-eye"></i> View</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page-1; ?>" class="pagination-btn"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page+1; ?>" class="pagination-btn"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
