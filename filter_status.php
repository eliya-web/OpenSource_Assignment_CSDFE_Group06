<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function cls($s) { return preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $s))); }

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$valid_statuses = ['Open', 'Investigating', 'Resolved', 'Closed'];
$selected = '';
$results  = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && in_array($_POST['status'], $valid_statuses)) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $results = 'invalid';
    } else {
    $selected = $_POST['status'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM incidents WHERE status = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "s", $selected);
    mysqli_stmt_execute($stmt);
    $results = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter by Status | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-content" id="pageContent">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-filter"></i> Filter Incidents by Status</h1>
            </div>

            <form method="post" style="display:flex;gap:10px;">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group" style="margin:0;flex:1;">
                    <select name="status" required>
                        <option value="">Select status</option>
                        <option value="Open" <?php echo $selected == 'Open' ? 'selected' : ''; ?>>Open</option>
                        <option value="Investigating" <?php echo $selected == 'Investigating' ? 'selected' : ''; ?>>Investigating</option>
                        <option value="Resolved" <?php echo $selected == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="Closed" <?php echo $selected == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="height:44px;"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>

        <?php if ($results === 'invalid'): ?>
            <div class="alert alert-red"><i class="fas fa-times-circle"></i> Invalid form submission.</div>
        <?php elseif ($results !== null): ?>
            <div class="card">
                <h2><i class="fas fa-list"></i> Status: <span class="st-<?php echo cls($selected); ?>" style="padding:4px 14px;border-radius:99px;font-size:13px;font-weight:600;"><?php echo h($selected); ?></span></h2>

                <?php if (mysqli_num_rows($results) == 0): ?>
                    <div class="empty">
                        <div class="icon"><i class="fas fa-file-alt"></i></div>
                        <h3>No incidents found</h3>
                        <p>No incidents with status "<?php echo h($selected); ?>" exist.</p>
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
                                <?php while ($row = mysqli_fetch_assoc($results)): ?>
                                    <tr>
                                        <td><code><?php echo h($row['incident_id']); ?></code></td>
                                        <td><?php echo h($row['incident_title']); ?></td>
                                        <td><?php echo h($row['incident_type']); ?></td>
                                        <td><span class="sev-<?php echo preg_replace('/[^a-z0-9-]/', '', strtolower($row['severity'])); ?>"><?php echo h($row['severity']); ?></span></td>
                                        <td><?php echo h($row['reporter_name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['date_reported'])); ?></td>
                                        <td><span class="st-<?php echo cls($row['status']); ?>"><?php echo h($row['status']); ?></span></td>
                                        <td style="text-align:center;"><a href="view_incident.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="padding:4px 14px;font-size:12px;"><i class="fas fa-eye"></i> View</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
