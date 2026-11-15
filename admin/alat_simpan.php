<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$id_alat = 0;
if (isset($_POST["id_alat"])) {
  $id_alat = (int)$_POST["id_alat"];
}

$id_kategori = 0;
if (isset($_POST["id_kategori"])) {
  $id_kategori = (int)$_POST["id_kategori"];
}

$nama_alat = "";
if (isset($_POST["nama_alat"])) {
  $nama_alat = trim($_POST["nama_alat"]);
}

$deskripsi = "";
if (isset($_POST["deskripsi"])) {
  $deskripsi = trim($_POST["deskripsi"]);
}

$harga_per_hari = 0;
if (isset($_POST["harga_per_hari"])) {
  $harga_per_hari = (int)$_POST["harga_per_hari"];
}

$stok_total = 0;
if (isset($_POST["stok_total"])) {
  $stok_total = (int)$_POST["stok_total"];
}

// alamat kembali ke form, dipakai kalau ada isian yang tidak valid
$kembali = "alat_form.php";
if ($id_alat > 0) {
  $kembali = "alat_form.php?ID_alat=" . $id_alat;
}

if ($nama_alat == "") {
  $_SESSION["pesan"] = "Nama alat wajib diisi.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: " . $kembali);
  exit();
}

if ($harga_per_hari < 1) {
  $_SESSION["pesan"] = "Harga per hari minimal Rp 1.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: " . $kembali);
  exit();
}

if ($stok_total < 0) {
  $_SESSION["pesan"] = "Stok tidak boleh negatif.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: " . $kembali);
  exit();
}

// kategori yang dipilih harus benar-benar ada di tabel kategori
$cek = $koneksi->prepare("SELECT COUNT(*) AS jumlah FROM kategori WHERE ID_kategori = ?");
$cek->bind_param("i", $id_kategori);
$cek->execute();
$jumlah_kategori = 0;
$cek->bind_result($jumlah_kategori);
$cek->fetch();
$cek->close();

if ($jumlah_kategori == 0) {
  $_SESSION["pesan"] = "Kategori yang dipilih tidak ditemukan.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: " . $kembali);
  exit();
}

if ($id_alat > 0) {
  $simpan = $koneksi->prepare("UPDATE alat SET ID_kategori = ?, nama_alat = ?, deskripsi = ?, harga_per_hari = ?, stok_total = ?
                               WHERE ID_alat = ?");
  $simpan->bind_param("issdii", $id_kategori, $nama_alat, $deskripsi, $harga_per_hari, $stok_total, $id_alat);
  $pesan_berhasil = "Data alat berhasil diperbarui.";
} else {
  $simpan = $koneksi->prepare("INSERT INTO alat (ID_kategori, nama_alat, deskripsi, harga_per_hari, stok_total)
                               VALUES (?, ?, ?, ?, ?)");
  $simpan->bind_param("issdi", $id_kategori, $nama_alat, $deskripsi, $harga_per_hari, $stok_total);
  $pesan_berhasil = "Alat baru berhasil ditambahkan.";
}

if ($simpan->execute()) {
  $_SESSION["pesan"] = $pesan_berhasil;
  $_SESSION["pesan_tipe"] = "success";
} else {
  $_SESSION["pesan"] = "Gagal menyimpan data alat.";
  $_SESSION["pesan_tipe"] = "danger";
}

$simpan->close();
header("Location: alat.php");
exit();
