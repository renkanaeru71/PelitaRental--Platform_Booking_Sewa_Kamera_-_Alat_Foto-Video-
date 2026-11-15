<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$id_alat = 0;
if (isset($_POST["id_alat"])) {
  $id_alat = (int)$_POST["id_alat"];
}

if ($id_alat < 1) {
  $_SESSION["pesan"] = "Alat tidak valid.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: alat.php");
  exit();
}

// aturan PRD: alat yang pernah dibooking tidak bisa dihapus
$cek = $koneksi->prepare("SELECT COUNT(*) AS total FROM detail_booking WHERE ID_alat = ?");
$cek->bind_param("i", $id_alat);
$cek->execute();
$total_booking = 0;
$cek->bind_result($total_booking);
$cek->fetch();
$cek->close();

if ($total_booking > 0) {
  $_SESSION["pesan"] = "Alat tidak bisa dihapus karena pernah dibooking sebanyak " . $total_booking . " kali.";
  $_SESSION["pesan_tipe"] = "warning";
  header("Location: alat.php");
  exit();
}

$hapus = $koneksi->prepare("DELETE FROM alat WHERE ID_alat = ?");
$hapus->bind_param("i", $id_alat);

if ($hapus->execute()) {
  $_SESSION["pesan"] = "Alat berhasil dihapus.";
  $_SESSION["pesan_tipe"] = "success";
} else {
  $_SESSION["pesan"] = "Gagal menghapus alat.";
  $_SESSION["pesan_tipe"] = "danger";
}

$hapus->close();
header("Location: alat.php");
exit();
