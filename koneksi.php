<?php
// Koneksi database MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'pelitarental';

$koneksi = new mysqli('localhost', 'root', '', 'projek_pemweb');

if (!$koneksi || $koneksi->connect_error) {
    die("Koneksi database gagal: " . ($koneksi->connect_error ?? mysqli_connect_error()));
}

// Auto-sinkronisasi booking yang hangus (lewat batas 24 jam sesuai PRD)
function sinkron_booking_kadaluarsa($conn) {
    if (!$conn) return;
    $now = date('Y-m-d H:i:s');
    $q = @mysqli_query($conn, "SELECT ID_booking FROM booking WHERE Status = 'menunggu_bayar' AND Batas_Bayar IS NOT NULL AND Batas_Bayar < '$now'");
    if ($q && mysqli_num_rows($q) > 0) {
        while ($row = mysqli_fetch_assoc($q)) {
            $id_booking = (int)$row['ID_booking'];
            $q_details = @mysqli_query($conn, "SELECT ID_alat, jumlah FROM detail_booking WHERE ID_booking = $id_booking");
            if ($q_details) {
                while ($det = mysqli_fetch_assoc($q_details)) {
                    $id_alat = (int)$det['ID_alat'];
                    $qty     = (int)$det['jumlah'];
                    @mysqli_query($conn, "UPDATE alat SET stok_total = stok_total + $qty WHERE ID_alat = $id_alat");
                }
            }
            @mysqli_query($conn, "UPDATE booking SET Status = 'gagal' WHERE ID_booking = $id_booking");
        }
    }
}
sinkron_booking_kadaluarsa($koneksi);
