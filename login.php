<?php
session_start();
include "koneksi.php";

$judul = "Masuk";
$akar = "";

// kalau sudah login langsung diarahkan sesuai role
if (isset($_SESSION["id_user"])) {
  if ($_SESSION["role"] == "admin") {
    header("Location: admin/dashboard.php");
  } else {
    header("Location: index.php");
  }
  exit();
}

// email diingat lewat cookie dari proses_login.php
$email_ingat = "";
if (isset($_COOKIE["email_ingat"])) {
  $email_ingat = $_COOKIE["email_ingat"];
}

include "header.php";
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card">
      <div class="card-body">
        <h1 class="h4 mb-3">Masuk</h1>
        <form action="proses_login.php" method="post">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email_ingat); ?>" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="ingat" name="ingat" <?php if ($email_ingat != "") { echo "checked"; } ?>>
            <label class="form-check-label" for="ingat">Ingat email saya</label>
          </div>
          <button type="submit" class="btn btn-primary">Masuk</button>
        </form>
        <p class="mt-3 mb-0">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
      </div>
    </div>
  </div>
</div>
<?php
include "footer.php";
?>
