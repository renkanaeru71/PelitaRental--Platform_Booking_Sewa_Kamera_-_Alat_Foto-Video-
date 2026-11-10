<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $no_hp    = trim($_POST['no_hp'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($nama) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Semua kolom wajib diisi!';
        $_SESSION['tab']   = 'register';
        header('Location: login.php');
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password minimal 6 karakter!';
        $_SESSION['tab']   = 'register';
        header('Location: login.php');
        exit;
    }

    // Cek apakah email sudah terdaftar
    $email_esc = mysqli_real_escape_string($koneksi, $email);
    $cek = mysqli_query($koneksi, "SELECT ID_user FROM user WHERE email = '$email_esc' LIMIT 1");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['error'] = 'Email sudah terdaftar, silakan gunakan email lain!';
        $_SESSION['tab']   = 'register';
        header('Location: login.php');
        exit;
    }

    $nama_esc  = mysqli_real_escape_string($koneksi, $nama);
    $no_hp_esc = mysqli_real_escape_string($koneksi, $no_hp);
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (Nama, Email, Password, no_hp, Role) 
            VALUES ('$nama_esc', '$email_esc', '$pass_hash', '$no_hp_esc', 'penyewa')";

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['success'] = 'Pendaftaran berhasil! Silakan login.';
        $_SESSION['tab']     = 'login';
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Gagal mendaftar: ' . mysqli_error($koneksi);
        $_SESSION['tab']   = 'register';
        header('Location: login.php');
        exit;
    }
}

header('Location: login.php');
exit;
