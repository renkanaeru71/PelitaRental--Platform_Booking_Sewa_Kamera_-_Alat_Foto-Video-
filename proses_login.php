<?php
session_start();
include "koneksi.php";

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
    $query = "SELECT * FROM user WHERE LOWER(Email) = '$email_esc' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $db_pass  = $user['Password'] ?? $user['password'] ?? '';
        $db_role  = $user['Role']     ?? $user['role']     ?? 'penyewa';
        $db_nama  = $user['Nama']     ?? $user['nama']     ?? '';
        $db_email = $user['Email']    ?? $user['email']    ?? '';
        $db_id    = $user['ID_user']  ?? $user['id_user']  ?? 0;
        $db_hp    = $user['no_hp']    ?? $user['No_HP']    ?? '';

        // Dukung password hash maupun plain-text
        $password_cocok = password_verify($password, $db_pass) || ($password === $db_pass);

        if ($password_cocok) {
            $_SESSION['id_user'] = $db_id;
            $_SESSION['user_id'] = $db_id;
            $_SESSION['nama']    = $db_nama;
            $_SESSION['email']   = $db_email;
            $_SESSION['no_hp']   = $db_hp;
            $_SESSION['role']    = $db_role;

            // Cookie ingat email
            if ($ingat) {
                setcookie('email_ingat', $db_email, time() + (30 * 86400), '/');
                setcookie('username',    $db_email, time() + (30 * 86400), '/');
            } else {
                setcookie('email_ingat', '', time() - 3600, '/');
                setcookie('username',    '', time() - 3600, '/');
            }

            if (strtolower($db_role) === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }

    $_SESSION['error'] = 'Email atau password salah.';
    $_SESSION['tab']   = 'login';
    header('Location: login.php');
    exit;
}

header('Location: login.php');
exit;
