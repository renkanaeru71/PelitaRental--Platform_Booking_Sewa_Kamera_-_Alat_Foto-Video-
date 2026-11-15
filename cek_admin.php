<?php
// dipakai halaman di folder admin, dicek login lalu dicek role
if (!isset($_SESSION["id_user"])) {
  $_SESSION["pesan"] = "Silakan login terlebih dahulu.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: ../login.php");
  exit();
}

if ($_SESSION["role"] != "admin") {
  $_SESSION["pesan"] = "Halaman ini hanya bisa dibuka oleh admin.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: ../index.php");
  exit();
}
