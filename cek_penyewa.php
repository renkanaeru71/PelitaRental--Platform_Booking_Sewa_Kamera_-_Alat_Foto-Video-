<?php
// dipakai halaman penyewa: dicek login dulu, lalu dicek rolenya harus penyewa.
// admin yang membuka halaman penyewa akan dikembalikan ke dashboard admin.
include "cek_login.php";

if ($_SESSION["role"] != "penyewa") {
  $_SESSION["pesan"] = "Halaman ini hanya bisa dibuka oleh penyewa.";
  $_SESSION["pesan_tipe"] = "danger";
  header("Location: admin/dashboard.php");
  exit();
}
