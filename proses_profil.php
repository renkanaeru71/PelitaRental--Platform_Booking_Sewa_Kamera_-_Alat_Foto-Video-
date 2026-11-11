<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';
require_once 'auth.php';
cek_login();

$user_id = (int)($_SESSION['user_id'] ?? 0);
if (!$user_id) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Sesi login tidak valid.']);
    exit;
}

// Baca input (JSON atau POST biasa)
$is_json = false;
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (is_array($input) && !empty($input)) {
    $is_json  = true;
    $nama     = trim((string)($input['name'] ?? $input['nama'] ?? ''));
    $email    = strtolower(trim((string)($input['email'] ?? '')));
    $phone    = trim((string)($input['phone'] ?? $input['no_hp'] ?? ''));
    $password = trim((string)($input['password'] ?? ''));
} else {
    $nama     = trim((string)($_POST['name'] ?? $_POST['nama'] ?? ''));
    $email    = strtolower(trim((string)($_POST['email'] ?? '')));
    $phone    = trim((string)($_POST['phone'] ?? $_POST['no_hp'] ?? ''));
    $password = trim((string)($_POST['password'] ?? ''));
}

function respond(bool $is_json, bool $success, string $message, array $data = []): void {
    if ($is_json) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    } else {
        $_SESSION['flash_msg'] = $message;
        header('Location: ' . (($_SESSION['role'] ?? '') === 'admin' ? 'dashboard-admin.php' : 'dashboard-user.php'));
    }
    exit;
}

if (empty($nama) || empty($email)) {
    respond($is_json, false, 'Nama dan Email wajib diisi!');
}

if (mb_strlen($nama) < 2) {
    respond($is_json, false, 'Nama minimal 2 karakter!');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond($is_json, false, 'Format email tidak valid!');
}

// Cek apakah email sudah digunakan oleh user lain
$email_esc = mysqli_real_escape_string($koneksi, $email);
$cek = mysqli_query($koneksi, "SELECT ID_user FROM user WHERE LOWER(Email) = '$email_esc' AND ID_user != $user_id LIMIT 1");
if ($cek && mysqli_num_rows($cek) > 0) {
    respond($is_json, false, 'Email sudah digunakan oleh akun lain!');
}

$nama_esc  = mysqli_real_escape_string($koneksi, $nama);
$phone_esc = mysqli_real_escape_string($koneksi, $phone);

// Update data (dengan atau tanpa password)
if (!empty($password)) {
    if (strlen($password) < 6) {
        respond($is_json, false, 'Password baru minimal 6 karakter!');
    }
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE user SET Nama = '$nama_esc', Email = '$email_esc', no_hp = '$phone_esc', Password = '$pass_hash' WHERE ID_user = $user_id";
} else {
    $sql = "UPDATE user SET Nama = '$nama_esc', Email = '$email_esc', no_hp = '$phone_esc' WHERE ID_user = $user_id";
}

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['nama']  = $nama;
    $_SESSION['email'] = $email;
    $_SESSION['no_hp'] = $phone;

    // Hitung inisial
    $parts = explode(' ', trim($nama));
    $initials = '';
    if (count($parts) >= 2) {
        $initials = mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);
    } else {
        $initials = mb_substr($nama, 0, 2);
    }
    $initials = strtoupper($initials);

    respond($is_json, true, 'Profil berhasil diperbarui!', [
        'nama'     => $nama,
        'email'    => $email,
        'no_hp'    => $phone,
        'initials' => $initials
    ]);
} else {
    respond($is_json, false, 'Gagal memperbarui profil: ' . mysqli_error($koneksi));
}
