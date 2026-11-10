// Data contoh. Nanti diganti dari PHP/database.
const pesanan = [
  {
    kode: "PR-2611-006",
    alat: "Sony A7 III",
    mulai: "2026-11-12",
    selesai: "2026-11-14",
    total: 1050000,
    status: "menunggu_pembayaran",
    tanggal: { dibuat: "2026-11-09", ambil: null, kembali: null }
  },
  {
    kode: "PR-2611-004",
    alat: "Godox AD200 Pro",
    mulai: "2026-11-08",
    selesai: "2026-11-11",
    total: 600000,
    status: "disewa",
    tanggal: { dibuat: "2026-11-06", ambil: "2026-11-08", kembali: null }
  },
  {
    kode: "PR-2610-002",
    alat: "Sigma 35mm f/1.4",
    mulai: "2026-10-20",
    selesai: "2026-10-22",
    total: 360000,
    status: "selesai",
    tanggal: { dibuat: "2026-10-18", ambil: "2026-10-20", kembali: "2026-10-22" }
  },
  {
    kode: "PR-2610-001",
    alat: "Manfrotto 055",
    mulai: "2026-10-05",
    selesai: "2026-10-05",
    total: 80000,
    status: "dibatalkan",
    tanggal: { dibuat: "2026-10-04", ambil: null, kembali: null }
  }
];

// Urutan tahap untuk timeline
const tahap = [
  { key: "dibuat", label: "Booking dibuat" },
  { key: "bayar", label: "Bayar di toko" },
  { key: "ambil", label: "Alat diambil" },
  { key: "kembali", label: "Alat dikembalikan" }
];

const labelStatus = {
  menunggu_pembayaran: ["Menunggu Pembayaran", "text-bg-warning"],
  menunggu_verifikasi: ["Menunggu Verifikasi", "text-bg-info"],
  disewa: ["Disewa", "text-bg-primary"],
  selesai: ["Selesai", "text-bg-success"],
  ditolak: ["Ditolak", "text-bg-danger"],
  dibatalkan: ["Dibatalkan", "text-bg-secondary"]
};

function rupiah(angka) {
  return "Rp " + angka.toLocaleString("id-ID");
}

function formatTanggal(tgl) {
  if (!tgl) return "-";
  return new Date(tgl).toLocaleDateString("id-ID", { day: "numeric", month: "short", year: "numeric" });
}

function hitungSisaHari(selesai) {
  const hari = Math.ceil((new Date(selesai) - new Date("2026-11-10")) / (1000 * 60 * 60 * 24));
  return hari;
}

function tampilkanRingkasan() {
  const aktif = pesanan.filter(p => p.status !== "selesai" && p.status !== "dibatalkan" && p.status !== "ditolak");
  const bayar = pesanan.filter(p => p.status === "menunggu_pembayaran");
  const sewa = pesanan.filter(p => p.status === "disewa");
  const tagihan = aktif.reduce((jumlah, p) => jumlah + p.total, 0);

  document.getElementById("statAktif").textContent = aktif.length;
  document.getElementById("statBayar").textContent = bayar.length;
  document.getElementById("statSewa").textContent = sewa.length;
  document.getElementById("statTagihan").textContent = rupiah(tagihan);
}

function tampilkanTabel() {
  const isi = document.getElementById("daftarPesanan");

  isi.innerHTML = pesanan.map(function (p, i) {
    const [teks, kelas] = labelStatus[p.status];
    return `
      <tr>
        <td class="fw-semibold">${p.kode}</td>
        <td>${p.alat}</td>
        <td>${formatTanggal(p.mulai)} - ${formatTanggal(p.selesai)}</td>
        <td>${rupiah(p.total)}</td>
        <td><span class="badge ${kelas}">${teks}</span></td>
        <td class="text-end">
          <button class="btn btn-outline-primary btn-sm" data-index="${i}">Detail</button>
        </td>
      </tr>`;
  }).join("");

  isi.querySelectorAll("button[data-index]").forEach(function (tombol) {
    tombol.addEventListener("click", function () {
      tampilkanTracking(Number(tombol.dataset.index));
      document.getElementById("kartuTracking").scrollIntoView({ behavior: "smooth" });
    });
  });
}

function tampilkanTracking(index) {
  const p = pesanan[index];
  const sudahBayar = p.status !== "menunggu_pembayaran" && p.status !== "dibatalkan";

  // Tentukan tahap yang sudah dilewati
  const selesaiTahap = {
    dibuat: true,
    bayar: sudahBayar,
    ambil: p.tanggal.ambil !== null,
    kembali: p.tanggal.kembali !== null
  };

  let html = `<h5 class="fw-bold mb-1">${p.kode} · ${p.alat}</h5>`;
  html += `<p class="text-muted small mb-3">Periode ${formatTanggal(p.mulai)} - ${formatTanggal(p.selesai)}</p>`;

  if (p.status === "dibatalkan") {
    html += `<div class="alert alert-secondary mb-0">Booking ini dibatalkan.</div>`;
    document.getElementById("isiTracking").innerHTML = html;
    return;
  }

  html += `<ul class="list-group mb-3">`;
  tahap.forEach(function (t) {
    const ok = selesaiTahap[t.key];
    const ikon = ok ? "✅" : "⬜";
    let keterangan = "";
    if (t.key === "dibuat") keterangan = formatTanggal(p.tanggal.dibuat);
    if (t.key === "ambil" && p.tanggal.ambil) keterangan = formatTanggal(p.tanggal.ambil);
    if (t.key === "kembali" && p.tanggal.kembali) keterangan = formatTanggal(p.tanggal.kembali);
    html += `<li class="list-group-item d-flex justify-content-between">
      <span>${ikon} ${t.label}</span>
      <span class="text-muted small">${keterangan}</span>
    </li>`;
  });
  html += `</ul>`;

  if (p.status === "menunggu_pembayaran") {
    html += `<div class="alert alert-warning mb-0">Bayar di toko dengan membawa kode booking <strong>${p.kode}</strong> dan identitas sebagai jaminan.</div>`;
  } else if (p.status === "disewa") {
    const sisa = hitungSisaHari(p.selesai);
    const teksSisa = sisa > 0 ? `Sisa ${sisa} hari sewa.` : "Hari ini waktu pengembalian.";
    html += `<div class="alert alert-primary mb-0">${teksSisa} Kembalikan alat ke toko sesuai tanggal selesai.</div>`;
  } else if (p.status === "selesai") {
    html += `<div class="alert alert-success mb-0">Sewa selesai. Terima kasih!</div>`;
  }

  document.getElementById("isiTracking").innerHTML = html;
}

// Mode gelap (kunci localStorage sama dengan halaman katalog)
const html = document.documentElement;
const tombolDark = document.getElementById("darkToggle");
let gelap = localStorage.getItem("pelita-dark-mode") === "true";

function terapkanTema() {
  html.setAttribute("data-bs-theme", gelap ? "dark" : "light");
  tombolDark.textContent = gelap ? "Mode Terang" : "Mode Gelap";
}

tombolDark.addEventListener("click", function () {
  gelap = !gelap;
  localStorage.setItem("pelita-dark-mode", gelap);
  terapkanTema();
});

terapkanTema();
tampilkanRingkasan();
tampilkanTabel();