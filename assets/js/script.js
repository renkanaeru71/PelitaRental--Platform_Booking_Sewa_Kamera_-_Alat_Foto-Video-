// Data contoh. Nanti diganti dari PHP/database.
const alat = [
  { nama: "Sony A7 III", kategori: "Kamera", harga: 350000, status: "Tersedia" },
  { nama: "Canon EOS 200D", kategori: "Kamera", harga: 200000, status: "Tersedia" },
  { nama: "Sigma 35mm f/1.4", kategori: "Lensa", harga: 120000, status: "Disewa" },
  { nama: "Canon 50mm f/1.8", kategori: "Lensa", harga: 60000, status: "Tersedia" },
  { nama: "Godox AD200 Pro", kategori: "Lighting", harga: 150000, status: "Tersedia" },
  { nama: "Godox V1", kategori: "Lighting", harga: 80000, status: "Disewa" },
  { nama: "Manfrotto 055", kategori: "Aksesori", harga: 80000, status: "Tersedia" },
  { nama: "Tripod Carbon", kategori: "Aksesori", harga: 50000, status: "Tersedia" }
];

const daftar = document.getElementById("daftar-alat");
const kosong = document.getElementById("kosong");
const cari = document.getElementById("cari");
const tombolFilter = document.querySelectorAll("#filter button");

let kategoriAktif = "Semua";

function rupiah(angka) {
  return "Rp " + angka.toLocaleString("id-ID");
}

function tampilkan() {
  const kata = cari.value.trim().toLowerCase();

  const hasil = alat.filter(function (item) {
    const cocokKategori = kategoriAktif === "Semua" || item.kategori === kategoriAktif;
    const cocokNama = item.nama.toLowerCase().includes(kata);
    return cocokKategori && cocokNama;
  });

  daftar.innerHTML = hasil.map(function (item) {
    const badgeStatus = item.status === "Tersedia" ? "text-bg-success" : "text-bg-warning";
    const tombolBooking = item.status === "Tersedia"
      ? '<a href="login.html" class="btn btn-primary btn-sm flex-fill">Booking</a>'
      : '<button class="btn btn-secondary btn-sm flex-fill" disabled>Penuh</button>';

    return `
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 kartu-alat">
          <div class="card-body d-flex flex-column">
            <span class="badge text-bg-secondary align-self-start mb-2">${item.kategori}</span>
            <h5 class="card-title">${item.nama}</h5>
            <p class="card-text mb-2">${rupiah(item.harga)} / hari</p>
            <span class="badge ${badgeStatus} align-self-start mb-3">${item.status}</span>
            <div class="mt-auto d-flex gap-2">
              <a href="login.html" class="btn btn-outline-primary btn-sm flex-fill">Keranjang</a>
              ${tombolBooking}
            </div>
          </div>
        </div>
      </div>`;
  }).join("");

  kosong.classList.toggle("d-none", hasil.length > 0);
}

// Pencarian
cari.addEventListener("input", tampilkan);

// Filter kategori
tombolFilter.forEach(function (tombol) {
  tombol.addEventListener("click", function () {
    kategoriAktif = tombol.dataset.kategori;

    tombolFilter.forEach(function (t) {
      t.classList.remove("aktif", "btn-primary");
      t.classList.add("btn-outline-primary");
    });
    tombol.classList.add("aktif", "btn-primary");
    tombol.classList.remove("btn-outline-primary");

    tampilkan();
  });
});

// Mode gelap (kunci localStorage sama dengan dashboard)
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
tampilkan();