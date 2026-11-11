<?php
declare(strict_types=1);

require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya role admin yang bisa akses
cek_admin();

$admin_id    = (int)($_SESSION['user_id'] ?? 0);
$admin_nama  = $_SESSION['nama'] ?? 'Admin Toko';
$admin_email = $_SESSION['email'] ?? '';
$admin_phone = $_SESSION['no_hp'] ?? '';
$admin_role  = $_SESSION['role'] ?? 'admin';

if ($admin_id > 0) {
    $q_adm = mysqli_query($koneksi, "SELECT * FROM user WHERE ID_user = $admin_id LIMIT 1");
    if ($q_adm && ($adm_row = mysqli_fetch_assoc($q_adm))) {
        $admin_nama  = $adm_row['Nama'] ?? $adm_row['nama'] ?? $admin_nama;
        $admin_email = $adm_row['Email'] ?? $adm_row['email'] ?? $admin_email;
        $admin_phone = $adm_row['no_hp'] ?? $admin_phone;
        $admin_role  = $adm_row['Role'] ?? $adm_row['role'] ?? $admin_role;
        $_SESSION['nama']  = $admin_nama;
        $_SESSION['email'] = $admin_email;
        $_SESSION['no_hp'] = $admin_phone;
    }
}

// Inisial avatar admin
$adm_parts = preg_split('/\s+/', trim($admin_nama));
if ($adm_parts && count($adm_parts) >= 2) {
    $admin_initials = mb_substr($adm_parts[0], 0, 1) . mb_substr($adm_parts[1], 0, 1);
} else {
    $admin_initials = mb_substr($admin_nama, 0, 2);
}
$admin_initials = strtoupper($admin_initials);

// Ambil kategori dari database
$list_kategori = [];
$res_kat = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
if ($res_kat) {
    while ($k = mysqli_fetch_assoc($res_kat)) {
        $list_kategori[] = $k;
    }
}

// Ambil daftar alat dari database
$list_alat = [];
$sql_alat = "SELECT a.*, k.nama_kategori 
             FROM alat a 
             JOIN kategori k ON a.ID_kategori = k.ID_kategori 
             ORDER BY a.ID_alat DESC";
$res_alat = mysqli_query($koneksi, $sql_alat);
if ($res_alat) {
    while ($a = mysqli_fetch_assoc($res_alat)) {
        $list_alat[] = [
            'id'        => (int)$a['ID_alat'],
            'kategori'  => $a['nama_kategori'],
            'nama'      => $a['nama_alat'],
            'harga'     => (float)$a['harga_per_hari'],
            'stok'      => (int)$a['stok_total'],
            'deskripsi' => $a['deskripsi'] ?? ''
        ];
    }
}

// Ambil seluruh data booking dari database pelitarental
$list_booking = [];
$sql_booking = "SELECT b.*, u.Nama as nama_penyewa, u.no_hp,
                GROUP_CONCAT(CONCAT(a.nama_alat, ' (', db.jumlah, 'x)') SEPARATOR ', ') as daftar_alat
                FROM booking b
                JOIN user u ON b.ID_user = u.ID_user
                LEFT JOIN detail_booking db ON b.ID_booking = db.ID_booking
                LEFT JOIN alat a ON db.ID_alat = a.ID_alat
                GROUP BY b.ID_booking
                ORDER BY b.ID_booking DESC";
$res_booking = mysqli_query($koneksi, $sql_booking);
if ($res_booking) {
    while ($b = mysqli_fetch_assoc($res_booking)) {
        $list_booking[] = [
            'id'          => (int)$b['ID_booking'],
            'kode'        => $b['Kode_booking'],
            'penyewa'     => $b['nama_penyewa'],
            'alat'        => $b['daftar_alat'] ?: 'Peralatan Sewa',
            'mulai'       => $b['Tanggal_Mulai'],
            'selesai'     => $b['Tanggal_selesai'],
            'total'       => (float)$b['Total_bayar'],
            'jaminan'     => $b['Jenis_Jaminan'] ?: 'KTM Asli',
            'status'      => $b['Status'],
            'batas_bayar' => $b['Batas_Bayar'] ? date('d M Y H:i', strtotime($b['Batas_Bayar'])) : '-'
        ];
    }
}

// Flash message
$flash_msg = $_SESSION['flash_msg'] ?? '';
unset($_SESSION['flash_msg']);

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function icon(string $name, string $class = ''): string
{
    return '<svg class="icon ' . escape($class) . '" aria-hidden="true"><use href="#icon-' . escape($name) . '"></use></svg>';
}
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff7718">
    <title>Dashboard Admin — PelitaRental</title>

    <script>
        (() => {
            let theme;
            try { theme = localStorage.getItem('pelita-theme'); } catch { theme = null; }
            document.documentElement.dataset.bsTheme = theme === 'dark' ? 'dark' : 'light';
        })();
    </script>

    <!-- Bootstrap 5 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Style PelitaRental -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script id="admin-db-alat" type="application/json"><?= json_encode($list_alat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <script id="admin-db-booking" type="application/json"><?= json_encode($list_booking, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</head>
<body>

<!-- SVG Sprite Ikon -->
<svg xmlns="http://www.w3.org/2000/svg" class="svg-sprite" aria-hidden="true">
    <symbol id="icon-bag" viewBox="0 0 24 24"><path d="M5 7h14l1 14H4L5 7Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2M9 13h6m-3-3v6"/></symbol>
    <symbol id="icon-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
    <symbol id="icon-tools" viewBox="0 0 24 24"><path d="m14.5 6 3.5 3.5m-6 4-8 8-2-2 8-8M14 3a6 6 0 0 0 7 7l-4-4 1-3a6 6 0 0 0-4 0ZM3 3l4 2 1 3 13 13-2 1L6 9 3 8V3Z"/></symbol>
    <symbol id="icon-check" viewBox="0 0 24 24"><path d="m7 12 3 3 7-7"/><path d="m12 2 3 2 4 1 1 4 2 3-2 3-1 4-4 1-3 2-3-2-4-1-1-4-2-3 2-3 1-4 4-1 3-2Z"/></symbol>
    <symbol id="icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
    <symbol id="icon-camera" viewBox="0 0 24 24"><path d="m8 5 1.5-2h5L16 5h4a2 2 0 0 1 2 2v12H2V7a2 2 0 0 1 2-2h4Z"/><circle cx="12" cy="12" r="4"/><path d="M5 8h.01"/></symbol>
    <symbol id="icon-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 2v6m10-6v6M3 11h18m-14 5h3m4 0h3"/></symbol>
    <symbol id="icon-orders" viewBox="0 0 24 24"><path d="M5 8h14l1 13H4L5 8Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2m-8 5 3 3 5-5"/></symbol>
    <symbol id="icon-moon" viewBox="0 0 24 24"><path d="M20.5 14.5A9 9 0 0 1 9.5 3.5a9 9 0 1 0 11 11Z"/><path d="M18 2v4m-2-2h4"/></symbol>
    <symbol id="icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/></symbol>
    <symbol id="icon-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
    <symbol id="icon-close" viewBox="0 0 24 24"><path d="m6 6 12 12M6 18 18 6"/></symbol>
    <symbol id="icon-trash" viewBox="0 0 24 24"><path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/></symbol>
    <symbol id="icon-chevron" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></symbol>
    <symbol id="icon-download" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></symbol>
    <symbol id="icon-profile" viewBox="0 0 24 24"><circle cx="9" cy="7" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M18 5h3m-3 6h3m-3 6h3"/></symbol>
</svg>

<a class="skip-link" href="#main-content">Lewati ke konten utama</a>

<!-- Sidebar Admin -->
<aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Navigasi admin">
    <a class="brand" href="dashboard-admin.php">
        <span class="brand-mark"><?= icon('bag') ?></span>
        <span class="brand-copy">
            <strong>PelitaRental</strong>
            <small>Panel Pengelola Toko</small>
        </span>
    </a>
    <nav class="sidebar-nav">
        <p class="nav-label">Menu Admin</p>
        <button class="nav-item is-active" type="button" data-view="dashboard">
            <?= icon('grid') ?><span>Dashboard</span><span class="active-dot"></span>
        </button>
        <button class="nav-item" type="button" data-view="alat">
            <?= icon('tools') ?><span>Kelola Data Alat</span>
        </button>
        <button class="nav-item" type="button" data-view="verifikasi">
            <?= icon('check') ?><span>Verifikasi Bayar</span>
        </button>
        <button class="nav-item" type="button" data-view="operasional">
            <?= icon('calendar') ?><span>Operasional Ambil/Kembali</span>
        </button>
        <button class="nav-item" type="button" data-view="laporan">
            <?= icon('orders') ?><span>Laporan Transaksi</span>
        </button>
        <button class="nav-item" type="button" data-view="profile">
            <?= icon('profile') ?><span>Profil Saya</span>
        </button>
    </nav>
    <button class="sidebar-close icon-button d-lg-none" type="button" data-bs-dismiss="offcanvas" data-bs-target="#sidebar">
        <?= icon('close') ?>
    </button>
</aside>

<!-- Workspace Area -->
<div class="workspace">
    <!-- Topbar Header -->
    <header class="topbar">
        <button class="icon-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <?= icon('menu') ?>
        </button>
        <div class="fw-bold fs-6 text-primary d-none d-md-block">
            Panel Operasional Admin PelitaRental
        </div>
        <div class="header-actions">
            <button class="icon-button" type="button" id="theme-toggle" title="Ganti Tema">
                <?= icon('moon') ?>
            </button>
            <span class="header-divider"></span>
            <div class="dropdown">
                <button class="user-menu" type="button" data-bs-toggle="dropdown">
                    <span class="avatar admin-avatar" id="admin-header-initials"><?= escape($admin_initials) ?></span>
                    <span class="user-copy">
                        <strong id="admin-header-name"><?= escape($admin_nama) ?></strong>
                        <small>Petugas Pengelola</small>
                    </span>
                    <?= icon('chevron', 'user-chevron') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-header">Akses Pengelola</span></li>
                    <li><button class="dropdown-item" type="button" data-view="profile">Profil saya</button></li>
                    <li><a class="dropdown-item text-primary fw-semibold" href="dashboard-user.php">Lihat Toko (Katalog)</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Keluar</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="main-content" id="main-content" tabindex="-1">
        <!-- 1. VIEW DASHBOARD ADMIN -->
        <section id="view-dashboard" class="page-view">
            <div class="page-heading">
                <h1>Statistik Operasional Toko</h1>
                <p>Ringkasan pemesanan aktif, pending bayar di toko, dan pendapatan sewa.</p>
            </div>

            <?php if ($flash_msg): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <?= escape($flash_msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- 4 Kartu Statistik PRD Admin Fitur 1 -->
            <div class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top">
                        <h2>Booking Aktif</h2>
                        <span class="stat-icon blue"><?= icon('camera') ?></span>
                    </div>
                    <strong class="stat-value" id="stat-aktif">0</strong>
                    <p>Unit sedang dibawa penyewa</p>
                </article>
                <article class="stat-card">
                    <div class="stat-top">
                        <h2>Pending Bayar</h2>
                        <span class="stat-icon amber"><?= icon('clock') ?></span>
                    </div>
                    <strong class="stat-value" id="stat-pending">0</strong>
                    <p>Batas 24 jam pembayaran toko</p>
                </article>
                <article class="stat-card">
                    <div class="stat-top">
                        <h2>Ambil &amp; Kembali Hari Ini</h2>
                        <span class="stat-icon orange"><?= icon('calendar') ?></span>
                    </div>
                    <strong class="stat-value" id="stat-operasional">0</strong>
                    <p>Jadwal serah terima di toko</p>
                </article>
                <article class="stat-card">
                    <div class="stat-top">
                        <h2>Total Pendapatan</h2>
                        <span class="stat-icon green"><?= icon('check') ?></span>
                    </div>
                    <strong class="stat-value" id="stat-pendapatan">Rp 0</strong>
                    <p>Transaksi lunas terverifikasi</p>
                </article>
            </div>

            <div class="data-table-card">
                <div class="data-table-header">
                    <h3>Antrean Pending Pembayaran (Batas 1 Hari)</h3>
                    <button class="btn btn-outline-secondary btn-sm" type="button" data-view="verifikasi">Lihat Semua</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Nama Penyewa</th>
                                <th>Alat</th>
                                <th>Total Bayar</th>
                                <th>Status Batas</th>
                                <th class="text-end">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody id="admin-quick-pending"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 2. VIEW KELOLA ALAT (CRUD) -->
        <section id="view-alat" class="page-view" hidden>
            <div class="page-heading d-flex justify-content-between align-items-center">
                <div>
                    <h1>Kelola Data Alat &amp; Stok</h1>
                    <p>Tambah, edit harga sewa per hari, dan cek ketersediaan stok fisik.</p>
                </div>
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal-tambah-alat">
                    + Tambah Alat Baru
                </button>
            </div>

            <div class="data-table-card">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kategori</th>
                                <th>Nama Peralatan</th>
                                <th>Harga / Hari</th>
                                <th>Stok Total</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-tabel-alat"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 3. VIEW VERIFIKASI PEMBAYARAN -->
        <section id="view-verifikasi" class="page-view" hidden>
            <div class="page-heading">
                <h1>Verifikasi Pembayaran di Toko</h1>
                <p>Cocokkan kode booking, nama penyewa, dan terima pembayaran langsung sebelum penyerahan alat.</p>
            </div>

            <div class="data-table-card">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Penyewa</th>
                                <th>Alat</th>
                                <th>Total Bayar</th>
                                <th>Jaminan Identitas</th>
                                <th>Batas Bayar</th>
                                <th class="text-end">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-tabel-verifikasi"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 4. VIEW OPERASIONAL AMBIL & KEMBALI -->
        <section id="view-operasional" class="page-view" hidden>
            <div class="page-heading">
                <h1>Operasional Pengambilan &amp; Pengembalian</h1>
                <p>Konfirmasi serah terima alat dan simpan/kembalikan kartu identitas fisik jaminan.</p>
            </div>

            <div class="data-table-card mb-4">
                <div class="data-table-header">
                    <h3>1. Konfirmasi Pengambilan (Terima Jaminan KTP/KTM)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Penyewa</th>
                                <th>Alat Disewa</th>
                                <th>Jaminan Fisik</th>
                                <th class="text-end">Aksi Serah Terima</th>
                            </tr>
                        </thead>
                        <tbody id="admin-tabel-ambil"></tbody>
                    </table>
                </div>
            </div>

            <div class="data-table-card">
                <div class="data-table-header">
                    <h3>2. Konfirmasi Pengembalian (Stok Bertambah Otomatis &amp; Kembalikan Jaminan)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Penyewa</th>
                                <th>Alat Disewa</th>
                                <th>Jaminan</th>
                                <th class="text-end">Aksi Pengembalian</th>
                            </tr>
                        </thead>
                        <tbody id="admin-tabel-kembali"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 5. VIEW LAPORAN TRANSAKSI -->
        <section id="view-laporan" class="page-view" hidden>
            <div class="page-heading d-flex justify-content-between align-items-center">
                <div>
                    <h1>Laporan Seluruh Transaksi</h1>
                    <p>Riwayat seluruh transaksi sewa dan ekspor data ke format CSV / Excel.</p>
                </div>
                <button class="btn btn-primary" type="button" id="btn-export-csv">
                    <?= icon('download') ?> Ekspor ke CSV
                </button>
            </div>

            <div class="data-table-card">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Penyewa</th>
                                <th>Alat</th>
                                <th>Periode Sewa</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="admin-tabel-laporan"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 6. VIEW PROFIL ADMIN -->
        <section id="view-profile" class="page-view" hidden>
            <div class="page-heading">
                <h1>Profil Petugas Pengelola</h1>
                <p>Kelola data akun administrator dan keamanan login sistem.</p>
            </div>
            <form class="profile-card" id="profile-form-admin" action="proses_profil.php" method="POST">
                <div class="profile-intro">
                    <span class="avatar avatar-large admin-avatar" id="admin-profile-initials"><?= escape($admin_initials) ?></span>
                    <div>
                        <h2 class="mb-1" id="admin-profile-display-name"><?= escape($admin_nama) ?></h2>
                        <p class="text-muted mb-0">Akun Administrator · Petugas Pengelola Toko PelitaRental</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="admin-profile-name">Nama Petugas</label>
                    <input class="form-control" id="admin-profile-name" name="name" required minlength="2" value="<?= escape($admin_nama) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="admin-profile-email">Alamat Email</label>
                    <input class="form-control" id="admin-profile-email" name="email" type="email" required value="<?= escape($admin_email) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="admin-profile-phone">Nomor HP / WhatsApp</label>
                    <input class="form-control" id="admin-profile-phone" name="phone" type="tel" value="<?= escape($admin_phone) ?>" placeholder="Contoh: 081234567890">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="admin-profile-password">Password Baru (opsional)</label>
                    <input class="form-control" id="admin-profile-password" name="password" type="password" minlength="6" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>
                <button class="btn btn-primary" type="submit" id="btn-save-admin-profile">Simpan Perubahan</button>
                <p class="profile-feedback mt-2" id="admin-profile-feedback" role="status"></p>
            </form>
        </section>

        <footer class="footer">
            <div class="footer-links">
                <span>&copy; <?= date('Y') ?> PelitaRental Studio — Panel Admin</span>
                <span>·</span>
                <span>Yogyakarta, Indonesia</span>
            </div>
            <div class="system-status">
                <span></span>Server Toko Berjalan Normal
            </div>
        </footer>
    </main>
</div>

<!-- Modal Tambah Alat Baru -->
<div class="modal fade" id="modal-tambah-alat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses_simpan.php" method="POST">
                <input type="hidden" name="id_alat" id="alat-id" value="">
                <div class="modal-header">
                    <h2 class="modal-title">Tambah / Ubah Data Alat</h2>
                    <button class="icon-button" type="button" data-bs-dismiss="modal"><?= icon('close') ?></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="alat-kategori">Kategori</label>
                        <select class="form-select" name="id_kategori" id="alat-kategori" required>
                            <?php foreach ($list_kategori as $kat): ?>
                                <option value="<?= $kat['ID_kategori'] ?>"><?= escape($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="alat-nama">Nama Peralatan</label>
                        <input class="form-control" name="nama_alat" id="alat-nama" required placeholder="Contoh: Sony FX30 Cinema">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" for="alat-harga">Harga Sewa / Hari (Rp)</label>
                            <input class="form-control" type="number" name="harga_per_hari" id="alat-harga" required min="10000" step="5000" placeholder="150000">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="alat-stok">Jumlah Stok</label>
                            <input class="form-control" type="number" name="stok_total" id="alat-stok" required min="0" max="50" placeholder="3">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="alat-deskripsi">Deskripsi Singkat</label>
                        <textarea class="form-control" name="deskripsi" id="alat-deskripsi" rows="2" placeholder="Spesifikasi atau kelengkapan alat..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan Alat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notifikasi Admin -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="admin-toast" class="toast" role="status" aria-live="polite">
        <div class="toast-body">
            <span class="toast-icon text-success"><?= icon('check') ?></span>
            <span id="toast-message"></span>
            <button class="icon-button ms-auto border-0" type="button" data-bs-dismiss="toast"><?= icon('close') ?></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/dashboard-admin.js"></script>
</body>
</html>
