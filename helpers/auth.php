<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_auth() {
    if (!isset($_SESSION['user'])) {
        header('Location: /inventory-app/public/login.php');
        exit;
    }
}

function check_role($roles = []) {
    check_auth();
    if (!in_array($_SESSION['user']['role'], $roles)) {
        http_response_code(403);
        echo "<h2 style='color:red; text-align:center;'>403 Forbidden: Anda tidak memiliki akses ke halaman ini.</h2>";
        exit;
    }
}