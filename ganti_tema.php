<?php
// tema hanya boleh terang atau gelap, selain itu dianggap terang
$tema = "light";
if (isset($_POST["tema"])) {
  if ($_POST["tema"] == "dark") {
    $tema = "dark";
  }
}

// cookie disimpan 30 hari supaya pilihan tema diingat browser
setcookie("tema", $tema, time() + 86400 * 30, "/");

$kembali = "index.php";
if (isset($_POST["kembali"])) {
  $kembali = $_POST["kembali"];
}

// kembali hanya boleh ke halaman di dalam aplikasi, bukan ke situs lain
if (strpos($kembali, "http") !== false || strpos($kembali, "//") !== false) {
  $kembali = "index.php";
}

header("Location: " . $kembali);
exit();
