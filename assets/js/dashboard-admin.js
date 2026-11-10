/**
 * PelitaRental — Dashboard Admin
 * Mengelola statistik admin, CRUD alat, verifikasi pembayaran di toko,
 * operasional pengambilan/pengembalian alat, dan ekspor laporan ke CSV.
 */
'use strict';

window.addEventListener('DOMContentLoaded', () => {
    // 1. Data Admin dari Database atau Fallback
    const dbAlatTag = document.getElementById('admin-db-alat');
    let dataAlat = [];
    if (dbAlatTag) {
        try {
            dataAlat = JSON.parse(dbAlatTag.textContent || '[]');
        } catch (e) {
            dataAlat = [];
        }
    }
    if (!dataAlat.length) {
        dataAlat = [
            { id: 1, kategori: 'Kamera', nama: 'Canon EOS 80D', harga: 150000, stok: 3, deskripsi: 'DSLR 24MP, cocok untuk foto dan video' },
            { id: 2, kategori: 'Kamera', nama: 'Sony A6400', harga: 200000, stok: 2, deskripsi: 'Mirrorless APS-C autofocus cepat' },
            { id: 3, kategori: 'Lensa', nama: 'Canon 50mm f/1.8 STM', harga: 50000, stok: 4, deskripsi: 'Lensa prime ringan untuk potret' },
            { id: 4, kategori: 'Lighting', nama: 'Godox SL60W', harga: 120000, stok: 2, deskripsi: 'Lampu LED continuous 60W studio' },
            { id: 5, kategori: 'Aksesori', nama: 'Tripod Velbon Sherpa', harga: 30000, stok: 4, deskripsi: 'Tripod kokoh pan-tilt head' }
        ];
    }

    const dbBookingTag = document.getElementById('admin-db-booking');
    let dataBooking = [];
    if (dbBookingTag) {
        try {
            dataBooking = JSON.parse(dbBookingTag.textContent || '[]');
        } catch (e) {
            dataBooking = [];
        }
    }

    // Helper
    const currency = new Intl.NumberFormat('id-ID');
    const money = val => 'Rp ' + currency.format(val);
    const escapeHTML = val => String(val).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const icon = name => `<svg class="icon" aria-hidden="true"><use href="#icon-${escapeHTML(name)}"></use></svg>`;

    const toastEl = document.getElementById('admin-toast');
    const toast = toastEl ? bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3000 }) : null;

    function notify(message) {
        if (!toast) return;
        document.getElementById('toast-message').textContent = message;
        toast.show();
    }

    // 2. Navigasi Antar View
    function showView(view) {
        document.querySelectorAll('.page-view').forEach(s => s.hidden = (s.id !== `view-${view}`));
        document.querySelectorAll('.sidebar-nav [data-view]').forEach(btn => {
            const active = btn.dataset.view === view;
            btn.classList.toggle('is-active', active);
        });

        if (view === 'dashboard') renderDashboardStats();
        if (view === 'alat') renderTabelAlat();
        if (view === 'verifikasi') renderTabelVerifikasi();
        if (view === 'operasional') renderTabelOperasional();
        if (view === 'laporan') renderTabelLaporan();

        bootstrap.Offcanvas.getInstance(document.getElementById('sidebar'))?.hide();
    }

    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.addEventListener('click', () => showView(btn.dataset.view));
    });

    // 3. Render Statistik Dashboard Admin (PRD Fitur Admin 1)
    function renderDashboardStats() {
        const aktif = dataBooking.filter(b => b.status === 'diambil').length;
        const pending = dataBooking.filter(b => b.status === 'menunggu_bayar').length;
        const jadwalHariIni = dataBooking.filter(b => b.status === 'dibayar' || b.status === 'diambil').length;
        const totalPendapatan = dataBooking.filter(b => b.status === 'selesai' || b.status === 'diambil' || b.status === 'dibayar').reduce((sum, b) => sum + b.total, 0);

        document.getElementById('stat-aktif').textContent = aktif;
        document.getElementById('stat-pending').textContent = pending;
        document.getElementById('stat-operasional').textContent = jadwalHariIni;
        document.getElementById('stat-pendapatan').textContent = money(totalPendapatan);

        // Tabel Cepat Pending Bayar
        const tbody = document.getElementById('admin-quick-pending');
        if (tbody) {
            const list = dataBooking.filter(b => b.status === 'menunggu_bayar');
            tbody.innerHTML = list.length ? list.map(b => `
                <tr>
                    <td><strong>${b.kode}</strong></td>
                    <td>${escapeHTML(b.penyewa)}</td>
                    <td>${escapeHTML(b.alat)}</td>
                    <td>${money(b.total)}</td>
                    <td><span class="badge bg-warning text-dark">Batas 24 Jam</span></td>
                    <td class="text-end">
                        <button class="btn btn-primary btn-sm" onclick="verifikasiBayar(${b.id})">Verifikasi</button>
                    </td>
                </tr>
            `).join('') : '<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada pending bayar saat ini.</td></tr>';
        }
    }

    // 4. Render Data Alat (CRUD - PRD Fitur Admin 2)
    function renderTabelAlat() {
        const tbody = document.getElementById('admin-tabel-alat');
        if (!tbody) return;

        tbody.innerHTML = dataAlat.map((a, i) => `
            <tr>
                <td><strong>#${a.id}</strong></td>
                <td><span class="badge bg-secondary-subtle text-dark">${escapeHTML(a.kategori)}</span></td>
                <td><strong>${escapeHTML(a.nama)}</strong><br><small class="text-muted">${escapeHTML(a.deskripsi)}</small></td>
                <td>${money(a.harga)}</td>
                <td><span class="badge ${a.stok > 0 ? 'bg-success' : 'bg-danger'}">${a.stok} Unit</span></td>
                <td class="text-end">
                    <button class="btn btn-outline-danger btn-sm" onclick="hapusAlat(${a.id})">${icon('trash')} Hapus</button>
                </td>
            </tr>
        `).join('');
    }

    window.hapusAlat = function(id) {
        if (!confirm('Hapus peralatan ini dari database?')) return;
        window.location.href = 'proses_hapus.php?id=' + id;
    };

    // Tambah Alat Baru
    document.getElementById('form-tambah-alat')?.addEventListener('submit', e => {
        e.preventDefault();
        const nama = document.getElementById('alat-nama').value.trim();
        const kategori = document.getElementById('alat-kategori').value;
        const harga = Number(document.getElementById('alat-harga').value);
        const stok = Number(document.getElementById('alat-stok').value);
        const deskripsi = document.getElementById('alat-deskripsi').value.trim();

        const idBaru = dataAlat.length ? Math.max(...dataAlat.map(a => a.id)) + 1 : 1;
        dataAlat.push({ id: idBaru, kategori, nama, harga, stok, deskripsi });

        renderTabelAlat();
        bootstrap.Modal.getInstance(document.getElementById('modal-tambah-alat'))?.hide();
        e.target.reset();
        notify('Alat baru berhasil ditambahkan.');
    });

    // 5. Verifikasi Pembayaran (PRD Fitur Admin 3)
    function renderTabelVerifikasi() {
        const tbody = document.getElementById('admin-tabel-verifikasi');
        if (!tbody) return;

        const list = dataBooking.filter(b => b.status === 'menunggu_bayar');
        tbody.innerHTML = list.length ? list.map(b => `
            <tr>
                <td><strong>${b.kode}</strong></td>
                <td>${escapeHTML(b.penyewa)}</td>
                <td>${escapeHTML(b.alat)}</td>
                <td><strong>${money(b.total)}</strong></td>
                <td><span class="badge bg-light text-dark border">${escapeHTML(b.jaminan)}</span></td>
                <td><small class="text-danger fw-bold">${b.batas_bayar}</small></td>
                <td class="text-end">
                    <button class="btn btn-primary btn-sm" onclick="verifikasiBayar(${b.id})">
                        ${icon('check')} Konfirmasi Bayar di Toko
                    </button>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="7" class="text-center text-muted py-4">Semua pembayaran sudah diverifikasi.</td></tr>';
    }

    window.verifikasiBayar = function(id) {
        fetch('proses_status_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'verifikasi_bayar', id_booking: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = dataBooking.find(b => b.id === id);
                if (item) item.status = 'dibayar';
                renderDashboardStats();
                renderTabelVerifikasi();
                notify(data.message);
            } else {
                alert(data.message);
            }
        });
    };

    // 6. Operasional Ambil & Kembali (PRD Fitur Admin 4)
    function renderTabelOperasional() {
        // Pengambilan
        const tbodyAmbil = document.getElementById('admin-tabel-ambil');
        if (tbodyAmbil) {
            const listAmbil = dataBooking.filter(b => b.status === 'dibayar');
            tbodyAmbil.innerHTML = listAmbil.length ? listAmbil.map(b => `
                <tr>
                    <td><strong>${b.kode}</strong></td>
                    <td>${escapeHTML(b.penyewa)}</td>
                    <td>${escapeHTML(b.alat)}</td>
                    <td><span class="badge bg-info text-dark">Wajib: ${escapeHTML(b.jaminan)}</span></td>
                    <td class="text-end">
                        <button class="btn btn-success btn-sm" onclick="konfirmasiAmbil(${b.id})">
                            ${icon('check')} Konfirmasi Ambil &amp; Simpan Jaminan
                        </button>
                    </td>
                </tr>
            `).join('') : '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada jadwal pengambilan barang.</td></tr>';
        }

        // Pengembalian
        const tbodyKembali = document.getElementById('admin-tabel-kembali');
        if (tbodyKembali) {
            const listKembali = dataBooking.filter(b => b.status === 'diambil');
            tbodyKembali.innerHTML = listKembali.length ? listKembali.map(b => `
                <tr>
                    <td><strong>${b.kode}</strong></td>
                    <td>${escapeHTML(b.penyewa)}</td>
                    <td>${escapeHTML(b.alat)}</td>
                    <td><span class="badge bg-warning text-dark">Kembalikan: ${escapeHTML(b.jaminan)}</span></td>
                    <td class="text-end">
                        <button class="btn btn-primary btn-sm" onclick="konfirmasiKembali(${b.id})">
                            ${icon('check')} Konfirmasi Kembali &amp; Restock
                        </button>
                    </td>
                </tr>
            `).join('') : '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada jadwal pengembalian barang.</td></tr>';
        }
    }

    window.konfirmasiAmbil = function(id) {
        fetch('proses_status_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'serah_terima', id_booking: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = dataBooking.find(b => b.id === id);
                if (item) item.status = 'diambil';
                renderTabelOperasional();
                renderDashboardStats();
                notify(data.message);
            } else {
                alert(data.message);
            }
        });
    };

    window.konfirmasiKembali = function(id) {
        fetch('proses_status_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'pengembalian', id_booking: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = dataBooking.find(b => b.id === id);
                if (item) item.status = 'selesai';
                renderTabelOperasional();
                renderDashboardStats();
                notify(data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                alert(data.message);
            }
        });
    };

    // 7. Laporan Transaksi & Ekspor CSV (PRD Fitur Admin 5)
    function renderTabelLaporan() {
        const tbody = document.getElementById('admin-tabel-laporan');
        if (!tbody) return;

        tbody.innerHTML = dataBooking.map(b => {
            const badgeClass = {
                menunggu_bayar: 'bg-warning text-dark',
                dibayar: 'bg-info text-dark',
                diambil: 'bg-primary',
                selesai: 'bg-success'
            }[b.status] || 'bg-secondary';

            return `
            <tr>
                <td><strong>${b.kode}</strong></td>
                <td>${escapeHTML(b.penyewa)}</td>
                <td>${escapeHTML(b.alat)}</td>
                <td>${b.mulai} s/d ${b.selesai}</td>
                <td>${money(b.total)}</td>
                <td><span class="badge ${badgeClass}">${b.status.toUpperCase()}</span></td>
            </tr>`;
        }).join('');
    }

    document.getElementById('btn-export-csv')?.addEventListener('click', () => {
        let csv = 'Kode Booking,Penyewa,Alat,Tanggal Mulai,Tanggal Selesai,Total Bayar,Jaminan,Status\n';
        dataBooking.forEach(b => {
            csv += `"${b.kode}","${b.penyewa}","${b.alat}","${b.mulai}","${b.selesai}","${b.total}","${b.jaminan}","${b.status}"\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `Laporan_PelitaRental_${new Date().toISOString().slice(0,10)}.csv`;
        link.click();
        notify('Laporan berhasil diekspor ke file CSV.');
    });

    // 8. Mode Tema Gelap / Terang
    const themeBtn = document.getElementById('theme-toggle');
    function updateTheme() {
        const isDark = document.documentElement.dataset.bsTheme === 'dark';
        if (themeBtn) themeBtn.innerHTML = icon(isDark ? 'sun' : 'moon');
    }
    themeBtn?.addEventListener('click', () => {
        const next = document.documentElement.dataset.bsTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.bsTheme = next;
        try { localStorage.setItem('pelita-theme', next); } catch {}
        updateTheme();
    });
    updateTheme();

    // Inisialisasi awal
    showView('dashboard');
});
