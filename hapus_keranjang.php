<?php
session_start();
include "koneksi.php";
include "cek_login.php";

$id_alat = 0;
if (isset($_POST["id_alat"])) {
  $id_alat = (int)$_POST["id_alat"];
}

// susun ulang keranjang tanpa alat yang dihapus
$keranjang_baru = array();
if (isset($_SESSION["keranjang"])) {
  for ($i = 0; $i < count($_SESSION["keranjang"]); $i++) {
    if ($_SESSION["keranjang"][$i]["id_alat"] != $id_alat) {
      $keranjang_baru[] = $_SESSION["keranjang"][$i];
    }
  }
}
$_SESSION["keranjang"] = $keranjang_baru;

$_SESSION["pesan"] = "Alat dihapus dari keranjang.";
$_SESSION["pesan_tipe"] = "success";
header("Location: keranjang.php");
exit();
