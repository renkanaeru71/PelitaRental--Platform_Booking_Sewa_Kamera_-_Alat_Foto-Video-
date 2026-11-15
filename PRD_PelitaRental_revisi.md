# PRD PelitaRental (Revisi)

Revisi: 15 Nov 2026

## Latar Belakang dan Tujuan

Banyak mahasiswa membutuhkan kamera dan peralatan foto/video untuk tugas atau kegiatan kampus, tetapi sulit mengecek ketersediaannya. PelitaRental adalah platform booking berbasis web untuk menyewa alat tersebut.

Tujuan projek:

- Penyewa bisa mengecek stok dan memesan alat untuk rentang tanggal tertentu.
- Admin bisa mengelola alat, memverifikasi pembayaran, dan mencatat pengambilan serta pengembalian.
- Stok tercatat dengan benar: jumlah unit yang dibooking pada tanggal yang sama tidak boleh melebihi stok alat.

## Pengguna

- **Penyewa (mahasiswa atau umum):** mendaftar, memesan alat, dan melihat riwayat sewa.
- **Admin (pengelola toko):** mengelola data alat dan kategori, memverifikasi pembayaran, mencatat pengambilan dan pengembalian, serta melihat laporan. Akun admin dibuat manual lewat database, tidak bisa didaftarkan dari form registrasi.

## Fitur Penyewa

1. **Akun:** registrasi (nama, email, nomor HP, password), login dengan email dan password, logout, dan edit profil (nama, email, nomor HP, password baru bersifat opsional). Login wajib sebelum booking. Katalog bisa dilihat tanpa login. Form login punya pilihan "Ingat email saya" yang menyimpan email di cookie.
2. **Katalog (tanpa login):** mencari alat berdasarkan nama, memfilter berdasarkan kategori (Kamera, Lensa, Lighting, Aksesori), dan melihat halaman detail alat berisi deskripsi, harga per hari, dan stok total.
3. **Keranjang dan booking:**
   - Keranjang disimpan di session. Penyewa bisa menambah alat, mengubah jumlah unit, dan menghapus alat dari keranjang.
   - Di halaman keranjang, penyewa memilih tanggal mulai dan tanggal selesai. Sistem menampilkan durasi, total bayar, dan sisa stok setiap alat pada rentang tanggal tersebut.
   - Penyewa memilih jenis jaminan identitas (KTM, KTP, atau SIM), lalu membuat booking.
   - Setelah booking dibuat, sistem menampilkan halaman **Bukti Booking** berisi kode booking, daftar alat, periode sewa, total bayar, jenis jaminan, dan batas bayar. Halaman ini bisa dicetak lewat tombol Cetak. Bukti ini dibawa ke toko.
4. **Tracking:** halaman riwayat sewa berisi semua booking milik penyewa beserta statusnya. Untuk booking yang sedang disewa (status diambil), ditampilkan sisa hari sewa.

## Fitur Admin

1. **Dashboard:** statistik berikut.
   - Booking aktif: jumlah booking berstatus dibayar atau diambil.
   - Pending bayar: jumlah booking berstatus menunggu_bayar. Batas bayar 1 hari, lewat dari itu status otomatis menjadi gagal.
   - Jadwal hari ini: jumlah booking yang harus diambil hari ini (status dibayar, tanggal mulai hari ini) dan yang harus dikembalikan hari ini (status diambil, tanggal selesai hari ini).
   - Total pendapatan: jumlah total bayar semua booking berstatus dibayar, diambil, dan selesai.
2. **Data alat dan kategori (CRUD):** tambah, lihat, ubah, dan hapus alat dan kategori, termasuk harga dan stok. Kategori yang masih dipakai alat dan alat yang pernah dibooking tidak bisa dihapus, sistem menampilkan pesan penolakan.
3. **Verifikasi pembayaran:** admin melihat daftar booking menunggu_bayar, memeriksa kode booking, nama penyewa, alat yang disewa, dan jenis jaminan, lalu menandai booking sudah dibayar.
4. **Operasional:** admin mengonfirmasi pengambilan (status dibayar menjadi diambil) dan pengembalian (status diambil menjadi selesai). Setelah pengembalian dikonfirmasi, booking tidak lagi dihitung sebagai pemakai stok sehingga unit otomatis tersedia kembali.
5. **Laporan:** tabel riwayat seluruh transaksi dan ekspor ke CSV (file CSV bisa dibuka di Excel).

## Aturan Bisnis dan Perhitungan Biaya

1. Tanggal mulai tidak boleh sebelum hari ini, dan tanggal selesai harus sama atau setelah tanggal mulai.
2. Durasi = (tanggal selesai − tanggal mulai) + 1 hari.
3. Total = jumlah (harga per hari × jumlah unit × durasi).
4. Dua booking bentrok jika tanggal mulai A ≤ tanggal selesai B dan tanggal selesai A ≥ tanggal mulai B.
5. Booking aktif adalah booking berstatus menunggu_bayar, dibayar, atau diambil. Booking berstatus selesai atau gagal tidak dihitung.
6. Stok sisa pada suatu rentang tanggal = stok total alat dikurangi jumlah unit pada booking aktif yang bentrok dengan rentang tersebut. Kolom stok total di tabel alat hanya diubah oleh admin lewat edit alat, tidak berubah otomatis saat booking atau pengembalian.
7. Penyewa harus meninggalkan jaminan berupa kartu identitas asli (KTM, KTP, atau SIM) yang disimpan di toko. Admin melihat jenis jaminan di daftar verifikasi dan operasional, lalu mengembalikan kartu saat alat kembali.
8. Batas bayar adalah 24 jam sejak booking dibuat. Booking menunggu_bayar yang melewati batas diubah menjadi gagal setiap kali halaman tertentu dibuka (tanpa cron).

### Status booking

| Dari | Ke | Pemicu |
|---|---|---|
| (baru) | menunggu_bayar | Penyewa membuat booking |
| menunggu_bayar | dibayar | Admin verifikasi pembayaran di toko |
| menunggu_bayar | gagal | Lewat batas bayar 24 jam |
| dibayar | diambil | Admin konfirmasi pengambilan, jaminan diterima |
| diambil | selesai | Admin konfirmasi pengembalian, jaminan dikembalikan |

## Alur Sewa

1. Penyewa login, membuka katalog, lalu memasukkan alat ke keranjang.
2. Di halaman keranjang, penyewa memilih tanggal mulai dan selesai. Sistem menghitung durasi, total, dan mengecek sisa stok pada tanggal tersebut.
3. Penyewa memilih jenis jaminan lalu membuat booking. Sistem mengecek stok sekali lagi di server sebelum menyimpan.
4. Penyewa mendapatkan halaman Bukti Booking (kode booking dan total bayar), lalu membawanya ke toko dalam 24 jam. Lewat dari itu booking gagal.
5. Penyewa membayar di toko, admin memverifikasi pembayaran.
6. Admin mengonfirmasi pengambilan alat dan menyimpan kartu jaminan penyewa.
7. Saat alat dikembalikan, admin mengonfirmasi pengembalian dan mengembalikan kartu jaminan. Booking selesai dan unit tersedia kembali.

## Kebutuhan Teknis

- **Bahasa dan teknologi:** HTML, CSS, JavaScript, PHP, dan Bootstrap 5.3 (lewat CDN).
- **Database:** MySQL, dijalankan lewat XAMPP. Tabel: user, kategori, alat, booking, detail_booking.
- **Struktur aplikasi:** multi halaman. Setiap halaman adalah file PHP sendiri, form dikirim dengan method POST lalu halaman dimuat ulang. Bagian yang berulang (koneksi, navbar, footer, pengecekan login) dipisah dengan `include`.
- **Keamanan dasar (Modul 7):**
  - Password disimpan dengan `password_hash()` dan dicek dengan `password_verify()`. Tidak ada password teks biasa di database.
  - Semua query yang memakai input pengguna memakai prepared statements gaya MySQLi berorientasi objek.
  - Halaman penyewa dicek dengan session login. Halaman admin dicek dengan session login dan role admin.
  - Validasi dilakukan di JavaScript dan diulang di PHP (tanggal, jumlah unit, jenis jaminan hanya KTM, KTP, atau SIM).
  - Teks dari database yang ditampilkan dibungkus `htmlspecialchars()`.
- **Konsistensi data:** pengecekan stok dilakukan di server tepat sebelum booking disimpan. Urutan simpan: cek stok semua alat, simpan data booking, lalu simpan detail booking.
- **Tampilan:** Bootstrap (navbar, grid, card, table, form, badge, alert) dengan satu file CSS kecil untuk penyesuaian. Website punya mode terang dan mode gelap lewat atribut `data-bs-theme` Bootstrap. Tombol ganti mode memanggil satu file PHP yang menyimpan pilihan di cookie (berlaku 30 hari), sehingga pilihan diingat saat pengguna kembali.

## Batasan Kode (agar sesuai Modul 1-7)

- PHP: variabel, if/else, switch, for, while, array, `include`, `session_start()`, `$_SESSION`, `setcookie()`, `$_COOKIE`, `mysqli` gaya objek dengan prepared statements, `password_hash()`, `password_verify()`, `header()`, `date()`, `strtotime()`.
- Hasil query dibaca dengan `while` (seperti Modul 5). Fungsi buatan sendiri dan `foreach` dipakai seminimal mungkin.
- JavaScript: `getElementById`, `querySelector`, `addEventListener`, `classList`, `innerHTML` atau `textContent`, `e.preventDefault()`, dan `if` untuk validasi. Dipakai hanya untuk validasi tanggal di form keranjang dan interaksi kecil.
- Tidak dipakai: `fetch`/AJAX, `localStorage`, single page app, JSON di dalam halaman, transaksi database, `GROUP_CONCAT`, `FOR UPDATE`, library tambahan.

## Struktur Halaman

| Area | File |
|---|---|
| Umum | koneksi.php, header.php, footer.php, cek_login.php, cek_admin.php, kadaluarsa.php, ganti_tema.php |
| Publik | index.php (katalog, cari, filter), detail.php, login.php, proses_login.php, register.php, proses_register.php, logout.php |
| Penyewa | keranjang.php, tambah_keranjang.php, hapus_keranjang.php, proses_booking.php, bukti_booking.php, riwayat.php, profil.php, proses_profil.php |
| Admin (folder admin/) | dashboard.php, alat.php, alat_form.php, alat_simpan.php, alat_hapus.php, kategori.php, kategori_form.php, kategori_simpan.php, kategori_hapus.php, verifikasi.php, operasional.php, proses_status.php, laporan.php, ekspor_csv.php |

## Pemetaan ke Modul

| Modul | Penerapan di PelitaRental |
|---|---|
| 1 HTML | Form registrasi, login, keranjang, tabel data admin, list |
| 2 CSS dan Bootstrap | Navbar, grid katalog, card alat, tabel, form, badge, mode gelap Bootstrap |
| 3 JavaScript | Validasi tanggal di form keranjang, event listener, manipulasi elemen |
| 4 PHP | Hitung durasi dan total, percabangan status, perulangan, array keranjang |
| 5 MySQLi | Koneksi, SELECT, INSERT, UPDATE, DELETE untuk alat, kategori, booking, dan profil |
| 6 Cookie dan Session | Session login dan keranjang, cookie mode gelap dan "ingat email" |
| 7 Authentication | Register dengan hash, login dengan prepared statements dan `password_verify()`, proteksi halaman, role admin, logout |

## Di Luar Ruang Lingkup

- Denda keterlambatan pengembalian
- Uang muka (DP) dan cicilan
- Pembayaran online otomatis (transfer otomatis, QRIS, payment gateway)
- Upload file apa pun, termasuk bukti transfer dan foto alat
- Notifikasi otomatis lewat email atau aplikasi. Notifikasi WhatsApp dikirim manual oleh admin di luar aplikasi.
- Pengembalian dana (refund) untuk booking yang dibatalkan
- Pembatalan booking oleh penyewa
- Ekspor Excel (.xlsx), hanya CSV
- Booking yang sudah dibayar tetapi tidak diambil tidak otomatis gagal, admin menanganinya manual
- Rating, ulasan, dan foto produk

## Catatan Perubahan dari Versi Sebelumnya

- Stok dihitung per rentang tanggal dari booking aktif, bukan dikurangi dan ditambah di kolom stok.
- Keranjang pindah dari localStorage ke session, mode gelap dari localStorage ke cookie PHP.
- Transaksi database dan `FOR UPDATE` dihapus dari kebutuhan teknis.
- "File booking" diganti halaman Bukti Booking yang bisa dicetak.
- CRUD kategori ditulis eksplisit, ekspor dibatasi CSV.
- Ditambahkan aturan tanggal mulai tidak boleh sebelum hari ini, definisi status booking, dan definisi tiap statistik dashboard.
- Ditambahkan batasan kode dan pemetaan ke modul untuk laporan.
