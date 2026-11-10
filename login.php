<?php
session_start();

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: dashboard-admin.php');
    } else {
        header('Location: dashboard-user.php');
    }
    exit;
}

$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
$old_tab = $_SESSION['tab']     ?? 'login';
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['tab']);

$cookie_user = $_COOKIE['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PelitaRental - Login</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f0f4f8;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
    }

    .auth-card {
      width: 100%;
      max-width: 420px;
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      padding: 28px 28px 24px;
    }

    .auth-card h1 {
      font-size: 1.6rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 20px;
      color: #212529;
    }

    .auth-card .nav-tabs {
      border-bottom: 1px solid #dee2e6;
      margin-bottom: 20px;
    }

    .auth-card .nav-tabs .nav-link {
      color: #0d6efd;
      border: 1px solid transparent;
      border-radius: 8px 8px 0 0;
      font-weight: 500;
    }

    .auth-card .nav-tabs .nav-link.active {
      color: #212529;
      background: #fff;
      border-color: #dee2e6 #dee2e6 #fff;
    }

    .form-label {
      font-weight: 500;
      color: #212529;
    }

    .form-control {
      padding: 10px 12px;
      border-radius: 8px;
    }

    .btn-auth {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      font-weight: 500;
    }

    .btn-daftar {
      background-color: #198754;
      border-color: #198754;
      color: #fff;
    }

    .btn-daftar:hover {
      background-color: #157347;
      border-color: #146c43;
      color: #fff;
    }

    .remember-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
  </style>
</head>
<body>

  <div class="auth-card">
    <h1>Selamat datang di PelitaRental</h1>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success py-2 small mb-3"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="authTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= $old_tab === 'login' ? 'active' : '' ?>" id="login-tab" data-bs-toggle="tab"
                data-bs-target="#login-pane" type="button" role="tab"
                aria-controls="login-pane" aria-selected="<?= $old_tab === 'login' ? 'true' : 'false' ?>">Login</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= $old_tab === 'register' ? 'active' : '' ?>" id="daftar-tab" data-bs-toggle="tab"
                data-bs-target="#daftar-pane" type="button" role="tab"
                aria-controls="daftar-pane" aria-selected="<?= $old_tab === 'register' ? 'true' : 'false' ?>">Daftar</button>
      </li>
    </ul>

    <div class="tab-content" id="authTabsContent">

      <!-- LOGIN -->
      <div class="tab-pane fade <?= $old_tab === 'login' ? 'show active' : '' ?>" id="login-pane" role="tabpanel"
           aria-labelledby="login-tab" tabindex="0">
        <form id="loginForm" action="proses_login.php" method="POST">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="loginEmail" placeholder="nama@email.com"
                   value="<?= htmlspecialchars($cookie_user) ?>" required>
          </div>

          <div class="mb-3">
            <label for="loginPassword" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="loginPassword" placeholder="Masukkan password" required>
          </div>

          <div class="remember-row">
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" name="ingat" id="rememberMe" <?= $cookie_user ? 'checked' : '' ?>>
              <label class="form-check-label" for="rememberMe">Ingat saya</label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-auth">Login</button>

          <div class="text-muted small mt-3 p-2 bg-light rounded">
            <strong>Akun demo admin:</strong><br>
            • Admin: <code>admin@gmail.com</code> / <code>admin123</code>
          </div>
        </form>
      </div>

      <!-- DAFTAR -->
      <div class="tab-pane fade <?= $old_tab === 'register' ? 'show active' : '' ?>" id="daftar-pane" role="tabpanel"
           aria-labelledby="daftar-tab" tabindex="0">
        <form id="daftarForm" action="proses_register.php" method="POST">
          <div class="mb-3">
            <label for="regNama" class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" id="regNama" placeholder="Ahmad Rizki" required>
          </div>

          <div class="mb-3">
            <label for="regEmail" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="regEmail" placeholder="nama@email.com" required>
          </div>

          <div class="mb-3">
            <label for="regHp" class="form-label">No. HP</label>
            <input type="tel" name="no_hp" class="form-control" id="regHp" placeholder="081234567890" required>
          </div>

          <div class="mb-3">
            <label for="regPassword" class="form-label">Password (min. 6 karakter)</label>
            <input type="password" name="password" class="form-control" id="regPassword" minlength="6" placeholder="Minimal 6 karakter" required>
          </div>

          <button type="submit" class="btn btn-auth btn-daftar">Daftar</button>
        </form>
      </div>

    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
