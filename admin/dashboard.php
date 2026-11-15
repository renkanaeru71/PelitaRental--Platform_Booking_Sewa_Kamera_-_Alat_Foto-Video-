<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";
include "../kadaluarsa.php";

$judul = "Dashboard Admin";
$akar = "../";

$hari_ini = date("Y-m-d");

// statistik 1: booking aktif berstatus dibayar atau diambil
$hasil_aktif = $koneksi->query("SELECT COUNT(*) AS jumlah FROM booking WHERE Status IN ('dibayar', 'diambil')");
$data_aktif = $hasil_aktif->fetch_assoc();
$jumlah_aktif = $data_aktif["jumlah"];

// statistik 2: booking yang masih menunggu pembayaran
$hasil_pending = $koneksi->query("SELECT COUNT(*) AS jumlah FROM booking WHERE Status = 'menunggu_bayar'");
$data_pending = $hasil_pending->fetch_assoc();
$jumlah_pending = $data_pending["jumlah"];

// statistik 3a: harus diambil hari ini (sudah dibayar dan tanggal mulai hari ini)
$perintah_ambil = $koneksi->prepare("SELECT COUNT(*) AS jumlah FROM booking WHERE Status = 'dibayar' AND Tanggal_Mulai = ?");
$perintah_ambil->bind_param("s", $hari_ini);
$perintah_ambil->execute();
$jumlah_ambil = 0;
$perintah_ambil->bind_result($jumlah_ambil);
$perintah_ambil->fetch();
$perintah_ambil->close();

// statistik 3b: harus dikembalikan hari ini (sedang diambil dan tanggal selesai hari ini)
$perintah_kembali = $koneksi->prepare("SELECT COUNT(*) AS jumlah FROM booking WHERE Status = 'diambil' AND Tanggal_selesai = ?");
$perintah_kembali->bind_param("s", $hari_ini);
$perintah_kembali->execute();
$jumlah_kembali = 0;
$perintah_kembali->bind_result($jumlah_kembali);
$perintah_kembali->fetch();
$perintah_kembali->close();

// statistik 4: total pendapatan dari booking dibayar, diambil, dan selesai
$hasil_pendapatan = $koneksi->query("SELECT COALESCE(SUM(Total_bayar), 0) AS total FROM booking WHERE Status IN ('dibayar', 'diambil', 'selesai')");
$data_pendapatan = $hasil_pendapatan->fetch_assoc();
$total_pendapatan = $data_pendapatan["total"];

include "../header.php";
?>
<h1 class="h3 mb-3">Dashboard Admin</h1>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-body-secondary small">Booking Aktif</div>
        <div class="h3 mb-0"><?php echo $jumlah_aktif; ?></div>
        <div class="small text-body-secondary">status dibayar dan diambil</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-body-secondary small">Pending Bayar</div>
        <div class="h3 mb-0"><?php echo $jumlah_pending; ?></div>
        <div class="small text-body-secondary">menunggu_bayar</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-body-secondary small">Jadwal Hari Ini</div>
        <div class="h3 mb-0"><?php echo $jumlah_ambil + $jumlah_kembali; ?></div>
        <div class="small text-body-secondary"><?php echo $jumlah_ambil; ?> ambil, <?php echo $jumlah_kembali; ?> kembali</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-body-secondary small">Total Pendapatan</div>
        <div class="h5 mb-0">Rp <?php echo number_format($total_pendapatan, 0, ",", "."); ?></div>
        <div class="small text-body-secondary">dibayar, diambil, selesai</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Harus Diambil Hari Ini (<?php echo date("d/m/Y"); ?>)</h2>
        <?php
        $ambil = $koneksi->prepare("SELECT b.Kode_booking, u.Nama, b.Jenis_Jaminan, b.Total_bayar
                                    FROM booking b JOIN user u ON b.ID_user = u.ID_user
                                    WHERE b.Status = 'dibayar' AND b.Tanggal_Mulai = ?
                                    ORDER BY b.ID_booking");
        $ambil->bind_param("s", $hari_ini);
        $ambil->execute();
        $hasil_ambil = $ambil->get_result();

        if ($hasil_ambil->num_rows == 0) {
          echo '<p class="text-body-secondary mb-0">Tidak ada jadwal pengambilan hari ini.</p>';
        } else {
          ?>
          <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
              <thead>
                <tr><th>Kode</th><th>Penyewa</th><th>Jaminan</th><th>Total</th></tr>
              </thead>
              <tbody>
              <?php
              while ($a = $hasil_ambil->fetch_assoc()) {
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($a["Kode_booking"]); ?></td>
                  <td><?php echo htmlspecialchars($a["Nama"]); ?></td>
                  <td><?php echo htmlspecialchars($a["Jenis_Jaminan"]); ?></td>
                  <td>Rp <?php echo number_format($a["Total_bayar"], 0, ",", "."); ?></td>
                </tr>
                <?php
              }
              ?>
              </tbody>
            </table>
          </div>
          <?php
        }
        $ambil->close();
        ?>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Harus Kembali Hari Ini (<?php echo date("d/m/Y"); ?>)</h2>
        <?php
        $kembali = $koneksi->prepare("SELECT b.Kode_booking, u.Nama, b.Jenis_Jaminan, b.Total_bayar
                                      FROM booking b JOIN user u ON b.ID_user = u.ID_user
                                      WHERE b.Status = 'diambil' AND b.Tanggal_selesai = ?
                                      ORDER BY b.ID_booking");
        $kembali->bind_param("s", $hari_ini);
        $kembali->execute();
        $hasil_kembali = $kembali->get_result();

        if ($hasil_kembali->num_rows == 0) {
          echo '<p class="text-body-secondary mb-0">Tidak ada jadwal pengembalian hari ini.</p>';
        } else {
          ?>
          <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
              <thead>
                <tr><th>Kode</th><th>Penyewa</th><th>Jaminan</th><th>Total</th></tr>
              </thead>
              <tbody>
              <?php
              while ($k = $hasil_kembali->fetch_assoc()) {
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($k["Kode_booking"]); ?></td>
                  <td><?php echo htmlspecialchars($k["Nama"]); ?></td>
                  <td><?php echo htmlspecialchars($k["Jenis_Jaminan"]); ?></td>
                  <td>Rp <?php echo number_format($k["Total_bayar"], 0, ",", "."); ?></td>
                </tr>
                <?php
              }
              ?>
              </tbody>
            </table>
          </div>
          <?php
        }
        $kembali->close();
        ?>
      </div>
    </div>
  </div>
</div>
<?php
include "../footer.php";
?>
