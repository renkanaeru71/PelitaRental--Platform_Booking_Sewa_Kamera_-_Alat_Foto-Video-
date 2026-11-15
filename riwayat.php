<?php
session_start();
include "koneksi.php";
include "cek_login.php";
include "kadaluarsa.php";

$judul = "Riwayat Sewa";
$akar = "";

$id_user = (int)$_SESSION["id_user"];
$hari_ini = date("Y-m-d");

// ambil semua riwayat booking milik penyewa ini, diurutkan dari yang terbaru
$perintah = $koneksi->prepare("SELECT ID_booking, Kode_booking, Tanggal_Mulai, Tanggal_selesai, Durasi, Total_bayar, Jenis_Jaminan, Status, Batas_Bayar
                               FROM booking
                               WHERE ID_user = ?
                               ORDER BY ID_booking DESC");
$perintah->bind_param("i", $id_user);
$perintah->execute();
$hasil = $perintah->get_result();

include "header.php";
?>
<h1 class="h3 mb-3">Riwayat Booking dan Tracking Sewa</h1>

<?php if ($hasil->num_rows == 0) { ?>
  <div class="alert alert-info">Belum ada riwayat booking. Silakan sewa alat di <a href="index.php">Katalog</a>.</div>
<?php } else { ?>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Periode Sewa</th>
              <th>Jaminan</th>
              <th>Total</th>
              <th>Status</th>
              <th>Keterangan / Tracking</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php
          while ($b = $hasil->fetch_assoc()) {
            $badge = "secondary";
            if ($b["Status"] == "menunggu_bayar") {
              $badge = "warning text-dark";
            } else if ($b["Status"] == "dibayar") {
              $badge = "info text-dark";
            } else if ($b["Status"] == "diambil") {
              $badge = "primary";
            } else if ($b["Status"] == "selesai") {
              $badge = "success";
            } else if ($b["Status"] == "gagal") {
              $badge = "danger";
            }

            // hitung tracking sisa hari sewa khusus untuk status 'diambil'
            $tracking = "-";
            if ($b["Status"] == "diambil") {
              $selisih_hari = (int)((strtotime($b["Tanggal_selesai"]) - strtotime($hari_ini)) / 86400);
              if ($selisih_hari > 0) {
                $tracking = '<span class="text-primary fw-semibold">Sisa ' . $selisih_hari . ' hari lagi</span>';
              } else if ($selisih_hari == 0) {
                $tracking = '<span class="text-warning fw-semibold">Batas kembali hari ini</span>';
              } else {
                $terlambat = abs($selisih_hari);
                $tracking = '<span class="text-danger fw-semibold">Terlambat ' . $terlambat . ' hari</span>';
              }
            } else if ($b["Status"] == "menunggu_bayar") {
              $tracking = '<small class="text-danger">Batas bayar: ' . date("d/m H:i", strtotime($b["Batas_Bayar"])) . '</small>';
            }
            ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($b["Kode_booking"]); ?></strong></td>
              <td>
                <?php echo date("d/m/Y", strtotime($b["Tanggal_Mulai"])); ?> - <?php echo date("d/m/Y", strtotime($b["Tanggal_selesai"])); ?>
                <br><small class="text-body-secondary">(<?php echo $b["Durasi"]; ?> hari)</small>
              </td>
              <td><?php echo htmlspecialchars($b["Jenis_Jaminan"]); ?></td>
              <td>Rp <?php echo number_format($b["Total_bayar"], 0, ",", "."); ?></td>
              <td><span class="badge text-bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($b["Status"]); ?></span></td>
              <td><?php echo $tracking; ?></td>
              <td>
                <a href="bukti_booking.php?ID_booking=<?php echo $b["ID_booking"]; ?>" class="btn btn-sm btn-outline-primary">Bukti</a>
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
<?php } ?>

<?php
$perintah->close();
include "footer.php";
?>
