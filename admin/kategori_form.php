<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$judul = "Form Kategori";
$akar = "../";

$id_kategori = 0;
if (isset($_GET["ID_kategori"])) {
  $id_kategori = (int)$_GET["ID_kategori"];
}

$nama_kategori = "";
if ($id_kategori > 0) {
  $perintah = $koneksi->prepare("SELECT nama_kategori FROM kategori WHERE ID_kategori = ?");
  $perintah->bind_param("i", $id_kategori);
  $perintah->execute();
  $perintah->bind_result($nama_kategori);
  $perintah->fetch();
  $perintah->close();
}

include "../header.php";
?>
<nav class="mb-3">
  <a href="kategori.php">&larr; Kembali ke daftar kategori</a>
</nav>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h1 class="h4 mb-3"><?php if ($id_kategori > 0) { echo "Ubah Kategori"; } else { echo "Tambah Kategori Baru"; } ?></h1>
        <form action="kategori_simpan.php" method="post">
          <input type="hidden" name="id_kategori" value="<?php echo $id_kategori; ?>">
          <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?php echo htmlspecialchars($nama_kategori); ?>" required>
          </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="kategori.php" class="btn btn-outline-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
include "../footer.php";
?>
