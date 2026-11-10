<?php
session_start();
require_once 'koneksi.php';
require_once 'auth.php';
cek_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM alat WHERE ID_alat = $id";
    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['flash_msg'] = 'Data alat berhasil dihapus!';
    } else {
        $_SESSION['flash_msg'] = 'Gagal menghapus alat karena terkait dengan transaksi booking.';
    }
}

header('Location: dashboard-admin.php');
exit;
