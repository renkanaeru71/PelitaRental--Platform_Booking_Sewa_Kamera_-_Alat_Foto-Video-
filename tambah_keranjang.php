<?php
session_start();
include "koneksi.php";
include "cek_login.php";

$judul = "Tambah Keranjang";
$akar = "";

$id_alat = 0;
if (isset($_POST["id_alat"])) {
  $id_alat = (int)$_POST["id_alat"];
}

$jumlah = 1;
if (isset($_POST["jumlah"])) {
  $jumlah = (int)$_POST["jumlah"];
}

if ($id_alat < 1) {
  $_SESSION["pesan"] = "Alat tidak dikenali.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: index.php");
  exit();
}

if ($jumlah < 1) {
  $jumlah = 1;
}

// ambil data alat untuk memastikan ID benar dan jumlah tidak melebihi stok
$perintah = $koneksi->prepare("SELECT nama_alat, stok_total FROM alat WHERE ID_alat = ?");
$perintah->bind_param("i", $id_alat);
$perintah->execute();
// dikosongkan dulu supaya tidak terbaca nilai lama kalau baris tidak ditemukan
$nama_alat = "";
$stok_total = 0;
$perintah->bind_result($nama_alat, $stok_total);
$perintah->fetch();
$perintah->close();

if ($nama_alat == "") {
  $_SESSION["pesan"] = "Alat tidak ditemukan.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: index.php");
  exit();
}

if ($jumlah > $stok_total) {
  $jumlah = $stok_total;
}

// keranjang disimpan di session sebagai array berisi id_alat dan jumlah
if (!isset($_SESSION["keranjang"])) {
  $_SESSION["keranjang"] = array();
}

$sudah_ada = false;
for ($i = 0; $i < count($_SESSION["keranjang"]); $i++) {
  if ($_SESSION["keranjang"][$i]["id_alat"] == $id_alat) {
    $jumlah_baru = $_SESSION["keranjang"][$i]["jumlah"] + $jumlah;
    if ($jumlah_baru > $stok_total) {
      $jumlah_baru = $stok_total;
    }
    $_SESSION["keranjang"][$i]["jumlah"] = $jumlah_baru;
    $sudah_ada = true;
  }
}

// kalau belum ada di keranjang, tambahkan sebagai elemen baru di akhir array
if ($sudah_ada == false) {
  $_SESSION["keranjang"][] = array("id_alat" => $id_alat, "jumlah" => $jumlah);
}

$_SESSION["pesan"] = $nama_alat . " masuk ke keranjang.";
$_SESSION["pesan_tipe"] = "success";
header("Location: keranjang.php");
exit();
