<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$judul = "Kelola Kategori";
$akar = "../";

// ambil daftar kategori beserta jumlah alat yang menggunakannya
$hasil = $koneksi->query("SELECT k.ID_kategori, k.nama_kategori, COUNT(a.ID_alat) AS jumlah_alat
                          FROM kategori k
                          LEFT JOIN alat a ON k.ID_kategori = a.ID_kategori
                          GROUP BY k.ID_kategori, k.nama_kategori
                          ORDER BY k.nama_kategori");

include "../header.php";
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Kelola Kategori Alat</h1>
  <a href="kategori_form.php" class="btn btn-primary btn-sm">Tambah Kategori</a>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th style="width: 60px;">ID</th>
            <th>Nama Kategori</th>
            <th style="width: 140px;">Jumlah Alat</th>
            <th style="width: 160px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $hasil->fetch_assoc()) {
          ?>
          <tr>
            <td><?php echo $row["ID_kategori"]; ?></td>
            <td><strong><?php echo htmlspecialchars($row["nama_kategori"]); ?></strong></td>
            <td><?php echo $row["jumlah_alat"]; ?> alat</td>
            <td>
              <a href="kategori_form.php?ID_kategori=<?php echo $row["ID_kategori"]; ?>" class="btn btn-sm btn-outline-secondary">Ubah</a>
              <form action="kategori_hapus.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                <input type="hidden" name="id_kategori" value="<?php echo $row["ID_kategori"]; ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
          <?php
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
include "../footer.php";
?>
