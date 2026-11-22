<?php
// pesan flash diambil dari session lalu dihapus supaya hanya tampil sekali
$pesan = "";
$pesan_tipe = "success";
if (isset($_SESSION["pesan"])) {
  $pesan = $_SESSION["pesan"];
  if (isset($_SESSION["pesan_tipe"])) {
    $pesan_tipe = $_SESSION["pesan_tipe"];
  }
  unset($_SESSION["pesan"]);
  unset($_SESSION["pesan_tipe"]);
}

// tema dibaca dari cookie yang disimpan ganti_tema.php selama 30 hari
$tema = "light";
if (isset($_COOKIE["tema"])) {
  if ($_COOKIE["tema"] == "dark") {
    $tema = "dark";
  }
}
if ($tema == "dark") {
  $tema_berikut = "light";
} else {
  $tema_berikut = "dark";
}

$halaman_ini = $_SERVER["REQUEST_URI"];
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="<?php echo $tema; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $judul; ?> - PelitaRental</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $akar; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo $akar; ?>index.php">PelitaRental</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menuUtama">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $akar; ?>index.php">Katalog</a>
        </li>
        <?php if (isset($_SESSION["id_user"])) { ?>
          <?php if ($_SESSION["role"] == "penyewa") { ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>keranjang.php">Keranjang</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>riwayat.php">Riwayat</a>
            </li>
          <?php } else { ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>admin/dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>admin/alat.php">Data Alat</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>admin/kategori.php">Kategori</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>admin/verifikasi.php">Verifikasi</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>admin/operasional.php">Operasional</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>admin/laporan.php">Laporan</a>
            </li>
          <?php } ?>
        <?php } ?>
      </ul>
      <ul class="navbar-nav align-items-lg-center">
        <?php if (isset($_SESSION["id_user"])) { ?>
          <?php if ($_SESSION["role"] == "penyewa") { ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $akar; ?>profil.php"><?php echo htmlspecialchars($_SESSION["nama"]); ?></a>
            </li>
          <?php } else { ?>
            <li class="nav-item">
              <span class="nav-link"><?php echo htmlspecialchars($_SESSION["nama"]); ?></span>
            </li>
          <?php } ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $akar; ?>logout.php">Logout</a>
          </li>
        <?php } else { ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $akar; ?>login.php">Masuk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $akar; ?>register.php">Daftar</a>
          </li>
        <?php } ?>
        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
          <form action="<?php echo $akar; ?>ganti_tema.php" method="post" class="m-0">
            <input type="hidden" name="tema" value="<?php echo $tema_berikut; ?>">
            <input type="hidden" name="kembali" value="<?php echo htmlspecialchars($halaman_ini); ?>">
            <button type="submit" class="btn btn-sm btn-outline-secondary">
              <?php if ($tema == "dark") { echo "Mode Terang"; } else { echo "Mode Gelap"; } ?>
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if ($pesan != "") { ?>
  <div class="alert alert-<?php echo $pesan_tipe; ?>"><?php echo htmlspecialchars($pesan); ?></div>
<?php } ?>
