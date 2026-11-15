<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";

$judul = "Data Alat";
$akar = "../";

// daftar alat digabung dengan nama kategorinya, diurutkan per kategori
$hasil = $koneksi->query("SELECT a.ID_alat, a.nama_alat, a.harga_per_hari, a.stok_total, k.nama_kategori
                          FROM alat a
                          JOIN kategori k ON a.ID_kategori = k.ID_kategori
                          ORDER BY k.nama_kategori, a.nama_alat");

include "../header.php";
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Data Alat</h1>
  <a href="alat_form.php" class="btn btn-primary btn-sm">Tambah Alat</a>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th style="width: 60px;">ID</th>
            <th>Nama Alat</th>
            <th>Kategori</th>
            <th>Harga/Hari</th>
            <th style="width: 90px;">Stok</th>
            <th style="width: 160px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        while ($alat = $hasil->fetch_assoc()) {
          ?>
          <tr>
            <td><?php echo $alat["ID_alat"]; ?></td>
            <td><strong><?php echo htmlspecialchars($alat["nama_alat"]); ?></strong></td>
            <td><?php echo htmlspecialchars($alat["nama_kategori"]); ?></td>
            <td>Rp <?php echo number_format($alat["harga_per_hari"], 0, ",", "."); ?></td>
            <td><?php echo $alat["stok_total"]; ?> unit</td>
            <td>
              <a href="alat_form.php?ID_alat=<?php echo $alat["ID_alat"]; ?>" class="btn btn-sm btn-outline-secondary">Ubah</a>
              <form action="alat_hapus.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                <input type="hidden" name="id_alat" value="<?php echo $alat["ID_alat"]; ?>">
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
