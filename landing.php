<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRS | Security Incident Reporting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>body { padding-top: 0 !important; }</style>
</head>
<body>

    <!-- Landing Nav -->
    <nav class="landing-nav" id="landingNav">
        <a href="landing.php" class="landing-brand">
            <span class="landing-brand-icon"><i class="fas fa-shield-alt"></i></span>
            <span class="landing-brand-text">SIRS</span>
        </a>
        <div class="landing-nav-links">
            <a href="#features">Features</a>
            <a href="#about">About</a>
            <a href="login.php">Sign In</a>
            <a href="register.php" class="landing-btn">Get Started</a>
        </div>
        <button class="landing-mobile-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
    </nav>

    <!-- Hero -->
    <section class="landing-hero" id="hero">
        <div class="landing-hero-bg"></div>
        <div class="landing-hero-content">
            <div class="landing-hero-icon"><i class="fas fa-shield-alt"></i></div>
            <h1>Security Incident<br>Reporting System</h1>
            <p>Report, track, and manage security incidents in one place.<br>Keeping your workplace safe and secure.</p>
            <div class="landing-hero-btns">
                <a href="register.php" class="landing-btn landing-btn-primary">Get Started</a>
                <a href="login.php" class="landing-btn landing-btn-outline">Sign In</a>
            </div>
        </div>
        <div class="landing-scroll">Scroll to explore <i class="fas fa-arrow-down"></i></div>
    </section>

    <!-- Features -->
    <section class="landing-section" id="features">
        <div class="landing-section-title">
            <h2>Why SIRS?</h2>
            <p>Everything you need to manage security incidents effectively</p>
        </div>
        <div class="landing-features">
            <div class="landing-feature">
                <div class="landing-feature-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <h3>Report Incidents</h3>
                <p>Submit security incidents with detailed descriptions, severity levels, and incident types in seconds.</p>
            </div>
            <div class="landing-feature">
                <div class="landing-feature-icon"><i class="fas fa-search"></i></div>
                <h3>Search &amp; Filter</h3>
                <p>Powerful search and filter capabilities to find incidents by title, reporter, status, or severity.</p>
            </div>
            <div class="landing-feature">
                <div class="landing-feature-icon"><i class="fas fa-chart-bar"></i></div>
                <h3>Dashboard Analytics</h3>
                <p>Real-time dashboard with incident statistics, status breakdowns, and quick access to all features.</p>
            </div>
            <div class="landing-feature">
                <div class="landing-feature-icon"><i class="fas fa-lock"></i></div>
                <h3>Secure &amp; Private</h3>
                <p>Your data is protected with industry-standard security measures and strict access controls.</p>
            </div>
            <div class="landing-feature">
                <div class="landing-feature-icon"><i class="fas fa-file-alt"></i></div>
                <h3>Incident Tracking</h3>
                <p>Track incidents from reporting through investigation to resolution with full status history.</p>
            </div>
            <div class="landing-feature">
                <div class="landing-feature-icon"><i class="fas fa-globe"></i></div>
                <h3>Multi-User Support</h3>
                <p>Create accounts, sign in securely, reset your password, and stay logged in across sessions.</p>
            </div>
        </div>
    </section>

    <!-- About -->
    <section class="landing-section landing-section-dark" id="about">
        <div class="landing-section-title">
            <h2>About the Project</h2>
            <p>Built with modern web technologies for CP 222 Open Source Technologies</p>
        </div>
        <div class="landing-about">
            <div class="landing-about-card">
                <h3><i class="fas fa-cogs"></i> Built With Care</h3>
                <ul>
                    <li>Fast and responsive interface</li>
                    <li>Reliable and robust performance</li>
                    <li>Modern, clean user experience</li>
                    <li>Cross-platform compatibility</li>
                </ul>
            </div>
            <div class="landing-about-card">
                <h3><i class="fas fa-shield-alt"></i> Your Safety Matters</h3>
                <ul>
                    <li>Strong password protection</li>
                    <li>Automatic session timeout</li>
                    <li>Account lockout after multiple attempts</li>
                    <li>Secure login with optional remember me</li>
                    <li>Data validation and sanitization</li>
                </ul>
            </div>
            <div class="landing-about-card">
                <h3><i class="fas fa-graduation-cap"></i> Course Details</h3>
                <ul>
                    <li>Course: CP 222 (formerly UCC 272)</li>
                    <li>Topic: Open Source Technologies</li>
                    <li>Group 06 — CSDFE Cluster</li>
                    <li>Security Incident Management</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="landing-cta">
        <h2>Ready to get started?</h2>
        <p>Create your account and start managing security incidents today.</p>
        <div class="landing-hero-btns">
            <a href="register.php" class="landing-btn landing-btn-primary">Create Account</a>
            <a href="login.php" class="landing-btn landing-btn-outline">Sign In</a>
        </div>
    </section>

    <!-- Footer -->
    <div class="landing-footer">
        <p>SIRS v1.0 <span>&mdash; Security Incident Reporting System</span></p>
    </div>

    <div class="page-transition" id="pageTransition"></div>
    <div id="toast-wrap"></div>
    <script src="script.js"></script>
</body>
</html>
