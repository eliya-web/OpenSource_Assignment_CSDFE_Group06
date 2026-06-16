<?php
$page = basename($_SERVER['PHP_SELF']);
$u = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
$initial = strtoupper(substr($u, 0, 1));
function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<nav class="navbar">
    <a href="index.php" class="navbar-brand">
        <span class="navbar-brand-icon"><i class="fas fa-shield-alt"></i></span>
        <span class="brand-text">SIRS</span>
    </a>

    <ul class="navbar-menu">
        <li><a href="index.php" <?php echo $page == 'index.php' ? 'class="active"' : ''; ?>><i class="fas fa-home"></i> <span class="label">Home</span></a></li>
        <li><a href="register_incident.php" <?php echo $page == 'register_incident.php' ? 'class="active"' : ''; ?>><i class="fas fa-exclamation-triangle"></i> <span class="label">Report</span></a></li>
        <li><a href="display_incidents.php" <?php echo $page == 'display_incidents.php' ? 'class="active"' : ''; ?>><i class="fas fa-file-alt"></i> <span class="label">Incidents</span></a></li>
        <li><a href="search_incident.php" <?php echo $page == 'search_incident.php' ? 'class="active"' : ''; ?>><i class="fas fa-search"></i> <span class="label">Search</span></a></li>
        <li><a href="filter_status.php" <?php echo $page == 'filter_status.php' ? 'class="active"' : ''; ?>><i class="fas fa-filter"></i> <span class="label">Filter</span></a></li>
    </ul>

    <div class="navbar-right">
        <button class="theme-btn" id="themeToggle" title="Toggle theme"><i class="fas fa-moon"></i></button>
        <div class="navbar-user">
            <span class="user-name"><?php echo $u; ?></span>
            <div class="avatar"><?php echo $initial; ?></div>
            <a href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
        </div>
    </div>
</nav>
