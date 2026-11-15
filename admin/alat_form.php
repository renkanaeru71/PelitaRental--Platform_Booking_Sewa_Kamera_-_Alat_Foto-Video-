<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$judul = "Form Alat";
$akar = "../";

$id_alat = 0;
if (isset($_GET["ID_alat"])) {
  $id_alat = (int)$_GET["ID_alat"];
}

$id_kategori = 0;
$nama_alat = "";
$deskripsi = "";
$harga_per_hari = "";
$stok_total = 0;

// kalau sedang mengubah, data lama diambil dulu sebagai isi awal form
if ($id_alat > 0) {
  $perintah = $koneksi->prepare("SELECT ID_kategori, nama_alat, deskripsi, harga_per_hari, stok_total FROM alat WHERE ID_alat = ?");
  $perintah->bind_param("i", $id_alat);
  $perintah->execute();
  // dikosongkan dulu supaya tidak terbaca nilai lama kalau baris tidak ditemukan
  $id_kategori = 0;
  $nama_alat = "";
  $deskripsi = "";
  $harga_per_hari = "";
  $stok_total = 0;
  $perintah->bind_result($id_kategori, $nama_alat, $deskripsi, $harga_per_hari, $stok_total);
  $perintah->fetch();
  $perintah->close();
}

// daftar kategori untuk pilihan dropdown
$daftar_kategori = $koneksi->query("SELECT ID_kategori, nama_kategori FROM kategori ORDER BY nama_kategori");

include "../header.php";
?>
<nav class="mb-3">
  <a href="alat.php">&larr; Kembali ke data alat</a>
</nav>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body">
        <h1 class="h4 mb-3"><?php if ($id_alat > 0) { echo "Ubah Alat"; } else { echo "Tambah Alat Baru"; } ?></h1>

        <form action="alat_simpan.php" method="post">
          <input type="hidden" name="id_alat" value="<?php echo $id_alat; ?>">

          <div class="mb-3">
            <label for="id_kategori" class="form-label">Kategori</label>
            <select class="form-select" id="id_kategori" name="id_kategori" required>
              <option value="">-- Pilih Kategori --</option>
              <?php
              while ($k = $daftar_kategori->fetch_assoc()) {
                ?>
                <option value="<?php echo $k["ID_kategori"]; ?>" <?php if ($k["ID_kategori"] == $id_kategori) { echo "selected"; } ?>>
                  <?php echo htmlspecialchars($k["nama_kategori"]); ?>
                </option>
                <?php
              }
              ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="nama_alat" class="form-label">Nama Alat</label>
            <input type="text" class="form-control" id="nama_alat" name="nama_alat" value="<?php echo htmlspecialchars($nama_alat); ?>" required>
          </div>

          <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo htmlspecialchars($deskripsi); ?></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="harga_per_hari" class="form-label">Harga per Hari (Rp)</label>
              <input type="number" class="form-control" id="harga_per_hari" name="harga_per_hari" value="<?php echo htmlspecialchars($harga_per_hari); ?>" min="1" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="stok_total" class="form-label">Stok Total (unit)</label>
              <input type="number" class="form-control" id="stok_total" name="stok_total" value="<?php echo $stok_total; ?>" min="0" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="alat.php" class="btn btn-outline-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
include "../footer.php";
?>
