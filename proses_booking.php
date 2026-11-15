<?php
session_start();
include "koneksi.php";
include "cek_login.php";
include "kadaluarsa.php";


$tanggal_mulai = "";
if (isset($_POST["tanggal_mulai"])) {
  $tanggal_mulai = trim($_POST["tanggal_mulai"]);
}
$tanggal_selesai = "";
if (isset($_POST["tanggal_selesai"])) {
  $tanggal_selesai = trim($_POST["tanggal_selesai"]);
}
$jenis_jaminan = "";
if (isset($_POST["jenis_jaminan"])) {
  $jenis_jaminan = trim($_POST["jenis_jaminan"]);
}

// kembali ke keranjang dengan pesan kalau ada yang tidak valid
function kembali_keranjang($pesan) {
  $_SESSION["pesan"] = $pesan;
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: keranjang.php");
  exit();
}

if (!isset($_SESSION["keranjang"]) || count($_SESSION["keranjang"]) == 0) {
  $_SESSION["pesan"] = "Keranjang sewa masih kosong.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: index.php");
  exit();
}

$hari_ini = date("Y-m-d");

// validasi tanggal: mulai tidak sebelum hari ini, selesai tidak sebelum mulai
if ($tanggal_mulai == "" || $tanggal_selesai == "") {
  kembali_keranjang("Tanggal mulai dan tanggal selesai wajib diisi.");
}

$waktu_mulai = strtotime($tanggal_mulai);
$waktu_selesai = strtotime($tanggal_selesai);

if ($waktu_mulai == false || $waktu_selesai == false) {
  kembali_keranjang("Format tanggal tidak valid.");
}

if ($tanggal_mulai < $hari_ini) {
  kembali_keranjang("Tanggal mulai tidak boleh sebelum hari ini.");
}

if ($waktu_selesai < $waktu_mulai) {
  kembali_keranjang("Tanggal selesai harus sama atau setelah tanggal mulai.");
}

// jaminan hanya boleh KTM Asli, KTP Asli, atau SIM Asli
if ($jenis_jaminan != "KTM Asli" && $jenis_jaminan != "KTP Asli" && $jenis_jaminan != "SIM Asli") {
  kembali_keranjang("Jenis jaminan harus KTM Asli, KTP Asli, atau SIM Asli.");
}

$durasi = (int)(($waktu_selesai - $waktu_mulai) / 86400) + 1;

// tahap 1: cek stok semua alat dulu dan hitung total bayar
$total_bayar = 0;
$daftar_alat = array();
$ada_masalah = "";

for ($i = 0; $i < count($_SESSION["keranjang"]); $i++) {
  $id_alat = $_SESSION["keranjang"][$i]["id_alat"];
  $jumlah = (int)$_SESSION["keranjang"][$i]["jumlah"];

  if ($jumlah < 1) {
    $ada_masalah = "Jumlah unit setiap alat minimal 1.";
  }

  $perintah = $koneksi->prepare("SELECT nama_alat, harga_per_hari, stok_total FROM alat WHERE ID_alat = ?");
  $perintah->bind_param("i", $id_alat);
  $perintah->execute();
  // dikosongkan dulu supaya tidak terbaca nilai lama kalau baris tidak ditemukan
  $nama_alat = "";
  $harga_per_hari = 0;
  $stok_total = 0;
  $perintah->bind_result($nama_alat, $harga_per_hari, $stok_total);
  $perintah->fetch();
  $perintah->close();

  if ($nama_alat == "") {
    $ada_masalah = "Ada alat di keranjang yang sudah tidak tersedia.";
  }

  if ($jumlah > $stok_total) {
    $ada_masalah = "Jumlah " . $nama_alat . " melebihi stok total.";
  }

  // stok sisa = stok_total dikurangi unit pada booking aktif yang bentrok tanggal
  $cek_stok = $koneksi->prepare("SELECT a.stok_total - (
                                   SELECT COALESCE(SUM(d.Jumlah), 0)
                                   FROM detail_booking d
                                   JOIN booking b ON d.ID_booking = b.ID_booking
                                   WHERE d.ID_alat = a.ID_alat
                                     AND b.Status IN ('menunggu_bayar', 'dibayar', 'diambil')
                                     AND b.Tanggal_Mulai <= ?
                                     AND b.Tanggal_selesai >= ?
                                 ) AS sisa
                                 FROM alat a WHERE a.ID_alat = ?");
  $cek_stok->bind_param("ssi", $tanggal_selesai, $tanggal_mulai, $id_alat);
  $cek_stok->execute();
  $sisa = 0;
  $cek_stok->bind_result($sisa);
  $cek_stok->fetch();
  $cek_stok->close();

  // angka negatif tidak ditampilkan, dianggap stok habis
  if ($sisa < 0) {
    $sisa = 0;
  }

  if ($jumlah > $sisa) {
    $ada_masalah = "Stok " . $nama_alat . " tidak cukup pada tanggal tersebut, sisa " . $sisa . " unit.";
  }

  $subtotal = $harga_per_hari * $jumlah * $durasi;
  $total_bayar = $total_bayar + $subtotal;

  $daftar_alat[] = array(
    "id_alat" => $id_alat,
    "jumlah" => $jumlah,
    "harga_satuan" => $harga_per_hari,
    "subtotal" => $subtotal
  );
}

if ($ada_masalah != "") {
  kembali_keranjang($ada_masalah);
}

// tahap 2: simpan booking, kode dibuat unik dari tanggal, jam, ID user, dan angka acak
$id_user = (int)$_SESSION["id_user"];
$angka_acak = rand(10, 99);
$kode_booking = "PR-" . date("ymd") . "-" . date("His") . "-" . $id_user . "-" . $angka_acak;
$batas_bayar = date("Y-m-d H:i:s", strtotime("+24 hours"));

$simpan = $koneksi->prepare("INSERT INTO booking (Kode_booking, ID_user, Tanggal_Mulai, Tanggal_selesai, Durasi, Total_bayar, Jenis_Jaminan, Status, Batas_Bayar)
                             VALUES (?, ?, ?, ?, ?, ?, ?, 'menunggu_bayar', ?)");
$simpan->bind_param("sissidss", $kode_booking, $id_user, $tanggal_mulai, $tanggal_selesai, $durasi, $total_bayar, $jenis_jaminan, $batas_bayar);

if (!$simpan->execute()) {
  $simpan->close();
  kembali_keranjang("Booking gagal disimpan, silakan coba lagi.");
}

$id_booking = $koneksi->insert_id;
$simpan->close();

// tahap 3: simpan detail booking, kalau gagal hapus booking yang baru dibuat
$gagal_detail = false;
for ($i = 0; $i < count($daftar_alat); $i++) {
  $id_alat = $daftar_alat[$i]["id_alat"];
  $jumlah = $daftar_alat[$i]["jumlah"];
  $harga_satuan = $daftar_alat[$i]["harga_satuan"];
  $subtotal = $daftar_alat[$i]["subtotal"];

  $detail = $koneksi->prepare("INSERT INTO detail_booking (ID_booking, ID_alat, Harga_Satuan, Jumlah, Subtotal) VALUES (?, ?, ?, ?, ?)");
  $detail->bind_param("iidid", $id_booking, $id_alat, $harga_satuan, $jumlah, $subtotal);

  if (!$detail->execute()) {
    $gagal_detail = true;
  }
  $detail->close();
}

if ($gagal_detail) {
  $hapus = $koneksi->prepare("DELETE FROM booking WHERE ID_booking = ?");
  $hapus->bind_param("i", $id_booking);
  $hapus->execute();
  $hapus->close();
  kembali_keranjang("Detail booking gagal disimpan, booking dibatalkan.");
}

// keranjang dikosongkan setelah booking berhasil
$_SESSION["keranjang"] = array();

header("Location: bukti_booking.php?ID_booking=" . $id_booking);
exit();
