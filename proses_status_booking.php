<?php
session_start();
require_once 'koneksi.php';
require_once 'auth.php';
cek_admin();

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$id_booking = (int)($input['id_booking'] ?? 0);

if (!$id_booking || empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap.']);
    exit;
}

$now = date('Y-m-d H:i:s');

if ($action === 'verifikasi_bayar') {
    // Verifikasi pembayaran di toko
    $sql = "UPDATE booking SET Status = 'dibayar', Dibayar_at = '$now' WHERE ID_booking = $id_booking AND Status = 'menunggu_bayar'";
    if (mysqli_query($koneksi, $sql) && mysqli_affected_rows($koneksi) > 0) {
        echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil diverifikasi.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal verifikasi atau status booking bukan menunggu bayar.']);
    }
    exit;
}

if ($action === 'serah_terima') {
    // Konfirmasi pengambilan alat dan jaminan fisik diterima
    $sql = "UPDATE booking SET Status = 'diambil', Diambil_at = '$now' WHERE ID_booking = $id_booking AND Status = 'dibayar'";
    if (mysqli_query($koneksi, $sql) && mysqli_affected_rows($koneksi) > 0) {
        echo json_encode(['success' => true, 'message' => 'Alat diserahkan & jaminan fisik disimpan di toko.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengubah status. Pastikan booking sudah berstatus dibayar.']);
    }
    exit;
}

if ($action === 'pengembalian') {
    // Pengembalian alat: kembalikan stok alat otomatis & kembalikan jaminan
    mysqli_begin_transaction($koneksi);
    try {
        $sql = "UPDATE booking SET Status = 'selesai', Dikembalikan_at = '$now' WHERE ID_booking = $id_booking AND Status = 'diambil'";
        if (!mysqli_query($koneksi, $sql) || mysqli_affected_rows($koneksi) === 0) {
            throw new Exception("Status booking tidak valid untuk pengembalian.");
        }

        // Ambil barang-barang dalam booking untuk menambah stok kembali
        $q_details = mysqli_query($koneksi, "SELECT ID_alat, jumlah FROM detail_booking WHERE ID_booking = $id_booking");
        while ($det = mysqli_fetch_assoc($q_details)) {
            $id_alat = (int)$det['ID_alat'];
            $qty = (int)$det['jumlah'];
            mysqli_query($koneksi, "UPDATE alat SET stok_total = stok_total + $qty WHERE ID_alat = $id_alat");
        }

        mysqli_commit($koneksi);
        echo json_encode(['success' => true, 'message' => 'Alat diterima, stok otomatis bertambah, dan jaminan diserahkan kembali.']);
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenali.']);
