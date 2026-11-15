<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";
include "../kadaluarsa.php";

$judul = "Operasional Toko";
$akar = "../";

// bagian 1: booking yang sudah dibayar dan menunggu diambil
$hasil_ambil = $koneksi->query("SELECT b.ID_booking, b.Kode_booking, b.Tanggal_Mulai, b.Tanggal_selesai, b.Durasi,
                                       b.Total_bayar, b.Jenis_Jaminan, u.Nama, u.no_hp
                                FROM booking b
                                JOIN user u ON b.ID_user = u.ID_user
                                WHERE b.Status = 'dibayar'
                                ORDER BY b.Tanggal_Mulai");

// bagian 2: booking yang sedang disewa (diambil) dan menunggu dikembalikan
$hasil_kembali = $koneksi->query("SELECT b.ID_booking, b.Kode_booking, b.Tanggal_Mulai, b.Tanggal_selesai, b.Durasi,
                                         b.Total_bayar, b.Jenis_Jaminan, u.Nama, u.no_hp
                                  FROM booking b
                                  JOIN user u ON b.ID_user = u.ID_user
                                  WHERE b.Status = 'diambil'
                                  ORDER BY b.Tanggal_selesai");

include "../header.php";
?>
<h1 class="h3 mb-3">Operasional: Pengambilan & Pengembalian</h1>

<!-- bagian pengambilan alat -->
<div class="card mb-4">
  <div class="card-header fw-bold bg-body-tertiary">
    1. Siap Diambil (Status: dibayar)
  </div>
  <div class="card-body">
    <p class="small text-body-secondary mb-3">
      Pastikan kartu jaminan fisik diterima dan disimpan sebelum menekan tombol <strong>Konfirmasi Diambil</strong>.
    </p>

    <?php if ($hasil_ambil->num_rows == 0) { ?>
      <div class="alert alert-info mb-0">Tidak ada alat yang menunggu diambil.</div>
    <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Penyewa</th>
              <th>Mulai Sewa</th>
              <th>Durasi</th>
              <th>Jaminan yang Diterima</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php
          while ($a = $hasil_ambil->fetch_assoc()) {
            ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($a["Kode_booking"]); ?></strong></td>
              <td>
                <?php echo htmlspecialchars($a["Nama"]); ?><br>
                <small class="text-body-secondary"><?php echo htmlspecialchars($a["no_hp"]); ?></small>
              </td>
              <td><?php echo date("d/m/Y", strtotime($a["Tanggal_Mulai"])); ?></td>
              <td><?php echo $a["Durasi"]; ?> hari</td>
              <td><span class="badge text-bg-warning text-dark"><?php echo htmlspecialchars($a["Jenis_Jaminan"]); ?></span></td>
              <td>
                <form action="proses_status.php" method="post" onsubmit="return confirm('Konfirmasi jaminan diterima dan alat diserahkan?')">
                  <input type="hidden" name="id_booking" value="<?php echo $a["ID_booking"]; ?>">
                  <input type="hidden" name="status_asal" value="dibayar">
                  <input type="hidden" name="status_baru" value="diambil">
                  <button type="submit" class="btn btn-sm btn-primary">Konfirmasi Diambil</button>
                </form>
              </td>
            </tr>
            <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
  </div>
</div>

<!-- bagian pengembalian alat -->
<div class="card">
  <div class="card-header fw-bold bg-body-tertiary">
    2. Sedang Disewa (Status: diambil)
  </div>
  <div class="card-body">
    <p class="small text-body-secondary mb-3">
      Periksa kondisi alat dan serahkan kembali kartu jaminan kepada penyewa saat menekan tombol <strong>Konfirmasi Selesai</strong>. Setelah selesai, stok unit otomatis kembali tersedia.
    </p>

    <?php if ($hasil_kembali->num_rows == 0) { ?>
      <div class="alert alert-info mb-0">Tidak ada alat yang sedang disewa.</div>
    <?php } else { ?>
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Penyewa</th>
              <th>Batas Kembali</th>
              <th>Jaminan yang Disimpan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php
          while ($k = $hasil_kembali->fetch_assoc()) {
            ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($k["Kode_booking"]); ?></strong></td>
              <td>
                <?php echo htmlspecialchars($k["Nama"]); ?><br>
                <small class="text-body-secondary"><?php echo htmlspecialchars($k["no_hp"]); ?></small>
              </td>
              <td><?php echo date("d/m/Y", strtotime($k["Tanggal_selesai"])); ?></td>
              <td><span class="badge text-bg-warning text-dark"><?php echo htmlspecialchars($k["Jenis_Jaminan"]); ?></span></td>
              <td>
                <form action="proses_status.php" method="post" onsubmit="return confirm('Konfirmasi alat lengkap dan kartu jaminan sudah dikembalikan?')">
                  <input type="hidden" name="id_booking" value="<?php echo $k["ID_booking"]; ?>">
                  <input type="hidden" name="status_asal" value="diambil">
                  <input type="hidden" name="status_baru" value="selesai">
                  <button type="submit" class="btn btn-sm btn-success">Konfirmasi Selesai</button>
                </form>
              </td>
            </tr>
            <?php
          }
          ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
  </div>
</div>

<?php
include "../footer.php";
?>
