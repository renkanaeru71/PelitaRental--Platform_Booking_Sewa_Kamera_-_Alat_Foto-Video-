<?php
// error mysqli dikembalikan sebagai nilai false, bukan exception,
// supaya pola if ($stmt->execute()) seperti Modul 7 tetap bisa dipakai
mysqli_report(MYSQLI_REPORT_OFF);

// koneksi database MySQL gaya object oriented seperti Modul 7
$koneksi = new mysqli("localhost", "root", "", "pelitarental");

if ($koneksi->connect_error) {
  die("Koneksi gagal: " . $koneksi->connect_error);
}

// zona waktu disamakan dengan sistem lokal (WIB)
date_default_timezone_set("Asia/Jakarta");
