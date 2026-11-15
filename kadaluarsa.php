<?php
// booking menunggu_bayar yang lewat batas 24 jam otomatis menjadi gagal
// stok tidak perlu dikembalikan karena stok dihitung dari booking aktif
$koneksi->query("UPDATE booking SET Status = 'gagal' WHERE Status = 'menunggu_bayar' AND Batas_Bayar < NOW()");
