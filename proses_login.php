<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $ingat    = isset($_POST['ingat']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email dan password wajib diisi!';
        $_SESSION['tab']   = 'login';
        header('Location: login.php');
        exit;
    }

    $email_esc = mysqli_real_escape_string($koneksi, $email);
    $query = "SELECT * FROM user WHERE LOWER(email) = '$email_esc' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $db_pass = $user['Password'] ?? $user['password'] ?? '';
        $db_role = $user['Role'] ?? $user['role'] ?? 'penyewa';
        $db_nama = $user['Nama'] ?? $user['nama'] ?? '';
        $db_email = $user['Email'] ?? $user['email'] ?? '';

        // Dukung password hash maupun plain-text (dari seeder Database.sql)
        $password_cocok = false;
        if (password_verify($password, $db_pass)) {
            $password_cocok = true;
        } elseif ($password === $db_pass) {
            $password_cocok = true;
        }

        if ($password_cocok) {
            // Set session
            $_SESSION['user_id'] = $user['ID_user'];
            $_SESSION['nama']    = $db_nama;
            $_SESSION['email']   = $db_email;
            $_SESSION['role']    = $db_role;

            // Cookie ingat username
            if ($ingat) {
                setcookie('username', $email, time() + (30 * 86400), '/');
            } else {
                setcookie('username', '', time() - 3600, '/');
            }

            // Redirect sesuai role
            if ($db_role === 'admin') {
                header('Location: dashboard-admin.php');
            } else {
                header('Location: dashboard-user.php');
            }
            exit;
        }
    }

    $_SESSION['error'] = 'Email atau password salah!';
    $_SESSION['tab']   = 'login';
    header('Location: login.php');
    exit;
}

header('Location: login.php');
exit;
