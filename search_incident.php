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

$results = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $results = 'invalid';
    } else {
    $search_raw = $_POST['search'];
    $search_param = "%$search_raw%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM incidents WHERE incident_id LIKE ? OR incident_title LIKE ? OR reporter_name LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "sss", $search_param, $search_param, $search_param);
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
    <title>Search Incidents | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-content" id="pageContent">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-search"></i> Search Incidents</h1>
            </div>

            <form method="post" style="display:flex;gap:10px;">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group" style="margin:0;flex:1;">
                    <input type="text" name="search" placeholder="Search by title or reporter name..." required>
                </div>
                <button type="submit" class="btn btn-primary" style="height:44px;"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

        <?php if ($results === 'invalid'): ?>
            <div class="alert alert-red"><i class="fas fa-times-circle"></i> Invalid form submission.</div>
        <?php elseif ($results !== null): ?>
            <div class="card">
                <h2><i class="fas fa-list"></i> Search Results</h2>

                <?php if (mysqli_num_rows($results) == 0): ?>
                    <div class="empty">
                        <div class="icon"><i class="fas fa-search"></i></div>
                        <h3>No results found</h3>
                        <p>Try a different search term.</p>
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
                                    <td><span class="st-<?php echo preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $row['status']))); ?>"><?php echo h($row['status']); ?></span></td>
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
