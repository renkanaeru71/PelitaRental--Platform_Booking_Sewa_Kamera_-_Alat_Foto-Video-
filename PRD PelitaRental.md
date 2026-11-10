# PRD PelitaRental

Nov 9, 2026

## Latar Belakang dan Tujuan

Banyak mahasiswa membutuhkan kamera dan peralatan foto/video untuk tugas atau kegiatan kampus, tetapi sulit mengecek ketersediaannya. PelitaRental adalah platform booking berbasis web untuk menyewa alat tersebut.

Tujuan projek:

- Penyewa bisa mengecek stok dan memesan alat untuk rentang tanggal tertentu.
- Admin bisa mengelola alat, memverifikasi pembayaran, dan mencatat pengambilan serta pengembalian.
- Stok tercatat dengan benar, tidak ada booking ganda pada alat yang sama.

## Pengguna

- **Penyewa (mahasiswa atau umum):** mendaftar, memesan alat, dan melihat riwayat sewa.
- **Admin (pengelola toko):** mengelola data alat, memverifikasi booking penyewa, mencatat pengambilan dan pengembalian, serta melihat laporan.

## Fitur Penyewa

1. **Akun:** registrasi, login, dan edit profil (nama, email, nomor HP, password). Login wajib sebelum booking.Katalog bisa dilihat tanpa login
2. **Katalog:** mencari alat, memfilter berdasarkan kategori (Kamera, Lensa, Lighting, Aksesori), dan melihat detail alat beserta stoknya.
3. **Booking dan keranjang:** memilih tanggal mulai dan selesai, serta menggabungkan beberapa alat dalam satu booking dan setelah booking penyewa mendapatkan kode booking dan total bayar untuk pengambilan di toko.
4. **Tracking:** melihat riwayat sewa, sisa hari sewa untuk alat yang sedang disewa

## Fitur Admin

1. **Dashboard:** statistik booking aktif, pending bayar (batas 1 hari, lewat dari itu booking otomatis gagal), jadwal ambil dan kembali hari ini, dan total pendapatan.
2. **Data alat dan kategori (CRUD):** tambah, ubah, dan hapus alat dan kategori, termasuk harga dan stok.
3. **Verifikasi pembayaran:** memeriksa kode booking, nama penyewa, dan alat yang disewa, lalu menandai booking sudah dibayar.
4. **Operasional:** mengonfirmasi pengambilan dan pengembalian barang. Stok kembali otomatis saat pengembalian dikonfirmasi.
5. **Laporan:** riwayat seluruh transaksi dan ekspor ke CSV atau Excel.

## Aturan Bisnis dan Perhitungan Biaya

1. Tanggal selesai harus sama atau setelah tanggal mulai.
2. Durasi = (tanggal selesai − tanggal mulai) + 1 hari.
3. Total = jumlah (harga per hari × jumlah unit × durasi).
4. Dua booking bentrok jika tanggal mulai A ≤ tanggal selesai B dan tanggal selesai A ≥ tanggal mulai B.
5. Stok sisa = total stok dikurangi unit yang dipesan pada booking yang bentrok dan masih aktif.
6. Stok bertambah kembali otomatis saat admin mengonfirmasi barang sudah dikembalikan.
7. Penyewa harus meninggalkan jaminan berupa indentitas. lalu di simpan di toko (identitas tersebut dilihat oleh admin agar dapat mengetahui apa jaminannya)

## Alur Sewa

1. Penyewa login, memilih alat, lalu memasukkannya ke keranjang.
2. Penyewa memilih tanggal mulai dan selesai, lalu sistem mengecek stok yang tersisa.
3. Penyewa membuat booking dan memasukkan jenis jaminan sewa (identitas).
4. Penyewa mendapatkan file booking, lalu di bawa ke toko (kalo lebih dari 24 jam hangus)
5. Penyewa membayar di toko.
6. Admin memverifikasi pengambilan alat dan mengonfirmasi jaminan, lalu mengembalikan identitas penyewa
7. Saat alat dikembalikan, admin mencatat pengembalian. Booking selesai dan stok kembali.

## Kebutuhan Teknis

- **Bahasa dan teknologi:** HTML, CSS, JavaScript, PHP, dan Bootstrap.
- **Database:** MySQL, dijalankan lewat XAMPP.
- **Keamanan dasar:** password disimpan dalam bentuk hash, dan akses halaman admin hanya untuk role admin.
- **Konsistensi data:** pengecekan stok dan penyimpanan booking dilakukan dalam satu transaksi database.
- **Tampilan:** website punya mode terang dan mode gelap yang bisa diganti lewat tombol. Pilihan pengguna diingat saat kembali ke website.

## Di Luar Ruang Lingkup

- Denda keterlambatan pengembalian
- Uang muka (DP) dan cicilan
- Pembayaran online otomatis (transfer otomatis, QRIS, payment gateway)
- Upload file apa pun, termasuk bukti transfer dan foto alat
- Notifikasi otomatis lewat email atau aplikasi. Notifikasi WhatsApp dikirim manual oleh admin di luar aplikasi.
- Pengembalian dana (refund) untuk booking yang dibatalkan
