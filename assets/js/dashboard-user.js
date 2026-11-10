/**
 * PelitaRental — Dashboard Penyewa (User Dashboard)
 * Mengelola navigasi halaman, pencarian & filter alat, keranjang sewa,
 * simulasi biaya, tracking alur sewa, dan mode gelap.
 */
'use strict';

window.addEventListener('DOMContentLoaded', () => {
    // 1. Data Produk & Pesanan Awal
    const productDataElement = document.getElementById('product-data');
    const products = productDataElement ? JSON.parse(productDataElement.textContent) : [];
    const productById = new Map(products.map(p => [p.id, p]));

    // Ambil data pesanan langsung dari database berdasarkan user_id login
    const ordersDataElement = document.getElementById('user-orders-data');
    const pesananUser = ordersDataElement ? JSON.parse(ordersDataElement.textContent || '[]') : [];

    // Helper Format
    const currency = new Intl.NumberFormat('id-ID');
    const money = val => 'Rp ' + currency.format(val);
    const escapeHTML = val => String(val).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const icon = name => `<svg class="icon" aria-hidden="true"><use href="#icon-${escapeHTML(name)}"></use></svg>`;

    function localToday() {
        const d = new Date();
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }

    // 2. Elemen DOM
    const search = document.getElementById('product-search');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const cartPanel = document.getElementById('cart-panel');
    const detailModal = document.getElementById('detail-modal');
    const toastEl = document.getElementById('feedback-toast');
    const toast = toastEl ? bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3000 }) : null;

    const cart = new Map();
    let selectedCategory = '';
    let selectedProduct = null;
    let detailTrigger = null;

    function notify(message) {
        if (!toast) return;
        document.getElementById('toast-message').textContent = message;
        toast.show();
    }

    // 3. Manajemen Keranjang (LocalStorage)
    function saveCart() {
        try {
            localStorage.setItem('pelita-cart', JSON.stringify([...cart].map(([id, quantity]) => ({ id, quantity }))));
        } catch {
            notify('Penyimpanan lokal browser penuh atau dibatasi.');
        }
    }

    function loadCart() {
        try {
            const saved = JSON.parse(localStorage.getItem('pelita-cart') || '[]');
            if (Array.isArray(saved)) {
                saved.forEach(item => {
                    const prod = productById.get(item.id);
                    if (prod && prod.stock > 0 && item.quantity > 0) {
                        cart.set(item.id, Math.min(item.quantity, prod.stock));
                    }
                });
            }
        } catch {
            cart.clear();
        }
    }
    loadCart();

    // 4. Inisialisasi Tanggal Sewa
    if (startDate && endDate) {
        startDate.min = localToday();
        startDate.value = localToday();
        endDate.min = localToday();
        endDate.value = localToday();
    }

    // 5. Navigasi Halaman (View Switching)
    function showView(view) {
        const titles = { home: 'Beranda', orders: 'Pesanan Saya', history: 'Riwayat Sewa', profile: 'Profil Saya' };
        document.querySelectorAll('.page-view').forEach(s => s.hidden = (s.id !== `view-${view}`));

        document.querySelectorAll('.sidebar-nav [data-view]').forEach(btn => {
            const active = btn.dataset.view === view && !selectedCategory;
            btn.classList.toggle('is-active', active);
            if (active) btn.setAttribute('aria-current', 'page');
            else btn.removeAttribute('aria-current');
        });

        document.title = `${titles[view] || 'Dashboard'} — PelitaRental`;
        bootstrap.Offcanvas.getInstance(document.getElementById('sidebar'))?.hide();

        if (view === 'orders') renderOrders();
        if (view === 'history') renderHistory();
    }

    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedCategory = '';
            if (search) search.value = '';
            filterProducts();
            showView(btn.dataset.view);
            document.getElementById('main-content')?.focus({ preventScroll: true });
        });
    });

    // 6. Pencarian & Filter Produk
    function filterProducts() {
        const query = search ? search.value.trim().toLowerCase() : '';
        let count = 0;

        document.querySelectorAll('.product-card').forEach(card => {
            const product = productById.get(Number(card.dataset.productId));
            if (!product) return;
            const text = `${product.name} ${product.type} ${product.category} ${product.specs} ${product.label}`.toLowerCase();
            const matchesCategory = !selectedCategory || product.category === selectedCategory;
            const matchesQuery = !query || query.split(/\s+/).every(w => text.includes(w));
            const match = matchesCategory && matchesQuery;

            card.hidden = !match;
            if (match) count++;
        });

        const empty = document.getElementById('catalog-empty');
        const grid = document.getElementById('products-grid');
        const countText = document.getElementById('result-count');
        const caption = document.getElementById('product-caption');

        if (empty) empty.hidden = (count !== 0);
        if (grid) grid.hidden = (count === 0);
        if (countText) countText.textContent = `${count} alat${query ? ' ditemukan' : ''}`;
        if (caption) caption.textContent = selectedCategory ? 'Alat sesuai kategori pilihan' : 'Paling sering disewa mahasiswa';

        document.querySelectorAll('[data-category]').forEach(btn => {
            const active = btn.dataset.category === selectedCategory;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', String(active));
        });
    }

    function resetCatalog() {
        selectedCategory = '';
        if (search) search.value = '';
        filterProducts();
        showView('home');
    }

    document.querySelectorAll('[data-category]').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedCategory = btn.dataset.category;
            if (search) search.value = '';
            filterProducts();
            showView('home');
            if (btn.classList.contains('category-nav')) {
                document.getElementById('products-heading')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    search?.addEventListener('input', () => {
        filterProducts();
        showView('home');
    });

    document.getElementById('show-all')?.addEventListener('click', resetCatalog);
    document.querySelector('[data-reset-catalog]')?.addEventListener('click', resetCatalog);

    // Shortcut Keyboard Ctrl + K
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            if (document.querySelector('.modal.show, .offcanvas.show')) return;
            e.preventDefault();
            search?.focus();
            search?.select();
        }
    });

    // 7. Mode Gelap & Terang (Local Storage)
    function updateThemeButton() {
        const isDark = document.documentElement.dataset.bsTheme === 'dark';
        const toggle = document.getElementById('theme-toggle');
        if (toggle) {
            toggle.innerHTML = icon(isDark ? 'sun' : 'moon');
            toggle.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        }
    }

    document.getElementById('theme-toggle')?.addEventListener('click', () => {
        const next = document.documentElement.dataset.bsTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.bsTheme = next;
        try {
            localStorage.setItem('pelita-theme', next);
        } catch {
            notify('Tema berhasil diganti.');
        }
        updateThemeButton();
    });
    updateThemeButton();

    // 8. Hitung Estimasi Sewa Keranjang (Aturan PRD)
    function getEstimate() {
        if (!startDate || !endDate) return { days: 0, daily: 0, total: 0, error: '' };
        const start = startDate.value;
        const end = endDate.value;
        let error = '';

        if (!start || !end) error = 'Pilih tanggal mulai dan selesai terlebih dahulu.';
        else if (start < localToday()) error = 'Tanggal mulai tidak boleh sebelum hari ini.';
        else if (end < start) error = 'Tanggal selesai harus sama atau setelah tanggal mulai.';

        const days = error ? 0 : Math.round((Date.parse(`${end}T00:00:00Z`) - Date.parse(`${start}T00:00:00Z`)) / 86400000) + 1;
        const daily = [...cart].reduce((sum, [id, qty]) => sum + (productById.get(id)?.price || 0) * qty, 0);
        return { days, daily, total: days * daily, error };
    }

    function updateEstimate() {
        const { days, daily, total, error } = getEstimate();
        const errEl = document.getElementById('date-error');
        if (errEl) {
            errEl.textContent = error;
            errEl.hidden = !error;
        }

        const durEl = document.getElementById('rental-duration');
        const dayEl = document.getElementById('daily-subtotal');
        const totEl = document.getElementById('rental-total');
        const printBtn = document.getElementById('print-summary');

        if (durEl) durEl.textContent = error ? '—' : `${days} hari`;
        if (dayEl) dayEl.textContent = money(daily);
        if (totEl) totEl.textContent = error ? '—' : money(total);
        if (printBtn) printBtn.disabled = Boolean(error) || cart.size === 0;
    }

    startDate?.addEventListener('input', () => {
        if (endDate && startDate.value >= localToday()) endDate.min = startDate.value;
        updateEstimate();
    });
    endDate?.addEventListener('input', updateEstimate);

    // 9. Render Keranjang
    function renderCart() {
        const totalQty = [...cart.values()].reduce((sum, q) => sum + q, 0);
        const countBadge = document.getElementById('cart-count');
        if (countBadge) countBadge.textContent = totalQty;

        const cartEmpty = document.getElementById('cart-empty');
        const cartContent = document.getElementById('cart-content');
        if (cartEmpty) cartEmpty.hidden = (cart.size > 0);
        if (cartContent) cartContent.hidden = (cart.size === 0);

        const cartItems = document.getElementById('cart-items');
        if (cartItems) {
            cartItems.innerHTML = [...cart].map(([id, qty]) => {
                const prod = productById.get(id);
                if (!prod) return '';
                return `
                <article class="cart-item">
                    <span class="cart-item-visual">${icon(prod.icon)}</span>
                    <div class="cart-item-info">
                        <h3>${escapeHTML(prod.name)}</h3>
                        <p>${money(prod.price)} / hari</p>
                        <div class="quantity-control" aria-label="Jumlah ${escapeHTML(prod.name)}">
                            <button type="button" data-quantity="${id}" data-delta="-1" ${qty <= 1 ? 'disabled' : ''}>−</button>
                            <span>${qty}</span>
                            <button type="button" data-quantity="${id}" data-delta="1" ${qty >= prod.stock ? 'disabled' : ''}>+</button>
                        </div>
                    </div>
                    <button class="icon-button remove-item" type="button" data-remove="${id}" aria-label="Hapus">${icon('trash')}</button>
                </article>`;
            }).join('');
        }
        updateEstimate();
    }

    function addToCart(id) {
        const prod = productById.get(id);
        if (!prod || prod.stock <= 0) return false;
        const current = cart.get(id) || 0;
        if (current >= prod.stock) {
            notify(`Stok ${prod.name} terbatas (${prod.stock} unit).`);
            return false;
        }
        cart.set(id, current + 1);
        saveCart();
        renderCart();
        notify(`${prod.name} ditambahkan ke keranjang.`);
        return true;
    }

    document.querySelectorAll('[data-add]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = Number(btn.dataset.add);
            if (addToCart(id) && cartPanel) {
                bootstrap.Offcanvas.getOrCreateInstance(cartPanel).show();
            }
        });
    });

    document.getElementById('cart-items')?.addEventListener('click', e => {
        const qtyBtn = e.target.closest('[data-quantity]');
        const rmBtn = e.target.closest('[data-remove]');

        if (qtyBtn) {
            const id = Number(qtyBtn.dataset.quantity);
            const delta = Number(qtyBtn.dataset.delta);
            const next = (cart.get(id) || 0) + delta;
            const max = productById.get(id)?.stock || 1;
            if (next >= 1 && next <= max) {
                cart.set(id, next);
                saveCart();
                renderCart();
            }
        }
        if (rmBtn) {
            const id = Number(rmBtn.dataset.remove);
            cart.delete(id);
            saveCart();
            renderCart();
        }
    });

    // 10. Modal Detail Alat
    document.querySelectorAll('[data-detail]').forEach(btn => {
        btn.addEventListener('click', () => {
            detailTrigger = btn;
            selectedProduct = productById.get(Number(btn.dataset.detail));
            if (!selectedProduct) return;

            document.getElementById('detail-type').textContent = selectedProduct.type;
            document.getElementById('detail-title').textContent = selectedProduct.name;
            document.getElementById('detail-visual').innerHTML = icon(selectedProduct.icon);
            document.getElementById('detail-specs').textContent = selectedProduct.specs;
            document.getElementById('detail-description').textContent = selectedProduct.description;
            document.getElementById('detail-included').textContent = selectedProduct.included;
            document.getElementById('detail-price').textContent = `${money(selectedProduct.price)} / hari`;
            document.getElementById('detail-stock').textContent = selectedProduct.stock ? `${selectedProduct.stock} unit tersedia` : 'Stok sedang habis';

            const addBtn = document.getElementById('detail-add');
            if (addBtn) {
                addBtn.disabled = (selectedProduct.stock === 0);
                addBtn.innerHTML = selectedProduct.stock ? `${icon('cart')} Tambah ke Keranjang` : 'Stok Habis';
            }
            if (detailModal) bootstrap.Modal.getOrCreateInstance(detailModal).show();
        });
    });

    document.getElementById('detail-add')?.addEventListener('click', () => {
        if (!selectedProduct || !addToCart(selectedProduct.id)) return;
        if (detailModal) bootstrap.Modal.getInstance(detailModal)?.hide();
        if (cartPanel) bootstrap.Offcanvas.getOrCreateInstance(cartPanel).show();
    });

    // 11. Tracking & Daftar Pesanan Penyewa (PRD Fitur 4)
    function renderOrders() {
        const activeOrders = pesananUser.filter(p => p.status !== 'selesai' && p.status !== 'dibatalkan');
        const container = document.getElementById('orders-list');
        if (!container) return;

        if (activeOrders.length === 0) {
            container.innerHTML = `
                <div class="empty-state page-empty">
                    <span class="empty-icon">${icon('orders')}</span>
                    <h2>Belum ada pesanan aktif</h2>
                    <p>Mulai ide kreatifmu dengan peralatan yang tepat.<br>Booking alat langsung melalui katalog.</p>
                    <button class="btn btn-primary" type="button" data-view="home">Jelajahi Katalog ${icon('arrow')}</button>
                </div>`;
            container.querySelector('[data-view="home"]')?.addEventListener('click', () => showView('home'));
            return;
        }

        container.innerHTML = activeOrders.map(p => {
            const isDisewa = (p.status === 'disewa');
            const target = new Date(p.selesai);
            const today = new Date();
            target.setHours(0,0,0,0); today.setHours(0,0,0,0);
            const sisaHari = Math.ceil((target - today) / (1000 * 60 * 60 * 24));

            return `
            <div class="data-table-card mb-4">
                <div class="data-table-header">
                    <div>
                        <span class="eyebrow">${p.kode}</span>
                        <h3>${escapeHTML(p.alat)}</h3>
                    </div>
                    <span class="badge ${p.status === 'disewa' ? 'bg-primary' : 'bg-warning text-dark'} px-3 py-2">
                        ${p.status === 'disewa' ? 'Sedang Disewa' : 'Menunggu Pembayaran di Toko'}
                    </span>
                </div>
                <div class="p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-4"><small class="text-muted d-block">Periode Sewa</small><strong>${p.mulai} s/d ${p.selesai}</strong></div>
                        <div class="col-sm-4"><small class="text-muted d-block">Total Biaya</small><strong class="text-orange">${money(p.total)}</strong></div>
                        <div class="col-sm-4"><small class="text-muted d-block">Jaminan Fisik</small><strong>${escapeHTML(p.jaminan)}</strong></div>
                    </div>
                    <div class="notice">
                        ${icon('info')}
                        <p>${isDisewa ? `Sisa waktu sewa: <strong>${sisaHari} hari lagi</strong>. Harap kembalikan alat sebelum tanggal selesai untuk mengambil jaminan Anda.` : `Segera bayar di toko dalam <strong>24 jam</strong>. Bawa kode booking <strong>${p.kode}</strong> dan kartu identitas fisik sebagai jaminan.`}</p>
                    </div>
                    <h4 class="small-heading mb-3">Alur Tahapan Booking</h4>
                    <div class="timeline-step">
                        <span class="timeline-icon-box text-success">${icon('check')} 1. Booking Dibuat</span>
                        <span class="text-muted small">${p.tanggal.dibuat}</span>
                    </div>
                    <div class="timeline-step">
                        <span class="timeline-icon-box ${p.tanggal.bayar ? 'text-success' : 'text-muted'}">${icon(p.tanggal.bayar ? 'check' : 'clock')} 2. Bayar di Toko</span>
                        <span class="text-muted small">${p.tanggal.bayar || 'Menunggu Pembayaran'}</span>
                    </div>
                    <div class="timeline-step">
                        <span class="timeline-icon-box ${p.tanggal.ambil ? 'text-success' : 'text-muted'}">${icon(p.tanggal.ambil ? 'check' : 'clock')} 3. Alat Diambil & Jaminan Ditinggal</span>
                        <span class="text-muted small">${p.tanggal.ambil || 'Menunggu Pengambilan'}</span>
                    </div>
                    <div class="timeline-step">
                        <span class="timeline-icon-box text-muted">${icon('clock')} 4. Alat Dikembalikan & Jaminan Kembali</span>
                        <span class="text-muted small">Menunggu Pengembalian</span>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function renderHistory() {
        const historyOrders = pesananUser.filter(p => p.status === 'selesai' || p.status === 'dibatalkan' || p.status === 'gagal');
        const container = document.getElementById('history-list');
        if (!container) return;

        if (historyOrders.length === 0) {
            container.innerHTML = `
                <div class="empty-state page-empty">
                    <span class="empty-icon">${icon('clock')}</span>
                    <h2>Belum ada riwayat transaksi</h2>
                    <p>Transaksi sewa yang telah selesai akan tercatat otomatis di sini.</p>
                </div>`;
            return;
        }

        container.innerHTML = `
            <div class="data-table-card">
                <div class="data-table-header">
                    <h3>Riwayat Penyewaan Selesai</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Alat</th>
                                <th>Periode Sewa</th>
                                <th>Total Biaya</th>
                                <th>Jaminan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${historyOrders.map(p => `
                                <tr>
                                    <td><strong>${p.kode}</strong></td>
                                    <td>${escapeHTML(p.alat)}</td>
                                    <td>${p.mulai} - ${p.selesai}</td>
                                    <td>${money(p.total)}</td>
                                    <td>${escapeHTML(p.jaminan)}</td>
                                    <td><span class="badge ${p.status === 'selesai' ? 'bg-success' : 'bg-danger'}">${p.status === 'selesai' ? 'Selesai' : 'Gagal / Hangus'}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>`;
    }

    // 12. Form Profil
    document.getElementById('profile-form')?.addEventListener('submit', e => {
        e.preventDefault();
        const nameInput = document.getElementById('profile-name');
        const name = nameInput.value.trim();
        if (name.length < 2) return;

        const initials = name.split(/\s+/).slice(0, 2).map(w => Array.from(w)[0]).join('').toUpperCase();
        document.getElementById('header-name').textContent = name;
        document.getElementById('welcome-name').textContent = name.split(/\s+/)[0];
        document.getElementById('header-initials').textContent = initials;
        document.getElementById('profile-initials').textContent = initials;
        document.getElementById('profile-feedback').textContent = 'Profil pratinjau berhasil disimpan.';
        notify('Profil berhasil diperbarui.');
    });

    // 13. Submit Booking ke Database & Cetak Lembar Bukti
    document.getElementById('rental-form')?.addEventListener('submit', async e => {
        e.preventDefault();
        const { days, daily, total, error } = getEstimate();
        if (error || !cart.size) return;

        const guaranteeEl = document.getElementById('rental-guarantee');
        const jaminanDipilih = guaranteeEl ? guaranteeEl.value : 'KTM Asli';

        const submitBtn = document.getElementById('print-summary');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan booking...';
        }

        // Siapkan payload items
        const items = [...cart].map(([id, qty]) => ({ id, qty }));

        try {
            const res = await fetch('proses_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    start_date: startDate.value,
                    end_date: endDate.value,
                    jaminan: jaminanDipilih,
                    items: items
                })
            });

            const data = await res.json();

            if (!data.success) {
                alert(data.message || 'Gagal menyimpan booking.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Booking & Cetak Bukti';
                }
                return;
            }

            const kodeBookingBaru = data.kode_booking;

            // Cetak bukti booking
            const printSheet = document.getElementById('print-sheet');
            if (printSheet) {
                printSheet.innerHTML = `
                    <h1>PelitaRental Studio</h1>
                    <p>Sewa Kamera & Gear Kreatif Mahasiswa · Yogyakarta</p>
                    <hr>
                    <h2>Bukti Booking Sewa (#${kodeBookingBaru})</h2>
                    <p><strong>Periode:</strong> ${startDate.value} s/d ${endDate.value} (${days} hari)</p>
                    <p><strong>Jaminan Fisik Terpilih:</strong> ${escapeHTML(jaminanDipilih)}</p>
                    <table>
                        <thead>
                            <tr><th>Nama Alat</th><th>Unit</th><th>Harga / Hari</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                            ${[...cart].map(([id, qty]) => {
                                const prod = productById.get(id);
                                return `<tr><td>${escapeHTML(prod?.name || 'Alat')}</td><td>${qty}</td><td>${money(prod?.price || 0)}</td><td>${money((prod?.price || 0) * qty * days)}</td></tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                    <p>Biaya sewa harian: ${money(daily)}</p>
                    <p class="print-total">Total Bayar di Toko: ${money(total)}</p>
                    <div class="print-notice">
                        <strong>KETENTUAN PENGAMBILAN DI TOKO:</strong><br>
                        1. Bawa lembar/kode booking ini ke toko PelitaRental Yogyakarta maksimal <strong>24 jam</strong>.<br>
                        2. Serahkan jaminan fisik asli: <strong>${escapeHTML(jaminanDipilih)}</strong> kepada admin toko saat penyerahan alat.<br>
                        3. Pembayaran dilakukan langsung di toko sebelum alat diserahkan.
                    </div>`;
                window.print();
            }

            // Bersihkan keranjang dan alihkan
            cart.clear();
            saveCart();
            renderCart();
            bootstrap.Offcanvas.getInstance(document.getElementById('cart-panel'))?.hide();
            notify(`Booking berhasil dibuat! Kode: ${kodeBookingBaru}`);
            setTimeout(() => window.location.reload(), 1500);

        } catch (err) {
            alert('Terjadi kesalahan koneksi server.');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Booking & Cetak Bukti';
            }
        }
    });

    // Inisialisasi awal
    renderCart();
    renderOrders();
});
