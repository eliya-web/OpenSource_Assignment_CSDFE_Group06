<?php
session_start();
include 'db_config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid form submission.";
    } else {
        $full_name        = $_POST['full_name'];
        $username         = $_POST['username'];
        $email            = $_POST['email'];
        $password         = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $error = "Password must contain at least one uppercase letter.";
        } elseif (!preg_match('/[a-z]/', $password)) {
            $error = "Password must contain at least one lowercase letter.";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $error = "Password must contain at least one number.";
        } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $error = "Password must contain at least one special character.";
        } else {
            $cstmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? OR email = ?");
            mysqli_stmt_bind_param($cstmt, "ss", $username, $email);
            mysqli_stmt_execute($cstmt);
            $check = mysqli_stmt_get_result($cstmt);
            if (mysqli_num_rows($check) > 0) {
                mysqli_stmt_close($cstmt);
                $error = "Username or email already taken.";
            } else {
                mysqli_stmt_close($cstmt);
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $istmt = mysqli_prepare($conn, "INSERT INTO users (full_name, username, email, password) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($istmt, "ssss", $full_name, $username, $email, $hashed);
                if (mysqli_stmt_execute($istmt)) {
                    mysqli_stmt_close($istmt);
                    $_SESSION['success'] = "Account created! You can now sign in.";
                    header("Location: login.php");
                    exit();
                } else {
                    mysqli_stmt_close($istmt);
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="padding-top:0">
    <div class="auth-wrap">
        <div class="auth-card<?php echo $error ? ' shake' : ''; ?>">
            <div class="logo fade-up" style="animation-delay:0s;"><i class="fas fa-shield-alt"></i></div>
            <h1 class="fade-up" style="animation-delay:0.05s;">Create account</h1>
            <p class="sub fade-up" style="animation-delay:0.1s;">Join the Security Incident Reporting System</p>

            <?php if ($error): ?>
                <div class="alert alert-red fade-up" style="animation-delay:0.1s;"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" id="authForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group fade-up" style="animation-delay:0.15s;">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="e.g. Full name" required autofocus>
                </div>
                <div class="form-group fade-up" style="animation-delay:0.18s;">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form-group fade-up" style="animation-delay:0.21s;">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="form-group fade-up" style="animation-delay:0.24s;">
                    <label>Password</label>
                    <div class="pw-wrap">
                        <input type="password" name="password" id="regPassword" placeholder="Create a strong password" required>
                        <button type="button" class="pw-toggle" id="regPwToggle" tabindex="-1"><i class="far fa-eye"></i></button>
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
                <div class="form-group fade-up" style="animation-delay:0.27s;">
                    <label>Confirm Password</label>
                    <div class="pw-wrap">
                        <input type="password" name="confirm_password" id="regConfirmPw" placeholder="Repeat your password" required>
                        <button type="button" class="pw-toggle" id="regConfirmPwToggle" tabindex="-1"><i class="far fa-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn fade-up" style="animation-delay:0.3s;" id="submitBtn">Create Account</button>
            </form>

            <p class="link fade-up" style="animation-delay:0.35s;">Already have an account? <a href="login.php">Sign in</a></p>
            <p class="link fade-up" style="animation-delay:0.35s;margin-top:4px;"><a href="landing.php"><i class="fas fa-home"></i> Back to home</a></p>
        </div>

        <div class="auth-credit">SIRS v1.0 <span>&mdash; Security Incident Reporting System</span></div>
    </div>

    <div class="page-transition" id="pageTransition"></div>
    <div id="toast-wrap"></div>
    <script src="script.js"></script>
</body>
</html>
