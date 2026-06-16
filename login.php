<?php
session_start();
include 'db_config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ===== Remember Me Auto-Login =====
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token_raw = $_COOKIE['remember_token'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE remember_token = ?");
    mysqli_stmt_bind_param($stmt, "s", $token_raw);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];
        mysqli_stmt_close($stmt);
        header("Location: index.php");
        exit();
    }
    mysqli_stmt_close($stmt);
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path'    => '/',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid form submission.";
    } else {
        $username_raw = $_POST['username'];
        $password = $_POST['password'];

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username_raw);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // ===== Check Account Lockout =====
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
                $error = "Account is locked. Try again in $remaining minute(s).";
            } elseif (password_verify($password, $user['password'])) {
                // ===== Successful Login =====
                $ustmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                mysqli_stmt_bind_param($ustmt, "i", $user['id']);
                mysqli_stmt_execute($ustmt);
                mysqli_stmt_close($ustmt);

                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role']      = $user['role'];

                // ===== Remember Me =====
                if (isset($_POST['remember'])) {
                    $remember_token = bin2hex(random_bytes(32));
                    $rstmt = mysqli_prepare($conn, "UPDATE users SET remember_token = ? WHERE id = ?");
                    mysqli_stmt_bind_param($rstmt, "si", $remember_token, $user['id']);
                    mysqli_stmt_execute($rstmt);
                    mysqli_stmt_close($rstmt);
                    setcookie('remember_token', $remember_token, [
                        'expires'  => time() + 86400 * 30,
                        'path'     => '/',
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
                }

                header("Location: index.php");
                exit();
            } else {
                // ===== Failed Login =====
                $attempts = $user['failed_attempts'] + 1;
                if ($attempts >= 5) {
                    $lock_time = date('Y-m-d H:i:s', time() + 900);
                    $fstmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
                    mysqli_stmt_bind_param($fstmt, "isi", $attempts, $lock_time, $user['id']);
                    mysqli_stmt_execute($fstmt);
                    mysqli_stmt_close($fstmt);
                    $error = "Too many failed attempts. Account locked for 15 minutes.";
                } else {
                    $fstmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = ? WHERE id = ?");
                    mysqli_stmt_bind_param($fstmt, "ii", $attempts, $user['id']);
                    mysqli_stmt_execute($fstmt);
                    mysqli_stmt_close($fstmt);
                    $remaining = 5 - $attempts;
                    $error = "Invalid password. $remaining attempt(s) remaining before lockout.";
                }
            }
        } else {
            mysqli_stmt_close($stmt);
            $error = "Invalid credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="padding-top:0">
    <div class="auth-wrap">
        <div class="auth-card<?php echo $error ? ' shake' : ''; ?>">
            <div class="logo fade-up" style="animation-delay:0s;"><i class="fas fa-shield-alt"></i></div>
            <h1 class="fade-up" style="animation-delay:0.05s;">Welcome back</h1>
            <p class="sub fade-up" style="animation-delay:0.1s;">Sign in to your account</p>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-green fade-up" style="animation-delay:0.1s;"><i class="fas fa-check"></i> <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-red fade-up" style="animation-delay:0.1s;"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" id="authForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group fade-up" style="animation-delay:0.15s;">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter your username" required autofocus>
                </div>
                <div class="form-group fade-up" style="animation-delay:0.2s;">
                    <label>Password</label>
                    <div class="pw-wrap">
                        <input type="password" name="password" id="loginPassword" placeholder="Enter your password" required>
                        <button type="button" class="pw-toggle" id="loginPwToggle" tabindex="-1"><i class="far fa-eye"></i></button>
                    </div>
                </div>
                <div class="check-group fade-up" style="animation-delay:0.25s;">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn fade-up" style="animation-delay:0.3s;" id="submitBtn">Sign In</button>
                <div class="forgot-link fade-up" style="animation-delay:0.35s;"><a href="forgot_password.php">Forgot password?</a></div>
            </form>

            <p class="link fade-up" style="animation-delay:0.35s;">Don't have an account? <a href="register.php">Register here</a></p>
            <p class="link fade-up" style="animation-delay:0.35s;margin-top:4px;"><a href="landing.php"><i class="fas fa-home"></i> Back to home</a></p>
        </div>

        <div class="auth-credit">SIRS v1.0 <span>&mdash; Security Incident Reporting System</span></div>
    </div>

    <div class="page-transition" id="pageTransition"></div>
    <script>(function(){var e=document.getElementById('pageTransition');if(sessionStorage.getItem('sirs_ts')==='1'){sessionStorage.removeItem('sirs_ts');window.__sirs=1;e.style.transform='translateX(0)';e.style.transition='none'}else{e.style.transform='translateX(100%)';e.style.transition='none'}})()</script>
    <div id="toast-wrap"></div>
    <script src="script.js"></script>
</body>
</html>
