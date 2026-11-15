<?php
session_start();
include "koneksi.php";

$judul = "Detail Alat";
$akar = "";

$id_alat = 0;
if (isset($_GET["ID_alat"])) {
  $id_alat = (int)$_GET["ID_alat"];
}

// data alat diambil dengan prepared statement karena ID berasal dari alamat
$perintah = $koneksi->prepare("SELECT a.ID_alat, a.nama_alat, a.deskripsi, a.harga_per_hari, a.stok_total, k.nama_kategori
                               FROM alat a JOIN kategori k ON a.ID_kategori = k.ID_kategori
                               WHERE a.ID_alat = ?");
$perintah->bind_param("i", $id_alat);
$perintah->execute();
$hasil = $perintah->get_result();

include "header.php";

if ($hasil->num_rows == 0) {
  echo '<div class="alert alert-warning">Alat tidak ditemukan.</div>';
} else {
  $alat = $hasil->fetch_assoc();
  ?>
  <nav class="mb-3">
    <a href="index.php">&larr; Kembali ke katalog</a>
  </nav>
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <span class="badge text-bg-secondary mb-2"><?php echo htmlspecialchars($alat["nama_kategori"]); ?></span>
          <h1 class="h3"><?php echo htmlspecialchars($alat["nama_alat"]); ?></h1>
          <p><?php echo htmlspecialchars($alat["deskripsi"]); ?></p>
          <p class="harga">Rp <?php echo number_format($alat["harga_per_hari"], 0, ",", "."); ?> / hari</p>
          <p>Stok total: <?php echo $alat["stok_total"]; ?> unit</p>
          <p class="text-body-secondary small">Sisa stok pada tanggal tertentu bisa dilihat di halaman keranjang setelah alat dimasukkan.</p>

          <form action="tambah_keranjang.php" method="post" class="row g-2 align-items-end">
            <input type="hidden" name="id_alat" value="<?php echo $alat["ID_alat"]; ?>">
            <div class="col-auto">
              <label for="jumlah" class="form-label">Jumlah Unit</label>
              <input type="number" class="form-control" id="jumlah" name="jumlah" value="1" min="1" max="<?php echo $alat["stok_total"]; ?>">
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
}

$perintah->close();
include "footer.php";
?>
