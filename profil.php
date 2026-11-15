<?php
session_start();
include "koneksi.php";
include "cek_login.php";

$judul = "Profil Saya";
$akar = "";

$id_user = (int)$_SESSION["id_user"];

// ambil data profil terbaru dari database
$perintah = $koneksi->prepare("SELECT Nama, Email, no_hp, Created_at FROM user WHERE ID_user = ?");
$perintah->bind_param("i", $id_user);
$perintah->execute();
$nama_db = "";
$email_db = "";
$no_hp_db = "";
$created_at = "";
$perintah->bind_result($nama_db, $email_db, $no_hp_db, $created_at);
$perintah->fetch();
$perintah->close();

include "header.php";
?>
<h1 class="h3 mb-3">Profil Saya</h1>

<div class="row">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body">
        <h2 class="h5 mb-3">Edit Data Profil</h2>
        <form action="proses_profil.php" method="post">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($nama_db); ?>" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email_db); ?>" required>
          </div>
          <div class="mb-3">
            <label for="no_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($no_hp_db); ?>" required>
          </div>
          <div class="mb-3">
            <label for="password_baru" class="form-label">Password Baru (opsional)</label>
            <input type="password" class="form-control" id="password_baru" name="password_baru" minlength="6">
            <div class="form-text">Kosongkan kalau tidak ingin mengganti password. Minimal 6 karakter.</div>
          </div>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card">
      <div class="card-body">
        <h2 class="h5 mb-3">Informasi Akun</h2>
        <p class="mb-1">Terdaftar sejak: <?php echo date("d/m/Y", strtotime($created_at)); ?></p>
        <p class="mb-0">Role: <?php echo htmlspecialchars($_SESSION["role"]); ?></p>
      </div>
    </div>
  </div>
</div>
<?php
include "footer.php";
?>
