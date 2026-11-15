<?php
session_start();
include "koneksi.php";

$nama = "";
if (isset($_POST["nama"])) {
  $nama = trim($_POST["nama"]);
}
$email = "";
if (isset($_POST["email"])) {
  $email = trim($_POST["email"]);
}
$no_hp = "";
if (isset($_POST["no_hp"])) {
  $no_hp = trim($_POST["no_hp"]);
}
$password = "";
if (isset($_POST["password"])) {
  $password = trim($_POST["password"]);
}

if ($nama == "" || $email == "" || $no_hp == "" || $password == "") {
  $_SESSION["pesan"] = "Semua kolom wajib diisi.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: register.php");
  exit();
}

if (strlen($password) < 6) {
  $_SESSION["pesan"] = "Password minimal 6 karakter.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: register.php");
  exit();
}

// cek dulu apakah email sudah dipakai
$cek = $koneksi->prepare("SELECT ID_user FROM user WHERE Email = ?");
$cek->bind_param("s", $email);
$cek->execute();
$cek->bind_result($id_cek);
if ($cek->fetch()) {
  $cek->close();
  $_SESSION["pesan"] = "Email sudah terdaftar, silakan pakai email lain.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: register.php");
  exit();
}
$cek->close();

// password diubah menjadi hash sebelum disimpan, role selalu penyewa
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$simpan = $koneksi->prepare("INSERT INTO user (Nama, Email, Password, no_hp, Role) VALUES (?, ?, ?, ?, 'penyewa')");
$simpan->bind_param("ssss", $nama, $email, $password_hash, $no_hp);

if ($simpan->execute()) {
  $_SESSION["pesan"] = "Pendaftaran berhasil, silakan login.";
  $_SESSION["pesan_tipe"] = "success";
  $simpan->close();
  header("Location: login.php");
  exit();
}

$simpan->close();
$_SESSION["pesan"] = "Pendaftaran gagal, silakan coba lagi.";
$_SESSION["pesan_tipe"] = "danger";
header("Location: register.php");
exit();
