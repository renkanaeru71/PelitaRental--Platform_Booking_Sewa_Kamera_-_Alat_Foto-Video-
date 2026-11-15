<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$id_kategori = 0;
if (isset($_POST["id_kategori"])) {
  $id_kategori = (int)$_POST["id_kategori"];
}

$nama_kategori = "";
if (isset($_POST["nama_kategori"])) {
  $nama_kategori = trim($_POST["nama_kategori"]);
}

if ($nama_kategori == "") {
  $_SESSION["pesan"] = "Nama kategori wajib diisi.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: kategori_form.php" . ($id_kategori > 0 ? "?ID_kategori=" . $id_kategori : ""));
  exit();
}

// periksa duplikasi nama kategori
if ($id_kategori > 0) {
  $cek = $koneksi->prepare("SELECT ID_kategori FROM kategori WHERE nama_kategori = ? AND ID_kategori != ?");
  $cek->bind_param("si", $nama_kategori, $id_kategori);
} else {
  $cek = $koneksi->prepare("SELECT ID_kategori FROM kategori WHERE nama_kategori = ?");
  $cek->bind_param("s", $nama_kategori);
}
$cek->execute();
$cek->bind_result($id_cek);
$id_cek = 0;
$cek->fetch();
$cek->close();

if ($id_cek != 0) {
  $_SESSION["pesan"] = "Nama kategori sudah ada.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: kategori_form.php" . ($id_kategori > 0 ? "?ID_kategori=" . $id_kategori : ""));
  exit();
}

if ($id_kategori > 0) {
  $simpan = $koneksi->prepare("UPDATE kategori SET nama_kategori = ? WHERE ID_kategori = ?");
  $simpan->bind_param("si", $nama_kategori, $id_kategori);
  $pesan = "Kategori berhasil diperbarui.";
} else {
  $simpan = $koneksi->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
  $simpan->bind_param("s", $nama_kategori);
  $pesan = "Kategori baru berhasil ditambahkan.";
}

if ($simpan->execute()) {
  $_SESSION["pesan"] = $pesan;
  $_SESSION["pesan_tipe"] = "success";
} else {
  $_SESSION["pesan"] = "Gagal menyimpan kategori.";
  $_SESSION["pesan_tipe"] = "danger";
}

$simpan->close();
header("Location: kategori.php");
exit();
