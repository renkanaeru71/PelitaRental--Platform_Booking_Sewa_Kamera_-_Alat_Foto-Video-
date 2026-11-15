<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";
include "../kadaluarsa.php";

$judul = "Laporan Transaksi";
$akar = "../";

// seluruh transaksi diambil beserta nama penyewanya, diurutkan dari yang terbaru
$hasil = $koneksi->query("SELECT b.ID_booking, b.Kode_booking, b.Tanggal_Mulai, b.Tanggal_selesai, b.Durasi,
                                 b.Total_bayar, b.Jenis_Jaminan, b.Status, b.Created_at, u.Nama
                          FROM booking b
                          JOIN user u ON b.ID_user = u.ID_user
                          ORDER BY b.ID_booking DESC");

include "../header.php";
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Laporan Transaksi</h1>
  <a href="ekspor_csv.php" class="btn btn-success btn-sm">Ekspor ke CSV</a>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Kode Booking</th>
            <th>Penyewa</th>
            <th>Periode Sewa</th>
            <th>Durasi</th>
            <th>Jaminan</th>
            <th>Total Bayar</th>
            <th>Status</th>
            <th>Dibuat</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $jumlah_transaksi = 0;
        $total_pendapatan = 0;

        while ($b = $hasil->fetch_assoc()) {
          $jumlah_transaksi = $jumlah_transaksi + 1;
          // pendapatan hanya dihitung dari booking yang sudah dibayar, diambil, atau selesai
          if ($b["Status"] == "dibayar" || $b["Status"] == "diambil" || $b["Status"] == "selesai") {
            $total_pendapatan = $total_pendapatan + $b["Total_bayar"];
          }

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
          ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($b["Kode_booking"]); ?></strong></td>
            <td><?php echo htmlspecialchars($b["Nama"]); ?></td>
            <td>
              <?php echo date("d/m/Y", strtotime($b["Tanggal_Mulai"])); ?> - <?php echo date("d/m/Y", strtotime($b["Tanggal_selesai"])); ?>
            </td>
            <td><?php echo $b["Durasi"]; ?> hari</td>
            <td><?php echo htmlspecialchars($b["Jenis_Jaminan"]); ?></td>
            <td>Rp <?php echo number_format($b["Total_bayar"], 0, ",", "."); ?></td>
            <td><span class="badge text-bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($b["Status"]); ?></span></td>
            <td><?php echo date("d/m/Y H:i", strtotime($b["Created_at"])); ?></td>
          </tr>
          <?php
        }

        if ($jumlah_transaksi == 0) {
          ?>
          <tr>
            <td colspan="8" class="text-center text-body-secondary">Belum ada transaksi.</td>
          </tr>
          <?php
        }
        ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="5" class="text-end">Jumlah Transaksi: <?php echo $jumlah_transaksi; ?></th>
            <th colspan="3">Pendapatan: Rp <?php echo number_format($total_pendapatan, 0, ",", "."); ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
<?php
include "../footer.php";
?>
