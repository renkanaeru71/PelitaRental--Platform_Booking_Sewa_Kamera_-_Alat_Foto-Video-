<?php
session_start();
require_once 'koneksi.php';
require_once 'auth.php';
cek_login();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Sesi login tidak valid.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Data booking kosong.']);
    exit;
}

$start_date = trim($input['start_date'] ?? '');
$end_date   = trim($input['end_date'] ?? '');
$jaminan    = trim($input['jaminan'] ?? 'KTM Asli');
$items      = $input['items'] ?? []; // Array [{id: 1, qty: 1}, ...]

if (empty($start_date) || empty($end_date) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Tanggal dan barang sewa wajib diisi.']);
    exit;
}

// Hitung durasi sesuai aturan PRD: (selesai - mulai) + 1 hari
$d1 = new DateTime($start_date);
$d2 = new DateTime($end_date);
if ($d2 < $d1) {
    echo json_encode(['success' => false, 'message' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.']);
    exit;
}
$diff = $d1->diff($d2);
$durasi = $diff->days + 1;

// Mulai Database Transaction sesuai PRD Kebutuhan Teknis
mysqli_begin_transaction($koneksi);

try {
    // 1. Validasi stok alat & hitung total bayar
    $total_bayar = 0;
    $detail_insert = [];

    foreach ($items as $item) {
        $id_alat = (int)($item['id'] ?? 0);
        $qty     = (int)($item['qty'] ?? 0);

        if ($id_alat <= 0 || $qty <= 0) continue;

        // Kunci baris alat (FOR UPDATE)
        $q_alat = mysqli_query($koneksi, "SELECT * FROM alat WHERE ID_alat = $id_alat FOR UPDATE");
        $alat = mysqli_fetch_assoc($q_alat);

        if (!$alat) {
            throw new Exception("Alat dengan ID #$id_alat tidak ditemukan.");
        }

        if ($qty > $alat['stok_total']) {
            throw new Exception("Stok untuk " . $alat['nama_alat'] . " tidak mencukupi (sisa: " . $alat['stok_total'] . ").");
        }

        $subtotal = (float)$alat['harga_per_hari'] * $qty * $durasi;
        $total_bayar += $subtotal;

        $detail_insert[] = [
            'id_alat'  => $id_alat,
            'qty'      => $qty,
            'subtotal' => $subtotal
        ];

        // Kurangi stok alat secara konsisten
        $sisa_stok = $alat['stok_total'] - $qty;
        mysqli_query($koneksi, "UPDATE alat SET stok_total = $sisa_stok WHERE ID_alat = $id_alat");
    }

    if (empty($detail_insert)) {
        throw new Exception("Keranjang sewa tidak memiliki alat yang valid.");
    }

    // 2. Buat Kode Booking: PR-YYMM-XXX
    $prefix = 'PR-' . date('ym') . '-';
    $q_last = mysqli_query($koneksi, "SELECT Kode_booking FROM booking WHERE Kode_booking LIKE '$prefix%' ORDER BY ID_booking DESC LIMIT 1");
    $last_num = 1;
    if ($row_last = mysqli_fetch_assoc($q_last)) {
        $last_code = $row_last['Kode_booking'];
        $parts = explode('-', $last_code);
        $last_num = ((int)end($parts)) + 1;
    }
    $kode_booking = $prefix . str_pad((string)$last_num, 3, '0', STR_PAD_LEFT);

    // Batas bayar di toko: 24 jam dari sekarang
    $batas_bayar = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $now = date('Y-m-d H:i:s');
    $jaminan_esc = mysqli_real_escape_string($koneksi, $jaminan);

    // 3. Insert ke tabel booking
    $sql_booking = "INSERT INTO booking 
        (Kode_booking, ID_user, Tanggal_Mulai, Tanggal_selesai, Durasi, Total_bayar, Jenis_Jaminan, Status, Batas_Bayar, Created_at)
        VALUES 
        ('$kode_booking', $user_id, '$start_date', '$end_date', $durasi, $total_bayar, '$jaminan_esc', 'menunggu_bayar', '$batas_bayar', '$now')";

    if (!mysqli_query($koneksi, $sql_booking)) {
        throw new Exception("Gagal membuat data booking: " . mysqli_error($koneksi));
    }

    $id_booking = mysqli_insert_id($koneksi);

    // 4. Insert ke tabel detail_booking
    foreach ($detail_insert as $d) {
        $id_a = $d['id_alat'];
        $q   = $d['qty'];
        $sub = $d['subtotal'];
        $sql_detail = "INSERT INTO detail_booking (ID_booking, ID_alat, jumlah, subtotal)
                       VALUES ($id_booking, $id_a, $q, $sub)";
        if (!mysqli_query($koneksi, $sql_detail)) {
            throw new Exception("Gagal menyimpan detail booking: " . mysqli_error($koneksi));
        }
    }

    // Commit transaksi
    mysqli_commit($koneksi);

    echo json_encode([
        'success'      => true,
        'kode_booking' => $kode_booking,
        'id_booking'   => $id_booking,
        'total'        => $total_bayar,
        'message'      => 'Booking berhasil dibuat! Silakan bayar di toko dalam 24 jam.'
    ]);
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
