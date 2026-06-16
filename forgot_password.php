<?php
session_start();
include 'db_config.php';
require 'vendor/phpmailer/PHPMailer.php';
require 'vendor/phpmailer/SMTP.php';
require 'vendor/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = null;
$error   = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid form submission. Please try again.";
    } else {
        // ===== IP Rate Limit (5 requests / 15 min) =====
        $ip = $_SERVER['REMOTE_ADDR'];
        $cutoff = date('Y-m-d H:i:s', time() - 900);
        $ipCheck = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM login_attempts WHERE ip_address = ? AND attempted_at > ?");
        mysqli_stmt_bind_param($ipCheck, "ss", $ip, $cutoff);
        mysqli_stmt_execute($ipCheck);
        $ipCount = mysqli_fetch_assoc(mysqli_stmt_get_result($ipCheck))['c'];
        mysqli_stmt_close($ipCheck);

        if ($ipCount >= 5) {
            $error = "Too many requests. Please try again later.";
        } else {
        $email_raw = $_POST['email'];
        $message = "If an account with that email exists, a reset link has been sent.";

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email_raw);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $user  = mysqli_fetch_assoc($result);
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $ustmt = mysqli_prepare($conn, "UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            mysqli_stmt_bind_param($ustmt, "ssi", $token, $expires, $user['id']);
            mysqli_stmt_execute($ustmt);
            mysqli_stmt_close($ustmt);

            $resetUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'your-email@gmail.com';
                $mail->Password   = 'your-app-password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('your-email@gmail.com', 'SIRS System');
                $mail->addAddress($email_raw, $user['full_name']);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset - SIRS System';
                $mail->Body    = "
                    <p>Hello <strong>{$user['full_name']}</strong>,</p>
                    <p>You requested a password reset for your SIRS account.</p>
                    <p><a href='$resetUrl' style='display:inline-block;padding:12px 24px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;'>Reset Password</a></p>
                    <p>Or copy this link:<br><code>$resetUrl</code></p>
                    <p>This link expires in <strong>1 hour</strong>.</p>
                    <p>If you did not request this, ignore this email.</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                // Silently fail — don't leak error details
            }
        }
        mysqli_stmt_close($stmt);
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="padding-top:0">
    <div class="auth-wrap">
        <div class="auth-card<?php echo $error ? ' shake' : ''; ?>">
            <div class="logo fade-up" style="animation-delay:0s;"><i class="fas fa-shield-alt"></i></div>
            <h1 class="fade-up" style="animation-delay:0.05s;">Forgot password</h1>
            <p class="sub fade-up" style="animation-delay:0.1s;">Enter your email to receive a reset link</p>

            <?php if ($error): ?>
                <div class="alert alert-red fade-up" style="animation-delay:0.1s;"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-green fade-up" style="animation-delay:0.1s;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post" id="authForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group fade-up" style="animation-delay:0.2s;">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email address" required autofocus>
                </div>
                <button type="submit" class="btn fade-up" style="animation-delay:0.25s;">Send Reset Link</button>
            </form>

            <p class="link fade-up" style="animation-delay:0.3s;"><a href="login.php"><i class="fas fa-arrow-left"></i> Back to sign in</a></p>
            <p class="link fade-up" style="animation-delay:0.3s;margin-top:4px;"><a href="landing.php"><i class="fas fa-home"></i> Back to home</a></p>
        </div>

        <div class="auth-credit">SIRS v1.0 <span>&mdash; Security Incident Reporting System</span></div>
    </div>

    <div class="page-transition" id="pageTransition"></div>
    <div id="toast-wrap"></div>
    <script src="script.js"></script>
</body>
</html>
