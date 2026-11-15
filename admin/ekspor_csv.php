<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

// file ini mengirim teks CSV langsung, jadi tidak memakai header.php dan footer.php
$hasil = $koneksi->query("SELECT b.Kode_booking, u.Nama, u.Email, b.Tanggal_Mulai, b.Tanggal_selesai, b.Durasi,
                                 b.Jenis_Jaminan, b.Total_bayar, b.Status, b.Created_at
                          FROM booking b
                          JOIN user u ON b.ID_user = u.ID_user
                          ORDER BY b.ID_booking DESC");

// nama file diberi tanggal supaya tiap unduhan berbeda
$nama_file = "laporan_pelitarental_" . date("Ymd") . ".csv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=" . $nama_file);

// baris pertama adalah judul kolom
echo "\"Kode Booking\",\"Nama Penyewa\",\"Email\",\"Tanggal Mulai\",\"Tanggal Selesai\",\"Durasi\",\"Jaminan\",\"Total Bayar\",\"Status\",\"Dibuat\"\n";

while ($b = $hasil->fetch_assoc()) {
  // teks dibungkus tanda kutip ganda supaya koma di dalam data tidak merusak kolom
  echo "\"" . $b["Kode_booking"] . "\",";
  echo "\"" . $b["Nama"] . "\",";
  echo "\"" . $b["Email"] . "\",";
  echo "\"" . date("d/m/Y", strtotime($b["Tanggal_Mulai"])) . "\",";
  echo "\"" . date("d/m/Y", strtotime($b["Tanggal_selesai"])) . "\",";
  echo "\"" . $b["Durasi"] . "\",";
  echo "\"" . $b["Jenis_Jaminan"] . "\",";
  echo "\"" . $b["Total_bayar"] . "\",";
  echo "\"" . $b["Status"] . "\",";
  echo "\"" . date("d/m/Y H:i", strtotime($b["Created_at"])) . "\"\n";
}

$koneksi->close();
exit();
