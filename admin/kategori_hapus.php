<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$id_kategori = 0;
if (isset($_POST["id_kategori"])) {
  $id_kategori = (int)$_POST["id_kategori"];
}

if ($id_kategori < 1) {
  $_SESSION["pesan"] = "Kategori tidak valid.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: kategori.php");
  exit();
}

// aturan PRD: kategori yang masih dipakai alat tidak bisa dihapus
$cek = $koneksi->prepare("SELECT COUNT(*) AS total FROM alat WHERE ID_kategori = ?");
$cek->bind_param("i", $id_kategori);
$cek->execute();
$total_alat = 0;
$cek->bind_result($total_alat);
$cek->fetch();
$cek->close();

if ($total_alat > 0) {
  $_SESSION["pesan"] = "Kategori tidak bisa dihapus karena masih digunakan oleh " . $total_alat . " alat.";
  $_SESSION["pesan_tipe"] = "warning";
  header("Location: kategori.php");
  exit();
}

$hapus = $koneksi->prepare("DELETE FROM kategori WHERE ID_kategori = ?");
$hapus->bind_param("i", $id_kategori);

if ($hapus->execute()) {
  $_SESSION["pesan"] = "Kategori berhasil dihapus.";
  $_SESSION["pesan_tipe"] = "success";
} else {
  $_SESSION["pesan"] = "Gagal menghapus kategori.";
  $_SESSION["pesan_tipe"] = "danger";
}

$hapus->close();
header("Location: kategori.php");
exit();
