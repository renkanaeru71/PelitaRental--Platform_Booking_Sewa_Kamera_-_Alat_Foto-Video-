<?php
session_start();
include "koneksi.php";
include "cek_login.php";
include "kadaluarsa.php";


$judul = "Keranjang Sewa";
$akar = "";

// update jumlah unit kalau dikirim lewat POST update_keranjang
if (isset($_POST["aksi"]) && $_POST["aksi"] == "ubah_jumlah") {
  $id_ubah = (int)$_POST["id_alat"];
  $jml_baru = (int)$_POST["jumlah"];
  if ($jml_baru < 1) {
    $jml_baru = 1;
  }
  if (isset($_SESSION["keranjang"])) {
    for ($i = 0; $i < count($_SESSION["keranjang"]); $i++) {
      if ($_SESSION["keranjang"][$i]["id_alat"] == $id_ubah) {
        $_SESSION["keranjang"][$i]["jumlah"] = $jml_baru;
      }
    }
  }
  header("Location: keranjang.php");
  exit();
}

$hari_ini = date("Y-m-d");
$tanggal_mulai = $hari_ini;
$tanggal_selesai = $hari_ini;

if (isset($_GET["tanggal_mulai"]) && $_GET["tanggal_mulai"] != "") {
  $tanggal_mulai = $_GET["tanggal_mulai"];
}
if (isset($_GET["tanggal_selesai"]) && $_GET["tanggal_selesai"] != "") {
  $tanggal_selesai = $_GET["tanggal_selesai"];
}

// durasi = (selesai - mulai) + 1 hari
$waktu_mulai = strtotime($tanggal_mulai);
$waktu_selesai = strtotime($tanggal_selesai);
$durasi = 0;
if ($waktu_mulai && $waktu_selesai && $waktu_selesai >= $waktu_mulai) {
  $durasi = (int)(($waktu_selesai - $waktu_mulai) / 86400) + 1;
}

include "header.php";
?>
<h1 class="h3 mb-3">Keranjang Sewa</h1>

<?php
if (!isset($_SESSION["keranjang"]) || count($_SESSION["keranjang"]) == 0) {
  ?>
  <div class="alert alert-info">Keranjang sewa masih kosong. Silakan pilih alat di <a href="index.php">Katalog</a>.</div>
  <?php
  include "footer.php";
  exit();
}
?>

<!-- form pemilihan tanggal sewa -->
<div class="card mb-3">
  <div class="card-body">
    <h2 class="h5">Pilih Rentang Tanggal Sewa</h2>
    <form action="keranjang.php" method="get" id="form-tanggal" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" min="<?php echo $hari_ini; ?>" value="<?php echo htmlspecialchars($tanggal_mulai); ?>" required>
      </div>
      <div class="col-md-4">
        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" min="<?php echo $hari_ini; ?>" value="<?php echo htmlspecialchars($tanggal_selesai); ?>" required>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-secondary">Cek Sisa Stok</button>
      </div>
      <div class="col-12">
        <div id="pesan-error-tanggal" class="text-danger small mt-1"></div>
      </div>
    </form>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <h2 class="h5">Daftar Alat</h2>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Alat</th>
            <th>Harga/Hari</th>
            <th style="width: 140px;">Jumlah</th>
            <th>Sisa Stok (Tanggal Ini)</th>
            <th>Subtotal (<?php echo $durasi; ?> Hari)</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $total_bayar = 0;
        $ada_stok_kurang = false;

        for ($i = 0; $i < count($_SESSION["keranjang"]); $i++) {
          $id_alat = $_SESSION["keranjang"][$i]["id_alat"];
          $jumlah = $_SESSION["keranjang"][$i]["jumlah"];

          $perintah = $koneksi->prepare("SELECT nama_alat, harga_per_hari, stok_total FROM alat WHERE ID_alat = ?");
          $perintah->bind_param("i", $id_alat);
          $perintah->execute();
          // dikosongkan dulu supaya tidak terbaca nilai lama kalau baris tidak ditemukan
          $nama_alat = "";
          $harga_per_hari = 0;
          $stok_total = 0;
          $perintah->bind_result($nama_alat, $harga_per_hari, $stok_total);
          $perintah->fetch();
          $perintah->close();

          // stok sisa = stok_total dikurangi unit pada booking aktif yang bentrok tanggal
          $cek_stok = $koneksi->prepare("SELECT a.stok_total - (
                                           SELECT COALESCE(SUM(d.Jumlah), 0)
                                           FROM detail_booking d
                                           JOIN booking b ON d.ID_booking = b.ID_booking
                                           WHERE d.ID_alat = a.ID_alat
                                             AND b.Status IN ('menunggu_bayar', 'dibayar', 'diambil')
                                             AND b.Tanggal_Mulai <= ?
                                             AND b.Tanggal_selesai >= ?
                                         ) AS sisa
                                         FROM alat a WHERE a.ID_alat = ?");
          $cek_stok->bind_param("ssi", $tanggal_selesai, $tanggal_mulai, $id_alat);
          $cek_stok->execute();
          $sisa = 0;
          $cek_stok->bind_result($sisa);
          $cek_stok->fetch();
          $cek_stok->close();

          // angka negatif tidak ditampilkan, dianggap stok habis
          if ($sisa < 0) {
            $sisa = 0;
          }

          if ($jumlah > $sisa) {
            $ada_stok_kurang = true;
          }

          $subtotal = $harga_per_hari * $jumlah * $durasi;
          $total_bayar = $total_bayar + $subtotal;
          ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($nama_alat); ?></strong></td>
            <td>Rp <?php echo number_format($harga_per_hari, 0, ",", "."); ?></td>
            <td>
              <form action="keranjang.php" method="post" class="d-flex">
                <input type="hidden" name="aksi" value="ubah_jumlah">
                <input type="hidden" name="id_alat" value="<?php echo $id_alat; ?>">
                <input type="number" class="form-control form-control-sm me-1" name="jumlah" value="<?php echo $jumlah; ?>" min="1" max="<?php echo $stok_total; ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Ubah</button>
              </form>
            </td>
            <td>
              <?php if ($sisa >= $jumlah) { ?>
                <span class="badge text-bg-success">Tersedia: <?php echo $sisa; ?> unit</span>
              <?php } else { ?>
                <span class="badge text-bg-danger">Sisa: <?php echo $sisa; ?> unit (kurang)</span>
              <?php } ?>
            </td>
            <td>Rp <?php echo number_format($subtotal, 0, ",", "."); ?></td>
            <td>
              <form action="hapus_keranjang.php" method="post">
                <input type="hidden" name="id_alat" value="<?php echo $id_alat; ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
          <?php
        }
        ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-end">Total Bayar (<?php echo $durasi; ?> hari):</th>
            <th colspan="2" class="h5 mb-0">Rp <?php echo number_format($total_bayar, 0, ",", "."); ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- form konfirmasi booking -->
<div class="card">
  <div class="card-body">
    <h2 class="h5">Formulir Booking</h2>
    <?php if ($ada_stok_kurang) { ?>
      <div class="alert alert-warning">Jumlah unit beberapa alat melebihi sisa stok pada rentang tanggal tersebut. Sesuaikan jumlah unit atau pilih tanggal lain sebelum booking.</div>
    <?php } else if ($durasi < 1) { ?>
      <div class="alert alert-warning">Tanggal selesai harus sama atau setelah tanggal mulai.</div>
    <?php } ?>

    <form action="proses_booking.php" method="post" id="form-booking">
      <input type="hidden" name="tanggal_mulai" value="<?php echo htmlspecialchars($tanggal_mulai); ?>">
      <input type="hidden" name="tanggal_selesai" value="<?php echo htmlspecialchars($tanggal_selesai); ?>">
      <div class="mb-3">
        <label for="jenis_jaminan" class="form-label">Jenis Jaminan Identitas (asli diserahkan di toko)</label>
        <select class="form-select" id="jenis_jaminan" name="jenis_jaminan" required>
          <option value="">-- Pilih Jaminan --</option>
          <option value="KTM Asli">KTM Asli</option>
          <option value="KTP Asli">KTP Asli</option>
          <option value="SIM Asli">SIM Asli</option>
        </select>
      </div>
      <div class="alert alert-secondary small">
        Batas pembayaran di toko adalah <strong>24 jam</strong> sejak booking dibuat. Lewat dari 24 jam, booking otomatis hangus.
      </div>
      <button type="submit" class="btn btn-primary" <?php if ($ada_stok_kurang || $durasi < 1) { echo "disabled"; } ?>>
        Konfirmasi Booking
      </button>
    </form>
  </div>
</div>

<script>
// validasi tanggal di client side (Modul 3 Program 3.14)
const formTanggal = document.getElementById("form-tanggal");
const tglMulai = document.getElementById("tanggal_mulai");
const tglSelesai = document.getElementById("tanggal_selesai");
const errorTanggal = document.getElementById("pesan-error-tanggal");

formTanggal.addEventListener("submit", function (e) {
  const hariIni = "<?php echo $hari_ini; ?>";
  if (tglMulai.value < hariIni) {
    e.preventDefault();
    errorTanggal.innerHTML = "Tanggal mulai tidak boleh sebelum hari ini.";
    return;
  }
  if (tglSelesai.value < tglMulai.value) {
    e.preventDefault();
    errorTanggal.innerHTML = "Tanggal selesai harus sama atau setelah tanggal mulai.";
    return;
  }
  errorTanggal.innerHTML = "";
});
</script>

<?php
include "footer.php";
?>
