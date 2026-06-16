<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error    = null;
$success  = null;
$valid    = false;
$email    = '';
$token    = isset($_GET['token']) ? $_GET['token'] : (isset($_POST['token']) ? $_POST['token'] : '');

if ($token) {
    $token = mysqli_real_escape_string($conn, $token);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '$token' AND reset_expires > NOW()");
    if (mysqli_num_rows($result) === 1) {
        $user  = mysqli_fetch_assoc($result);
        $email = $user['email'];
        $valid = true;
    } else {
        $error = "This reset link is invalid or has expired.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $valid) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid form submission.";
    } else {
        $token = mysqli_real_escape_string($conn, $_POST['token']);

        $result = mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '$token' AND reset_expires > NOW()");
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $password         = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password !== $confirm_password) {
                $error = "Passwords do not match.";
            } elseif (strlen($password) < 6) {
                $error = "Password must be at least 6 characters.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET password = '$hashed', reset_token = NULL, reset_expires = NULL WHERE id = {$user['id']}");
                $_SESSION['success'] = "Password reset successful! You can now sign in.";
                header("Location: login.php");
                exit();
            }
        } else {
            $error = "This reset link is invalid or has expired.";
            $valid = false;
        }
    }
}

if ($valid) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="padding-top:0">
    <div class="auth-wrap">
        <div class="auth-card<?php echo $error ? ' shake' : ''; ?>">
            <div class="logo fade-up" style="animation-delay:0s;"><i class="fas fa-shield-alt"></i></div>

            <?php if ($valid): ?>
                <h1 class="fade-up" style="animation-delay:0.05s;">Reset password</h1>
                <p class="sub fade-up" style="animation-delay:0.1s;">Choose a new password for <strong><?php echo $email; ?></strong></p>

                <?php if ($error): ?>
                    <div class="alert alert-red fade-up" style="animation-delay:0.1s;"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="post" id="authForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <div class="form-group fade-up" style="animation-delay:0.15s;">
                        <label>New Password</label>
                        <div class="pw-wrap">
                            <input type="password" name="password" id="resetPassword" placeholder="Enter new password" required autofocus>
                            <button type="button" class="pw-toggle" id="resetPwToggle" tabindex="-1"><i class="far fa-eye"></i></button>
                        </div>
                        <div class="pw-strength"><div class="pw-strength-bar" id="pwStrengthBar"></div></div>
                        <div class="pw-hint" id="pwHint">Use 6+ characters with a mix of letters & numbers</div>
                        <ul class="pw-rules" id="pwRules">
                            <li data-rule="length">At least 6 characters</li>
                            <li data-rule="lower">One lowercase letter</li>
                            <li data-rule="upper">One uppercase letter</li>
                            <li data-rule="number">One number</li>
                            <li data-rule="special">One special character (!@#$%^&*)</li>
                        </ul>
                    </div>
                    <div class="form-group fade-up" style="animation-delay:0.2s;">
                        <label>Confirm Password</label>
                        <div class="pw-wrap">
                            <input type="password" name="confirm_password" id="resetConfirmPw" placeholder="Repeat new password" required>
                            <button type="button" class="pw-toggle" id="resetConfirmPwToggle" tabindex="-1"><i class="far fa-eye"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn fade-up" style="animation-delay:0.25s;" id="submitBtn">Reset Password</button>
                </form>

                <p class="link fade-up" style="animation-delay:0.3s;"><a href="login.php"><i class="fas fa-arrow-left"></i> Back to sign in</a></p>
                <p class="link fade-up" style="animation-delay:0.3s;margin-top:4px;"><a href="landing.php"><i class="fas fa-home"></i> Back to home</a></p>

            <?php else: ?>
                <h1 class="fade-up" style="animation-delay:0.05s;">Invalid link</h1>
                <p class="sub fade-up" style="animation-delay:0.1s;"><?php echo $error ?: 'This reset link is invalid.'; ?></p>
                <p class="link fade-up" style="animation-delay:0.15s;"><a href="forgot_password.php">Request a new reset link</a></p>
                <p class="link fade-up" style="animation-delay:0.15s;margin-top:4px;"><a href="landing.php"><i class="fas fa-home"></i> Back to home</a></p>
            <?php endif; ?>
        </div>

        <div class="auth-credit">SIRS v1.0 <span>&mdash; Security Incident Reporting System</span></div>
    </div>

    <div class="page-transition" id="pageTransition"></div>
    <script>(function(){var e=document.getElementById('pageTransition');if(sessionStorage.getItem('sirs_ts')==='1'){sessionStorage.removeItem('sirs_ts');window.__sirs=1;e.style.transform='translateX(0)';e.style.transition='none'}else{e.style.transform='translateX(100%)';e.style.transition='none'}})()</script>
    <div id="toast-wrap"></div>
    <script src="script.js"></script>
</body>
</html>
