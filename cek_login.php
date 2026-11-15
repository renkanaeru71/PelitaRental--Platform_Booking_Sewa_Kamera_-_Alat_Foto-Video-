<?php
// dipakai halaman penyewa, menghentikan halaman kalau belum login
if (!isset($_SESSION["id_user"])) {
  $_SESSION["pesan"] = "Silakan login terlebih dahulu.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: login.php");
  exit();
}
