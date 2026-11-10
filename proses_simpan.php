<?php
session_start();
require_once 'koneksi.php';
require_once 'auth.php';
cek_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_alat        = isset($_POST['id_alat']) && $_POST['id_alat'] !== '' ? (int)$_POST['id_alat'] : null;
    $id_kategori    = (int)($_POST['id_kategori'] ?? 0);
    $nama_alat      = trim($_POST['nama_alat'] ?? '');
    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $harga_per_hari = (float)($_POST['harga_per_hari'] ?? 0);
    $stok_total     = (int)($_POST['stok_total'] ?? 0);

    if (empty($nama_alat) || $id_kategori <= 0 || $harga_per_hari <= 0) {
        $_SESSION['flash_msg'] = 'Nama alat, kategori, dan harga wajib diisi!';
        header('Location: dashboard-admin.php');
        exit;
    }

    $nama_esc = mysqli_real_escape_string($koneksi, $nama_alat);
    $desk_esc = mysqli_real_escape_string($koneksi, $deskripsi);

    if ($id_alat) {
        // Update alat
        $sql = "UPDATE alat SET 
                ID_kategori = $id_kategori,
                nama_alat = '$nama_esc',
                deskripsi = '$desk_esc',
                harga_per_hari = $harga_per_hari,
                stok_total = $stok_total
                WHERE ID_alat = $id_alat";
        mysqli_query($koneksi, $sql);
        $_SESSION['flash_msg'] = 'Data alat berhasil diperbarui!';
    } else {
        // Insert alat baru
        $sql = "INSERT INTO alat (ID_kategori, nama_alat, deskripsi, harga_per_hari, stok_total)
                VALUES ($id_kategori, '$nama_esc', '$desk_esc', $harga_per_hari, $stok_total)";
        mysqli_query($koneksi, $sql);
        $_SESSION['flash_msg'] = 'Alat baru berhasil ditambahkan!';
    }
}

header('Location: dashboard-admin.php');
exit;
