<?php
session_start();
include "koneksi.php";

$judul = "Masuk";
$akar = "";

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['id_user']) || isset($_SESSION['user_id'])) {
    if (strtolower($_SESSION['role'] ?? '') === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$pesan = $_SESSION["pesan"] ?? '';
$pesan_tipe = $_SESSION["pesan_tipe"] ?? '';
$error = $_SESSION['error'] ?? ($pesan_tipe === 'danger' ? $pesan : '');
$success = $_SESSION['success'] ?? ($pesan_tipe === 'success' ? $pesan : '');
$old_tab = $_SESSION['tab'] ?? 'login';
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['pesan'], $_SESSION['pesan_tipe'], $_SESSION['tab']);

$cookie_user = $_COOKIE['username'] ?? '';
$email_ingat = $_COOKIE['email_ingat'] ?? ($cookie_user !== '' ? $cookie_user : '');

include "header.php";
?>

<div class="row justify-content-center py-4">
  <div class="col-md-6 col-lg-5 col-xl-4">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 text-center mb-4">Selamat Datang di PelitaRental</h1>

        <?php if ($error): ?>
          <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success py-2 small mb-3"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
          <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100 <?= $old_tab === 'login' ? 'active' : '' ?>" id="login-tab" data-bs-toggle="tab"
                    data-bs-target="#login-pane" type="button" role="tab"
                    aria-controls="login-pane" aria-selected="<?= $old_tab === 'login' ? 'true' : 'false' ?>">Login</button>
          </li>
          <li class="nav-item flex-fill text-center" role="presentation">
            <button class="nav-link w-100 <?= $old_tab === 'register' ? 'active' : '' ?>" id="daftar-tab" data-bs-toggle="tab"
                    data-bs-target="#daftar-pane" type="button" role="tab"
                    aria-controls="daftar-pane" aria-selected="<?= $old_tab === 'register' ? 'true' : 'false' ?>">Register</button>
          </li>
        </ul>

        <div class="tab-content" id="authTabsContent">

          <!-- LOGIN -->
          <div class="tab-pane fade <?= $old_tab === 'login' ? 'show active' : '' ?>" id="login-pane" role="tabpanel"
               aria-labelledby="login-tab" tabindex="0">
            <form id="loginForm" action="proses_login.php" method="POST">
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
              <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
          </div>

          <!-- DAFTAR -->
          <div class="tab-pane fade <?= $old_tab === 'register' ? 'show active' : '' ?>" id="daftar-pane" role="tabpanel"
               aria-labelledby="daftar-tab" tabindex="0">
            <form id="daftarForm" action="proses_register.php" method="POST">
              <div class="mb-3">
                <label for="regNama" class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" id="regNama" placeholder="Nama Kamu" required>
              </div>

              <div class="mb-3">
                <label for="regEmail" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="regEmail" placeholder="nama@email.com" required>
              </div>

              <div class="mb-3">
                <label for="regHp" class="form-label">No. HP</label>
                <input type="tel" name="no_hp" class="form-control" id="regHp" placeholder="08xxxxxxxxxx" required>
              </div>

              <div class="mb-3">
                <label for="regPassword" class="form-label">Password (min. 6 karakter)</label>
                <input type="password" name="password" class="form-control" id="regPassword" minlength="6" placeholder="Minimal 6 karakter" required>
              </div>

              <button type="submit" class="btn btn-success w-100">Daftar</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php
include "footer.php";
?>
