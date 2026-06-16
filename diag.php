<?php
$logPath = __DIR__ . '/login_debug.log';
$phpv = phpversion();
$now = date('Y-m-d H:i:s');
$logContent = file_exists($logPath) ? (file_get_contents($logPath) ?: '(empty)') : '(no log file yet)';
header('Content-Type: text/plain');
echo "SIRS Diagnostic\n";
echo "PHP: $phpv | Time: $now\n\n";
echo "=== Debug Log ===\n$logContent\n";
echo "\n=== Check login_debug.log after login attempt ===\n";
