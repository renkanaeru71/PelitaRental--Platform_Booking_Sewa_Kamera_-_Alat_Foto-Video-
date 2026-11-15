<?php
session_start();
include "koneksi.php";

$judul = "Daftar Akun";
$akar = "";

if (isset($_SESSION["id_user"])) {
  header("Location: index.php");
  exit();
}

include "header.php";
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card">
      <div class="card-body">
        <h1 class="h4 mb-3">Daftar Akun Penyewa</h1>
        <form action="proses_register.php" method="post">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="no_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password (minimal 6 karakter)</label>
            <input type="password" class="form-control" id="password" name="password" minlength="6" required>
          </div>
          <button type="submit" class="btn btn-primary">Daftar</button>
        </form>
        <p class="mt-3 mb-0">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
      </div>
    </div>
  </div>
</div>
<?php
include "footer.php";
?>
