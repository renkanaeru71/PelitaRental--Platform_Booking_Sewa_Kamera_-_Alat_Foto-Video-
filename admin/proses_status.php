<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

// file ini hanya boleh diakses lewat form POST, bukan lewat alamat GET
if ($_SERVER["REQUEST_METHOD"] != "POST") {
  $_SESSION["pesan"] = "Perubahan status hanya bisa lewat tombol di halaman admin.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: dashboard.php");
  exit();
}

$id_booking = 0;
if (isset($_POST["id_booking"])) {
  $id_booking = (int)$_POST["id_booking"];
}

$status_asal = "";
if (isset($_POST["status_asal"])) {
  $status_asal = trim($_POST["status_asal"]);
}

$status_baru = "";
if (isset($_POST["status_baru"])) {
  $status_baru = trim($_POST["status_baru"]);
}

// halaman tujuan setelah proses, disesuaikan dengan status barunya
$kembali = "operasional.php";
if ($status_baru == "dibayar") {
  $kembali = "verifikasi.php";
}

if ($id_booking < 1) {
  $_SESSION["pesan"] = "Booking tidak valid.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: " . $kembali);
  exit();
}

// hanya tiga perpindahan status ini yang diizinkan sesuai tabel status PRD
$diizinkan = false;
if ($status_asal == "menunggu_bayar" && $status_baru == "dibayar") {
  $diizinkan = true;
}
if ($status_asal == "dibayar" && $status_baru == "diambil") {
  $diizinkan = true;
}
if ($status_asal == "diambil" && $status_baru == "selesai") {
  $diizinkan = true;
}

if ($diizinkan == false) {
  $_SESSION["pesan"] = "Perubahan status tidak diizinkan.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: " . $kembali);
  exit();
}

$waktu_sekarang = date("Y-m-d H:i:s");

// syarat Status = status_asal pada WHERE mencegah status berubah dua kali dari halaman yang sudah usang
if ($status_baru == "dibayar") {
  $ubah = $koneksi->prepare("UPDATE booking SET Status = 'dibayar', Dibayar_at = ? WHERE ID_booking = ? AND Status = ?");
  $ubah->bind_param("sis", $waktu_sekarang, $id_booking, $status_asal);
  $pesan_berhasil = "Pembayaran booking berhasil diverifikasi.";
} else if ($status_baru == "diambil") {
  $ubah = $koneksi->prepare("UPDATE booking SET Status = 'diambil', Diambil_at = ? WHERE ID_booking = ? AND Status = ?");
  $ubah->bind_param("sis", $waktu_sekarang, $id_booking, $status_asal);
  $pesan_berhasil = "Pengambilan alat berhasil dicatat. Jangan lupa menyimpan kartu jaminan.";
} else {
  $ubah = $koneksi->prepare("UPDATE booking SET Status = 'selesai', Dikembalikan_at = ? WHERE ID_booking = ? AND Status = ?");
  $ubah->bind_param("sis", $waktu_sekarang, $id_booking, $status_asal);
  $pesan_berhasil = "Pengembalian alat berhasil dicatat dan unit sudah tersedia kembali.";
}

$ubah->execute();
$terpengaruh = $ubah->affected_rows;
$ubah->close();

if ($terpengaruh > 0) {
  $_SESSION["pesan"] = $pesan_berhasil;
  $_SESSION["pesan_tipe"] = "success";
} else {
  $_SESSION["pesan"] = "Status booking sudah berubah sebelumnya. Muat ulang halaman untuk melihat data terbaru.";
  $_SESSION["pesan_tipe"] = "warning";
}

header("Location: " . $kembali);
exit();
