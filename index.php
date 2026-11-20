<?php
session_start();
include "koneksi.php";

$judul = "Katalog Alat";
$akar = "";

// kata kunci dan kategori diambil dari form pencarian (method get)
$cari = "";
if (isset($_GET["cari"])) {
  $cari = trim($_GET["cari"]);
}
$pilih_kategori = 0;
if (isset($_GET["kategori"])) {
  $pilih_kategori = (int)$_GET["kategori"];
}

include "header.php";
?>
<h1 class="h3 mb-3">Katalog Alat</h1>

<form action="index.php" method="get" class="row g-2 mb-4">
  <div class="col-md-5">
    <input type="text" class="form-control" name="cari" placeholder="Cari nama alat" value="<?php echo htmlspecialchars($cari); ?>">
  </div>
  <div class="col-md-4">
    <select class="form-select" name="kategori">
      <option value="0">Semua kategori</option>
      <?php
      // daftar kategori untuk pilihan filter, tanpa input pengguna
      $hasil_kategori = $koneksi->query("SELECT ID_kategori, nama_kategori FROM kategori ORDER BY nama_kategori");
      while ($kategori = $hasil_kategori->fetch_assoc()) {
        $terpilih = "";
        if ($pilih_kategori == $kategori["ID_kategori"]) {
          $terpilih = "selected";
        }
        ?>
        <option value="<?php echo $kategori["ID_kategori"]; ?>" <?php echo $terpilih; ?>>
          <?php echo htmlspecialchars($kategori["nama_kategori"]); ?>
        </option>
        <?php
      }
      ?>
    </select>
  </div>
  <div class="col-md-3">
    <button type="submit" class="btn btn-primary">Cari</button>
    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
  </div>
</form>

<div class="row g-3">
<?php
// kata kunci dibungkus tanda persen supaya cocok di mana saja
$kata_kunci = "%" . $cari . "%";

if ($pilih_kategori > 0) {
  $perintah = $koneksi->prepare("SELECT a.ID_alat, a.nama_alat, a.deskripsi, a.harga_per_hari, a.stok_total, k.nama_kategori
                                 FROM alat a JOIN kategori k ON a.ID_kategori = k.ID_kategori
                                 WHERE a.nama_alat LIKE ? AND a.ID_kategori = ?
                                 ORDER BY a.nama_alat");
  $perintah->bind_param("si", $kata_kunci, $pilih_kategori);
} else {
  $perintah = $koneksi->prepare("SELECT a.ID_alat, a.nama_alat, a.deskripsi, a.harga_per_hari, a.stok_total, k.nama_kategori
                                 FROM alat a JOIN kategori k ON a.ID_kategori = k.ID_kategori
                                 WHERE a.nama_alat LIKE ?
                                 ORDER BY a.nama_alat");
  $perintah->bind_param("s", $kata_kunci);
}

$perintah->execute();
$hasil = $perintah->get_result();

if ($hasil->num_rows == 0) {
  echo '<div class="col-12"><div class="alert alert-info">Alat tidak ditemukan.</div></div>';
}

while ($alat = $hasil->fetch_assoc()) {
  ?>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <span class="badge text-bg-secondary mb-2"><?php echo htmlspecialchars($alat["nama_kategori"]); ?></span>
        <h2 class="h5"><?php echo htmlspecialchars($alat["nama_alat"]); ?></h2>
        <p class="text-body-secondary small"><?php echo htmlspecialchars($alat["deskripsi"]); ?></p>
        <p class="harga mb-1">Rp <?php echo number_format($alat["harga_per_hari"], 0, ",", "."); ?> / hari</p>
        <p class="small mb-3">Stok total: <?php echo $alat["stok_total"]; ?> unit</p>
        <a href="detail.php?ID_alat=<?php echo $alat["ID_alat"]; ?>" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
        <?php if (isset($_SESSION["id_user"]) && $_SESSION["role"] == "penyewa") { ?>
          <form action="tambah_keranjang.php" method="post" class="d-inline">
            <input type="hidden" name="id_alat" value="<?php echo $alat["ID_alat"]; ?>">
            <input type="hidden" name="jumlah" value="1">
            <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
          </form>
        <?php } else if (!isset($_SESSION["id_user"])) { ?>
          <a href="login.php" class="btn btn-outline-secondary btn-sm">Masuk untuk sewa</a>
        <?php } ?>
      </div>
    </div>
  </div>
  <?php
}

$perintah->close();
?>
</div>
<?php
include "footer.php";
?>
