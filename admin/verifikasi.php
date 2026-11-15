<?php
session_start();
include "../koneksi.php";
include "../cek_admin.php";
include "../kadaluarsa.php";

$judul = "Verifikasi Pembayaran";
$akar = "../";

// hanya booking yang masih menunggu pembayaran yang perlu diverifikasi
$hasil = $koneksi->query("SELECT b.ID_booking, b.Kode_booking, b.Tanggal_Mulai, b.Tanggal_selesai, b.Durasi,
                                 b.Total_bayar, b.Jenis_Jaminan, b.Batas_Bayar, u.Nama, u.no_hp
                          FROM booking b
                          JOIN user u ON b.ID_user = u.ID_user
                          WHERE b.Status = 'menunggu_bayar'
                          ORDER BY b.Batas_Bayar");

include "../header.php";
?>
<h1 class="h3 mb-3">Verifikasi Pembayaran</h1>

<div class="alert alert-secondary small">
  Periksa kode booking dan jaminan yang dibawa penyewa. Setelah uang diterima, tekan tombol <strong>Sudah Dibayar</strong>.
</div>

<?php if ($hasil->num_rows == 0) { ?>
  <div class="alert alert-info">Tidak ada booking yang menunggu pembayaran.</div>
<?php } else { ?>
  <?php
  while ($b = $hasil->fetch_assoc()) {
    ?>
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h2 class="h5 mb-1"><?php echo htmlspecialchars($b["Kode_booking"]); ?></h2>
            <div class="small text-body-secondary">
              <?php echo htmlspecialchars($b["Nama"]); ?> &middot; <?php echo htmlspecialchars($b["no_hp"]); ?>
            </div>
          </div>
          <span class="badge text-bg-warning text-dark">menunggu_bayar</span>
        </div>

        <table class="table table-sm table-borderless mb-2">
          <tr>
            <td style="width: 160px;">Periode Sewa</td>
            <td>: <?php echo date("d/m/Y", strtotime($b["Tanggal_Mulai"])); ?> s.d. <?php echo date("d/m/Y", strtotime($b["Tanggal_selesai"])); ?> (<?php echo $b["Durasi"]; ?> hari)</td>
          </tr>
          <tr>
            <td>Jenis Jaminan</td>
            <td>: <strong><?php echo htmlspecialchars($b["Jenis_Jaminan"]); ?></strong></td>
          </tr>
          <tr>
            <td>Batas Bayar</td>
            <td>: <span class="text-danger fw-semibold"><?php echo date("d/m/Y H:i", strtotime($b["Batas_Bayar"])); ?> WIB</span></td>
          </tr>
          <tr>
            <td>Total Bayar</td>
            <td>: <strong>Rp <?php echo number_format($b["Total_bayar"], 0, ",", "."); ?></strong></td>
          </tr>
        </table>

        <h3 class="h6">Alat yang Disewa</h3>
        <ul class="mb-3">
          <?php
          // daftar alat diambil per booking, satu query untuk setiap booking
          $detail = $koneksi->prepare("SELECT a.nama_alat, d.Jumlah, d.Harga_Satuan
                                       FROM detail_booking d
                                       JOIN alat a ON d.ID_alat = a.ID_alat
                                       WHERE d.ID_booking = ?");
          $detail->bind_param("i", $b["ID_booking"]);
          $detail->execute();
          $hasil_detail = $detail->get_result();

          while ($d = $hasil_detail->fetch_assoc()) {
            ?>
            <li>
              <?php echo htmlspecialchars($d["nama_alat"]); ?>
              &mdash; <?php echo $d["Jumlah"]; ?> unit &times; Rp <?php echo number_format($d["Harga_Satuan"], 0, ",", "."); ?>/hari
            </li>
            <?php
          }
          $detail->close();
          ?>
        </ul>

        <form action="proses_status.php" method="post" class="d-inline">
          <input type="hidden" name="id_booking" value="<?php echo $b["ID_booking"]; ?>">
          <input type="hidden" name="status_asal" value="menunggu_bayar">
          <input type="hidden" name="status_baru" value="dibayar">
          <button type="submit" class="btn btn-success btn-sm">Sudah Dibayar</button>
        </form>
      </div>
    </div>
    <?php
  }
  ?>
<?php } ?>

<?php
include "../footer.php";
?>
