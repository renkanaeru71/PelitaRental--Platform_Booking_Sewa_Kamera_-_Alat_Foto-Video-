<?php
// Helper otentikasi & proteksi sesi role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cek_login() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Silakan login terlebih dahulu.';
        header('Location: login.php');
        exit;
    }
}

function cek_admin() {
    cek_login();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: dashboard-user.php');
        exit;
    }
}

function cek_penyewa() {
    cek_login();
    if ($_SESSION['role'] !== 'penyewa') {
        header('Location: dashboard-admin.php');
        exit;
    }
}
