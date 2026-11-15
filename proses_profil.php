<?php
session_start();
include "koneksi.php";
include "cek_login.php";

$id_user = (int)$_SESSION["id_user"];

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
$password_baru = "";
if (isset($_POST["password_baru"])) {
  $password_baru = trim($_POST["password_baru"]);
}

if ($nama == "" || $email == "" || $no_hp == "") {
  $_SESSION["pesan"] = "Nama, email, dan nomor HP wajib diisi.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: profil.php");
  exit();
}

// email tidak boleh dipakai akun lain
$cek = $koneksi->prepare("SELECT ID_user FROM user WHERE Email = ? AND ID_user != ?");
$cek->bind_param("si", $email, $id_user);
$cek->execute();
$cek->bind_result($id_cek);
$id_cek = 0;
$cek->fetch();
$cek->close();

if ($id_cek != 0) {
  $_SESSION["pesan"] = "Email sudah dipakai akun lain.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: profil.php");
  exit();
}

if ($password_baru != "") {
  if (strlen($password_baru) < 6) {
    $_SESSION["pesan"] = "Password baru minimal 6 karakter.";
    $_SESSION["pesan_tipe"] = "danger";
    header("Location: profil.php");
    exit();
  }
  $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
  $ubah = $koneksi->prepare("UPDATE user SET Nama = ?, Email = ?, no_hp = ?, Password = ? WHERE ID_user = ?");
  $ubah->bind_param("ssssi", $nama, $email, $no_hp, $password_hash, $id_user);
} else {
  $ubah = $koneksi->prepare("UPDATE user SET Nama = ?, Email = ?, no_hp = ? WHERE ID_user = ?");
  $ubah->bind_param("sssi", $nama, $email, $no_hp, $id_user);
}

if ($ubah->execute()) {
  // nama di session ikut diperbarui supaya navbar langsung berubah
  $_SESSION["nama"] = $nama;
  $_SESSION["email"] = $email;
  $_SESSION["no_hp"] = $no_hp;
  $ubah->close();
  $_SESSION["pesan"] = "Profil berhasil diperbarui.";
  $_SESSION["pesan_tipe"] = "success";
  header("Location: profil.php");
  exit();
}

$ubah->close();
$_SESSION["pesan"] = "Profil gagal diperbarui, silakan coba lagi.";
$_SESSION["pesan_tipe"] = "danger";
header("Location: profil.php");
exit();
