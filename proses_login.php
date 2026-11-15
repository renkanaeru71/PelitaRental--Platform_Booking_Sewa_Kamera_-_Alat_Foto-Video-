<?php
session_start();
include "koneksi.php";

$email = "";
if (isset($_POST["email"])) {
  $email = trim($_POST["email"]);
}
$password = "";
if (isset($_POST["password"])) {
  $password = trim($_POST["password"]);
}

if ($email == "" || $password == "") {
  $_SESSION["pesan"] = "Email dan password wajib diisi.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: login.php");
  exit();
}

// query memakai placeholder supaya input tidak masuk ke teks SQL
$perintah = $koneksi->prepare("SELECT ID_user, Nama, Email, Password, no_hp, Role FROM user WHERE Email = ?");
$perintah->bind_param("s", $email);
$perintah->execute();
$perintah->bind_result($id_user, $nama, $email_db, $password_hash, $no_hp, $role);

if ($perintah->fetch()) {
  // password di database berupa hash, dicek dengan password_verify
  if (password_verify($password, $password_hash)) {
    $_SESSION["id_user"] = $id_user;
    $_SESSION["nama"] = $nama;
    $_SESSION["email"] = $email_db;
    $_SESSION["no_hp"] = $no_hp;
    $_SESSION["role"] = $role;

    // email disimpan di cookie 30 hari kalau centang "ingat email saya"
    if (isset($_POST["ingat"])) {
      setcookie("email_ingat", $email_db, time() + 86400 * 30, "/");
    } else {
      setcookie("email_ingat", "", time() - 3600, "/");
    }

    $perintah->close();
    if ($role == "admin") {
      header("Location: admin/dashboard.php");
    } else {
      header("Location: index.php");
    }
    exit();
  }
}

$perintah->close();
$_SESSION["pesan"] = "Email atau password salah.";
$_SESSION["pesan_tipe"] = "danger";
header("Location: login.php");
exit();
