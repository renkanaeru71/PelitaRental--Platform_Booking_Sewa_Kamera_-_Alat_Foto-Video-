<?php
declare(strict_types=1);

require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan user sudah login
cek_login();

// Ambil info user dari session / database
$user_id    = (int)($_SESSION['user_id'] ?? 0);
$user_name  = $_SESSION['nama'] ?? 'Penyewa';
$user_email = $_SESSION['email'] ?? '';
$user_phone = $_SESSION['no_hp'] ?? '';
$user_role  = $_SESSION['role'] ?? 'penyewa';

if ($user_id > 0) {
    $q_user = mysqli_query($koneksi, "SELECT * FROM user WHERE ID_user = $user_id LIMIT 1");
    if ($q_user && ($u_row = mysqli_fetch_assoc($q_user))) {
        $user_name  = $u_row['Nama'] ?? $u_row['nama'] ?? $user_name;
        $user_email = $u_row['Email'] ?? $u_row['email'] ?? $user_email;
        $user_phone = $u_row['no_hp'] ?? $user_phone;
        $user_role  = $u_row['Role'] ?? $u_row['role'] ?? $user_role;
        $_SESSION['nama']  = $user_name;
        $_SESSION['email'] = $user_email;
        $_SESSION['no_hp'] = $user_phone;
        $_SESSION['role']  = $user_role;
    }
}

// Inisial untuk avatar
$u_parts = preg_split('/\s+/', trim($user_name));
if ($u_parts && count($u_parts) >= 2) {
    $user_initials = mb_substr($u_parts[0], 0, 1) . mb_substr($u_parts[1], 0, 1);
} else {
    $user_initials = mb_substr($user_name, 0, 2);
}
$user_initials = strtoupper($user_initials);

// Query kategori dari database
$categories = [];
$res_kat = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY ID_kategori ASC");
if ($res_kat) {
    while ($row = mysqli_fetch_assoc($res_kat)) {
        $nama_lower = strtolower($row['nama_kategori']);
        $icon = 'camera';
        if (str_contains($nama_lower, 'lensa')) $icon = 'lens';
        elseif (str_contains($nama_lower, 'light')) $icon = 'bolt';
        elseif (str_contains($nama_lower, 'aksesor')) $icon = 'tools';

        $categories[] = [
            'id'   => $nama_lower,
            'name' => $row['nama_kategori'],
            'icon' => $icon
        ];
    }
}

// Query alat dari database pelitarental
$products = [];
$sql_alat = "SELECT a.*, k.nama_kategori 
             FROM alat a 
             JOIN kategori k ON a.ID_kategori = k.ID_kategori 
             ORDER BY a.ID_alat ASC";
$res_alat = mysqli_query($koneksi, $sql_alat);
if ($res_alat) {
    while ($r = mysqli_fetch_assoc($res_alat)) {
        $cat_slug = strtolower($r['nama_kategori']);
        $icon = 'camera';
        if (str_contains($cat_slug, 'lensa')) $icon = 'lens';
        elseif (str_contains($cat_slug, 'light')) $icon = 'bolt';
        elseif (str_contains($cat_slug, 'aksesor')) $icon = 'tools';

        $products[] = [
            'id'          => (int)$r['ID_alat'],
            'category'    => $cat_slug,
            'type'        => $r['nama_kategori'],
            'name'        => $r['nama_alat'],
            'specs'       => $r['deskripsi'] ?: 'Peralatan fotografi & videografi berkualitas',
            'price'       => (int)$r['harga_per_hari'],
            'stock'       => (int)$r['stok_total'],
            'rating'      => '4.9',
            'icon'        => $icon,
            'label'       => strtoupper($r['nama_kategori']),
            'description' => $r['deskripsi'] ?: 'Peralatan fotografi & videografi berkualitas.',
            'included'    => 'Unit alat, baterai/kabel kelengkapan, dan tas pelindung.'
        ];
    }
}

// Query statistik booking penyewa saat ini
$stat_aktif = 0;
$stat_pending = 0;
$stat_selesai = 0;
if ($user_id) {
    $q_stat = mysqli_query($koneksi, "SELECT Status, COUNT(*) as jml FROM booking WHERE ID_user = $user_id GROUP BY Status");
    if ($q_stat) {
        while ($st = mysqli_fetch_assoc($q_stat)) {
            $status = strtolower($st['Status']);
            if (in_array($status, ['diambil', 'disewa'])) $stat_aktif += (int)$st['jml'];
            if (in_array($status, ['menunggu_bayar', 'menunggu_pembayaran'])) $stat_pending += (int)$st['jml'];
            if ($status === 'selesai') $stat_selesai += (int)$st['jml'];
        }
    }
}

// Query daftar pesanan milik user ini dari database
$user_orders = [];
if ($user_id) {
    $sql_user_orders = "SELECT b.*, 
                        GROUP_CONCAT(CONCAT(a.nama_alat, ' (', db.jumlah, 'x)') SEPARATOR ', ') as daftar_alat
                        FROM booking b
                        LEFT JOIN detail_booking db ON b.ID_booking = db.ID_booking
                        LEFT JOIN alat a ON db.ID_alat = a.ID_alat
                        WHERE b.ID_user = $user_id
                        GROUP BY b.ID_booking
                        ORDER BY b.ID_booking DESC";
    $res_user_orders = mysqli_query($koneksi, $sql_user_orders);
    if ($res_user_orders) {
        while ($bo = mysqli_fetch_assoc($res_user_orders)) {
            $user_orders[] = [
                'id'       => (int)$bo['ID_booking'],
                'kode'     => $bo['Kode_booking'],
                'alat'     => $bo['daftar_alat'] ?: 'Peralatan Sewa',
                'mulai'    => $bo['Tanggal_Mulai'],
                'selesai'  => $bo['Tanggal_selesai'],
                'total'    => (float)$bo['Total_bayar'],
                'jaminan'  => $bo['Jenis_Jaminan'] ?: 'KTM Asli',
                'status'   => $bo['Status'],
                'tanggal'  => [
                    'dibuat'  => $bo['Created_at'] ? date('d M Y', strtotime($bo['Created_at'])) : '-',
                    'bayar'   => $bo['Dibayar_at'] ? date('d M Y H:i', strtotime($bo['Dibayar_at'])) : null,
                    'ambil'   => $bo['Diambil_at'] ? date('d M Y H:i', strtotime($bo['Diambil_at'])) : null,
                    'kembali' => $bo['Dikembalikan_at'] ? date('d M Y H:i', strtotime($bo['Dikembalikan_at'])) : null,
                ]
            ];
        }
    }
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function icon(string $name, string $class = ''): string
{
    return '<svg class="icon ' . escape($class) . '" aria-hidden="true"><use href="#icon-' . escape($name) . '"></use></svg>';
}

function rupiah(int $value): string
{
    return 'Rp ' . number_format($value, 0, ',', '.');
}
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ff7718">
    <meta name="description" content="PelitaRental — Platform sewa kamera dan perlengkapan kreatif mahasiswa.">
    <title>Beranda Penyewa — PelitaRental</title>

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
    <script id="product-data" type="application/json"><?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <script id="user-orders-data" type="application/json"><?= json_encode($user_orders, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</head>
<body>

<!-- SVG Sprite Ikon -->
<svg xmlns="http://www.w3.org/2000/svg" class="svg-sprite" aria-hidden="true">
    <symbol id="icon-bag" viewBox="0 0 24 24"><path d="M5 7h14l1 14H4L5 7Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2M9 13h6m-3-3v6"/></symbol>
    <symbol id="icon-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
    <symbol id="icon-orders" viewBox="0 0 24 24"><path d="M5 8h14l1 13H4L5 8Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2m-8 5 3 3 5-5"/></symbol>
    <symbol id="icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
    <symbol id="icon-profile" viewBox="0 0 24 24"><circle cx="9" cy="7" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M18 5h3m-3 6h3m-3 6h3"/></symbol>
    <symbol id="icon-camera" viewBox="0 0 24 24"><path d="m8 5 1.5-2h5L16 5h4a2 2 0 0 1 2 2v12H2V7a2 2 0 0 1 2-2h4Z"/><circle cx="12" cy="12" r="4"/><path d="M5 8h.01"/></symbol>
    <symbol id="icon-lens" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></symbol>
    <symbol id="icon-bolt" viewBox="0 0 24 24"><path d="m14 2-10 12h7l-1 8L20 9h-7l1-7Z"/></symbol>
    <symbol id="icon-video" viewBox="0 0 24 24"><rect x="2" y="10" width="13" height="12" rx="2"/><path d="m15 14 7-4v12l-7-4"/><circle cx="5.5" cy="5.5" r="3.5"/><circle cx="13" cy="5.5" r="3.5"/></symbol>
    <symbol id="icon-tools" viewBox="0 0 24 24"><path d="m14.5 6 3.5 3.5m-6 4-8 8-2-2 8-8M14 3a6 6 0 0 0 7 7l-4-4 1-3a6 6 0 0 0-4 0ZM3 3l4 2 1 3 13 13-2 1L6 9 3 8V3Z"/></symbol>
    <symbol id="icon-search" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="7"/><path d="m16 16 5 5"/></symbol>
    <symbol id="icon-cart" viewBox="0 0 24 24"><path d="M2 3h3l3 12h11l3-9H6M8 15l-1 3h13"/><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/></symbol>
    <symbol id="icon-moon" viewBox="0 0 24 24"><path d="M20.5 14.5A9 9 0 0 1 9.5 3.5a9 9 0 1 0 11 11Z"/><path d="M18 2v4m-2-2h4"/></symbol>
    <symbol id="icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/></symbol>
    <symbol id="icon-chevron" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></symbol>
    <symbol id="icon-arrow" viewBox="0 0 24 24"><path d="M4 12h16m-6-6 6 6-6 6"/></symbol>
    <symbol id="icon-check" viewBox="0 0 24 24"><path d="m7 12 3 3 7-7"/><path d="m12 2 3 2 4 1 1 4 2 3-2 3-1 4-4 1-3 2-3-2-4-1-1-4-2-3 2-3 1-4 4-1 3-2Z"/></symbol>
    <symbol id="icon-star" viewBox="0 0 24 24"><path d="m12 2 3 6.5 7 1-5 5 1 7-6-3.5L6 21l1-6.5-5-5 7-1L12 2Z"/></symbol>
    <symbol id="icon-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
    <symbol id="icon-close" viewBox="0 0 24 24"><path d="m6 6 12 12M6 18 18 6"/></symbol>
    <symbol id="icon-trash" viewBox="0 0 24 24"><path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/></symbol>
    <symbol id="icon-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v.01"/></symbol>
    <symbol id="icon-print" viewBox="0 0 24 24"><path d="M6 8V2h12v6M6 17H3V8h18v9h-3M6 14h12v8H6v-8Zm11-3h1"/></symbol>
</svg>

<a class="skip-link" href="#main-content">Lewati ke konten utama</a>

<!-- Sidebar Navigasi Penyewa -->
<aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Navigasi utama">
    <a class="brand" href="dashboard-user.php" aria-label="PelitaRental Beranda">
        <span class="brand-mark"><?= icon('bag') ?></span>
        <span class="brand-copy">
            <strong>PelitaRental</strong>
            <small>Sewa Gear Mahasiswa</small>
        </span>
    </a>
    <nav class="sidebar-nav">
        <p class="nav-label">Menu Utama</p>
        <button class="nav-item is-active" type="button" data-view="home" aria-current="page">
            <?= icon('grid') ?><span>Beranda &amp; Katalog</span><span class="active-dot"></span>
        </button>
        <button class="nav-item" type="button" data-view="orders">
            <?= icon('orders') ?><span>Pesanan Saya</span>
        </button>
        <button class="nav-item" type="button" data-view="history">
            <?= icon('clock') ?><span>Riwayat Sewa</span>
        </button>
        <button class="nav-item" type="button" data-view="profile">
            <?= icon('profile') ?><span>Profil Saya</span>
        </button>

        <p class="nav-label category-nav-label">Kategori Alat</p>
        <?php foreach ($categories as $category): ?>
            <button class="nav-item category-nav" type="button" data-category="<?= escape($category['id']) ?>">
                <?= icon($category['icon']) ?><span><?= escape($category['name']) ?></span>
            </button>
        <?php endforeach; ?>
    </nav>
    <button class="sidebar-close icon-button d-lg-none" type="button" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Tutup menu">
        <?= icon('close') ?>
    </button>
</aside>

<!-- Workspace / Area Kerja Utama -->
<div class="workspace">
    <!-- Topbar Header -->
    <header class="topbar">
        <button class="icon-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Buka menu">
            <?= icon('menu') ?>
        </button>
        <div class="search-wrap">
            <?= icon('search') ?>
            <input type="search" id="product-search" placeholder="Cari produk (contoh: Sony A7 III, Godox, Lensa 35mm)..." aria-label="Cari produk" autocomplete="off">
            <kbd class="search-shortcut">Ctrl K</kbd>
        </div>
        <div class="header-actions">
            <button class="icon-button" type="button" id="theme-toggle" aria-label="Ganti mode tema" title="Ganti tema">
                <?= icon('moon') ?>
            </button>
            <button class="icon-button cart-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#cart-panel" aria-label="Buka keranjang sewa">
                <?= icon('cart') ?><span id="cart-count">0</span>
            </button>
            <span class="header-divider"></span>
            <div class="dropdown">
                <button class="user-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                    <span class="avatar" id="header-initials"><?= escape($user_initials) ?></span>
                    <span class="user-copy">
                        <strong id="header-name"><?= escape($user_name) ?></strong>
                        <small><?= ucfirst(escape($user_role)) ?></small>
                    </span>
                    <?= icon('chevron', 'user-chevron') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-header">Akun <?= $user_role === 'admin' ? 'Admin' : 'Penyewa' ?></span></li>
                    <?php if ($user_role === 'admin'): ?>
                        <li><a class="dropdown-item text-primary fw-semibold" href="dashboard-admin.php">Panel Admin</a></li>
                    <?php endif; ?>
                    <li><button class="dropdown-item" type="button" data-view="profile">Profil saya</button></li>
                    <li><button class="dropdown-item" type="button" data-view="orders">Pesanan saya</button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Keluar</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Konten Halaman (Single-Page Views) -->
    <main class="main-content" id="main-content" tabindex="-1">
        <!-- 1. VIEW BERANDA & KATALOG -->
        <section id="view-home" class="page-view" aria-labelledby="welcome-heading">
            <div class="page-heading">
                <h1 id="welcome-heading">Selamat Datang, <span id="welcome-name"><?= escape(explode(' ', $user_name)[0]) ?></span></h1>
                <p>Kelola sewa peralatan foto &amp; video di Yogyakarta. Pembayaran &amp; jaminan fisik dilakukan di toko.</p>
            </div>

            <!-- Kartu Statistik -->
            <div class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top">
                        <h2>Booking Aktif</h2>
                        <span class="stat-icon blue"><?= icon('camera') ?></span>
                    </div>
                    <strong class="stat-value"><?= $stat_aktif ?></strong>
                    <p>Unit sedang dalam masa sewa</p>
                </article>
                <article class="stat-card">
                    <div class="stat-top">
                        <h2>Pending Bayar</h2>
                        <span class="stat-icon amber"><?= icon('clock') ?></span>
                    </div>
                    <strong class="stat-value"><?= $stat_pending ?></strong>
                    <p>Batas ambil 24 jam · Bayar di toko</p>
                </article>
                <article class="stat-card">
                    <div class="stat-top">
                        <h2>Transaksi Selesai</h2>
                        <span class="stat-icon green"><?= icon('check') ?></span>
                    </div>
                    <strong class="stat-value"><?= $stat_selesai ?></strong>
                    <p>Riwayat sewa sukses</p>
                </article>
            </div>

            <!-- Katalog Peralatan -->
            <section class="products-section" aria-labelledby="products-heading">
                <div class="section-heading">
                    <div class="section-title">
                        <h2 id="products-heading">Katalog Peralatan</h2>
                        <span class="section-note" id="product-caption">Paling sering disewa mahasiswa</span>
                    </div>
                    <button class="text-button" type="button" id="show-all">Lihat Semua <?= icon('arrow') ?></button>
                </div>

                <div class="catalog-toolbar">
                    <div class="category-tabs" role="group" aria-label="Filter kategori">
                        <button class="category-tab is-active" type="button" data-category="" aria-pressed="true">
                            <?= icon('grid') ?><span>Semua Alat</span><span class="tab-count"><?= count($products) ?></span>
                        </button>
                        <?php foreach ($categories as $category): ?>
                            <button class="category-tab" type="button" data-category="<?= escape($category['id']) ?>" aria-pressed="false">
                                <?= icon($category['icon']) ?><span><?= escape($category['name']) ?></span>
                                <span class="tab-count"><?= count(array_filter($products, fn($p) => $p['category'] === $category['id'])) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <span class="catalog-count" id="result-count" role="status"><?= count($products) ?> alat</span>
                </div>

                <div class="products-grid" id="products-grid">
                    <?php foreach ($products as $product): ?>
                        <?php $status = $product['stock'] === 0 ? 'empty' : ($product['stock'] <= 2 ? 'limited' : 'available'); ?>
                        <article class="product-card" data-product-id="<?= $product['id'] ?>">
                            <div class="product-visual">
                                <span class="stock-badge <?= $status ?>">
                                    <?= $status === 'empty' ? 'Habis' : ($status === 'limited' ? 'Stok Terbatas' : 'Tersedia') ?>
                                </span>
                                <div class="product-placeholder">
                                    <?= icon($product['icon']) ?><span><?= escape($product['label']) ?></span>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="product-type"><?= escape($product['type']) ?></span>
                                <h3><?= escape($product['name']) ?></h3>
                                <p class="product-specs"><?= escape($product['specs']) ?></p>
                                <div class="product-price-row">
                                    <p><strong><?= rupiah($product['price']) ?></strong><span>/hari</span></p>
                                    <span class="rating"><?= icon('star') ?><?= escape($product['rating']) ?></span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-outline-secondary" type="button" data-detail="<?= $product['id'] ?>">Lihat Detail</button>
                                    <button class="btn btn-primary" type="button" data-add="<?= $product['id'] ?>" <?= $product['stock'] === 0 ? 'disabled' : '' ?>>Booking</button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="empty-state" id="catalog-empty" hidden>
                    <span class="empty-icon"><?= icon('search') ?></span>
                    <h3>Belum ada alat yang cocok</h3>
                    <p>Coba kata kunci lain atau pilih kategori yang tersedia.</p>
                    <button class="btn btn-primary" type="button" data-reset-catalog>Tampilkan semua alat</button>
                </div>
            </section>
        </section>

        <!-- 2. VIEW PESANAN SAYA & TRACKING -->
        <section id="view-orders" class="page-view" hidden aria-labelledby="orders-heading">
            <div class="page-heading">
                <h1 id="orders-heading">Pesanan Saya &amp; Tracking Sewa</h1>
                <p>Pantau alur sewa, batas pembayaran 24 jam di toko, dan sisa durasi peminjaman alat.</p>
            </div>
            <div id="orders-list"></div>
        </section>

        <!-- 3. VIEW RIWAYAT SEWA -->
        <section id="view-history" class="page-view" hidden aria-labelledby="history-heading">
            <div class="page-heading">
                <h1 id="history-heading">Riwayat Sewa Selesai</h1>
                <p>Seluruh riwayat transaksi peminjaman alat yang telah dikembalikan.</p>
            </div>
            <div id="history-list"></div>
        </section>

        <!-- 4. VIEW PROFIL SAYA -->
        <section id="view-profile" class="page-view" hidden aria-labelledby="profile-heading">
            <div class="page-heading">
                <h1 id="profile-heading">Profil Saya</h1>
                <p>Kelola data pribadi untuk verifikasi identitas dan operasional sewa.</p>
            </div>
            <form class="profile-card" id="profile-form" action="proses_profil.php" method="POST">
                <div class="profile-intro">
                    <span class="avatar avatar-large" id="profile-initials"><?= escape($user_initials) ?></span>
                    <div>
                        <h2 class="mb-1" id="profile-display-name"><?= escape($user_name) ?></h2>
                        <p class="text-muted mb-0" id="profile-role-badge">
                            <?= $user_role === 'admin' ? 'Akun Admin · Petugas Toko' : 'Akun Penyewa · Mahasiswa Yogyakarta' ?>
                        </p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="profile-name">Nama Lengkap</label>
                    <input class="form-control" id="profile-name" name="name" required minlength="2" value="<?= escape($user_name) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="profile-email">Alamat Email</label>
                    <input class="form-control" id="profile-email" name="email" type="email" required value="<?= escape($user_email) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="profile-phone">Nomor HP / WhatsApp</label>
                    <input class="form-control" id="profile-phone" name="phone" type="tel" value="<?= escape($user_phone) ?>" placeholder="Contoh: 081234567890">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="profile-password">Password Baru (opsional)</label>
                    <input class="form-control" id="profile-password" name="password" type="password" minlength="6" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>
                <p class="text-muted small mb-3">Pastikan nama sesuai dengan kartu identitas (KTP/KTM) yang akan ditinggalkan sebagai jaminan di toko.</p>
                <button class="btn btn-primary" type="submit" id="btn-save-profile">Simpan Perubahan</button>
                <p class="profile-feedback mt-2" id="profile-feedback" role="status"></p>
            </form>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-links">
                <span>&copy; <?= date('Y') ?> PelitaRental Studio</span>
                <span>·</span>
                <button type="button" data-bs-toggle="modal" data-bs-target="#terms-modal">Syarat &amp; Ketentuan Sewa</button>
                <span>·</span>
                <span>Yogyakarta, Indonesia</span>
            </div>
            <div class="system-status">
                <span></span>Sistem Aktif &amp; Terverifikasi
            </div>
        </footer>
    </main>
</div>

<!-- Modal Detail Alat -->
<div class="modal fade" id="detail-modal" tabindex="-1" aria-labelledby="detail-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="eyebrow" id="detail-type"></span>
                    <h2 class="modal-title" id="detail-title">Detail Alat</h2>
                </div>
                <button class="icon-button" type="button" data-bs-dismiss="modal" aria-label="Tutup"><?= icon('close') ?></button>
            </div>
            <div class="modal-body">
                <div class="detail-visual" id="detail-visual"></div>
                <p class="detail-specs" id="detail-specs"></p>
                <p id="detail-description"></p>
                <h3 class="small-heading">Kelengkapan Paket</h3>
                <p id="detail-included"></p>
                <div class="detail-bottom">
                    <strong id="detail-price"></strong>
                    <span id="detail-stock"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-primary" type="button" id="detail-add"><?= icon('cart') ?> Tambah ke Keranjang</button>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas Keranjang Sewa & Estimasi Biaya -->
<div class="offcanvas offcanvas-end cart-panel" tabindex="-1" id="cart-panel" aria-labelledby="cart-heading">
    <div class="offcanvas-header">
        <div>
            <span class="eyebrow">Sewa Gear Mahasiswa</span>
            <h2 id="cart-heading">Keranjang Sewa</h2>
        </div>
        <button class="icon-button" type="button" data-bs-dismiss="offcanvas" aria-label="Tutup keranjang"><?= icon('close') ?></button>
    </div>
    <div class="offcanvas-body">
        <div id="cart-empty" class="empty-state">
            <span class="empty-icon"><?= icon('cart') ?></span>
            <h3>Keranjang masih kosong</h3>
            <p>Pilih perlengkapan kamera dan gear yang ingin disewa.</p>
            <button class="btn btn-primary" type="button" data-bs-dismiss="offcanvas">Jelajahi Katalog</button>
        </div>
        <div id="cart-content" hidden>
            <div id="cart-items"></div>
            <form id="rental-form">
                <div class="rental-dates">
                    <div>
                        <label class="form-label" for="start-date">Tanggal Mulai</label>
                        <input class="form-control" type="date" id="start-date" required>
                    </div>
                    <div>
                        <label class="form-label" for="end-date">Tanggal Selesai</label>
                        <input class="form-control" type="date" id="end-date" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label" for="rental-guarantee">Jaminan Fisik Identitas</label>
                    <select class="form-select" id="rental-guarantee" required>
                        <option value="KTM Asli">KTM Asli (Kartu Tanda Mahasiswa)</option>
                        <option value="KTP Asli">KTP Asli</option>
                        <option value="SIM Asli">SIM Asli</option>
                    </select>
                    <small class="text-muted d-block mt-1" style="font-size: 11px;">Kartu identitas fisik asli ditinggalkan di toko saat serah terima alat.</small>
                </div>

                <p class="text-danger small mt-2" id="date-error" role="alert" hidden></p>
                <div class="rental-total">
                    <p><span>Durasi Sewa:</span><strong id="rental-duration">1 hari</strong></p>
                    <p><span>Biaya per hari:</span><strong id="daily-subtotal">Rp 0</strong></p>
                    <div><span>Estimasi Total:</span><strong id="rental-total">Rp 0</strong></div>
                </div>
                <div class="notice">
                    <?= icon('info') ?>
                    <p>Bawa kode booking dan jaminan fisik asli ke toko. Booking hangus bila tidak dibayar dalam 24 jam.</p>
                </div>
                <button class="btn btn-primary w-100" id="print-summary" type="submit"><?= icon('check') ?> Booking &amp; Cetak Bukti</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Syarat & Ketentuan Sesuai PRD -->
<div class="modal fade" id="terms-modal" tabindex="-1" aria-labelledby="terms-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="terms-title">Syarat &amp; Ketentuan Sewa PelitaRental</h2>
                <button class="icon-button" type="button" data-bs-dismiss="modal"><?= icon('close') ?></button>
            </div>
            <div class="modal-body">
                <ol class="ps-3 mb-0">
                    <li class="mb-2">Durasi sewa dihitung: <strong>(tanggal selesai − tanggal mulai) + 1 hari</strong>.</li>
                    <li class="mb-2">Total biaya = jumlah (harga per hari × jumlah unit × durasi).</li>
                    <li class="mb-2">Penyewa wajib meninggalkan <strong>kartu identitas asli (KTP/KTM)</strong> sebagai jaminan di toko.</li>
                    <li class="mb-2">Batas pembayaran di toko adalah <strong>24 jam</strong> sejak booking dibuat; lewat dari itu booking hangus otomatis.</li>
                    <li>Stok barang kembali otomatis setelah admin mengonfirmasi pengembalian alat dan menyerahkan kembali kartu jaminan.</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifikasi -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="feedback-toast" class="toast" role="status" aria-live="polite">
        <div class="toast-body">
            <span class="toast-icon"><?= icon('check') ?></span>
            <span id="toast-message"></span>
            <button class="icon-button ms-auto border-0" type="button" data-bs-dismiss="toast"><?= icon('close') ?></button>
        </div>
    </div>
</div>

<section id="print-sheet" aria-label="Estimasi sewa cetak"></section>

<!-- Bootstrap Bundle JS & Skrip Dashboard -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/dashboard-user.js"></script>
</body>
</html>
