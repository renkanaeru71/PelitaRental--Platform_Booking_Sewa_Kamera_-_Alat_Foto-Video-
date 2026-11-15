<?php
session_start();
include "koneksi.php";
include "cek_login.php";

$judul = "Bukti Booking";
$akar = "";

$id_booking = 0;
if (isset($_GET["ID_booking"])) {
  $id_booking = (int)$_GET["ID_booking"];
}

$id_user = (int)$_SESSION["id_user"];

// booking diambil dengan validasi ID_user milik penyewa yang sedang login
$perintah = $koneksi->prepare("SELECT b.ID_booking, b.Kode_booking, b.Tanggal_Mulai, b.Tanggal_selesai, b.Durasi,
                                      b.Total_bayar, b.Jenis_Jaminan, b.Status, b.Batas_Bayar, b.Created_at,
                                      u.Nama, u.Email, u.no_hp
                               FROM booking b
                               JOIN user u ON b.ID_user = u.ID_user
                               WHERE b.ID_booking = ? AND b.ID_user = ?");
$perintah->bind_param("ii", $id_booking, $id_user);
$perintah->execute();
$hasil = $perintah->get_result();

include "header.php";

if ($hasil->num_rows == 0) {
  echo '<div class="alert alert-warning">Data booking tidak ditemukan.</div>';
} else {
  $b = $hasil->fetch_assoc();
  ?>
  <div class="card mx-auto bukti-booking">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Bukti Booking PelitaRental</h1>
        <button type="button" class="btn btn-outline-secondary btn-sm tombol-cetak" onclick="window.print()">Cetak</button>
      </div>

      <div class="alert alert-secondary py-2 small mb-3">
        Tunjukkan bukti ini ke toko saat pembayaran dan pengambilan alat.
      </div>

      <table class="table table-sm table-borderless mb-3">
        <tr>
          <td style="width: 160px;">Kode Booking</td>
          <td>: <strong><?php echo htmlspecialchars($b["Kode_booking"]); ?></strong></td>
        </tr>
        <tr>
          <td>Status</td>
          <td>: <span class="badge text-bg-warning"><?php echo htmlspecialchars($b["Status"]); ?></span></td>
        </tr>
        <tr>
          <td>Nama Penyewa</td>
          <td>: <?php echo htmlspecialchars($b["Nama"]); ?> (<?php echo htmlspecialchars($b["no_hp"]); ?>)</td>
        </tr>
        <tr>
          <td>Periode Sewa</td>
          <td>: <?php echo date("d/m/Y", strtotime($b["Tanggal_Mulai"])); ?> s.d. <?php echo date("d/m/Y", strtotime($b["Tanggal_selesai"])); ?> (<?php echo $b["Durasi"]; ?> hari)</td>
        </tr>
        <tr>
          <td>Jaminan</td>
          <td>: <?php echo htmlspecialchars($b["Jenis_Jaminan"]); ?> (diserahkan saat ambil alat)</td>
        </tr>
        <tr>
          <td>Batas Bayar di Toko</td>
          <td>: <span class="text-danger fw-semibold"><?php echo date("d/m/Y H:i", strtotime($b["Batas_Bayar"])); ?> WIB</span></td>
        </tr>
      </table>

      <h2 class="h6 mb-2">Daftar Alat</h2>
      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>Alat</th>
            <th>Harga/Hari</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $detail = $koneksi->prepare("SELECT a.nama_alat, d.Harga_Satuan, d.Jumlah, d.Subtotal
                                     FROM detail_booking d
                                     JOIN alat a ON d.ID_alat = a.ID_alat
                                     WHERE d.ID_booking = ?");
        $detail->bind_param("i", $id_booking);
        $detail->execute();
        $hasil_detail = $detail->get_result();
        while ($d = $hasil_detail->fetch_assoc()) {
          ?>
          <tr>
            <td><?php echo htmlspecialchars($d["nama_alat"]); ?></td>
            <td>Rp <?php echo number_format($d["Harga_Satuan"], 0, ",", "."); ?></td>
            <td><?php echo $d["Jumlah"]; ?> unit</td>
            <td>Rp <?php echo number_format($d["Subtotal"], 0, ",", "."); ?></td>
          </tr>
          <?php
        }
        $detail->close();
        ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-end">Total Bayar:</th>
            <th class="h6 mb-0">Rp <?php echo number_format($b["Total_bayar"], 0, ",", "."); ?></th>
          </tr>
        </tfoot>
      </table>

      <div class="mt-3 tombol-cetak">
        <a href="riwayat.php" class="btn btn-primary btn-sm">Lihat Riwayat Sewa</a>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Katalog</a>
      </div>
    </div>
  </div>
  <?php
}

$perintah->close();
include "footer.php";
?>
