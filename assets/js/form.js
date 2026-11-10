const KUNCI = "pelita-keranjang";
const html = document.documentElement;
const tombolDark = document.getElementById("darkToggle");

function ambilKeranjang() {
  try {
    return JSON.parse(localStorage.getItem(KUNCI)) || [];
  } catch (e) {
    return [];
  }
}

function rupiah(angka) {
  return "Rp " + angka.toLocaleString("id-ID");
}

// Durasi = (selesai - mulai) + 1 hari
function hitungDurasi(mulai, selesai) {
  const selisih = (new Date(selesai) - new Date(mulai)) / (1000 * 60 * 60 * 24);
  return selisih + 1;
}

function kodeBookingBaru() {
  const angka = Math.floor(100 + Math.random() * 900);
  const sekarang = new Date();
  const bulan = String(sekarang.getMonth() + 1).padStart(2, "0");
  const tahun = String(sekarang.getFullYear()).slice(2);
  return "PR-" + tahun + bulan + "-" + angka;
}

const data = ambilKeranjang();
const tglMulai = document.getElementById("tglMulai");
const tglSelesai = document.getElementById("tglSelesai");
const pesanError = document.getElementById("pesanError");

if (data.length === 0) {
  document.getElementById("tidakAdaItem").classList.remove("d-none");
  document.getElementById("formBooking").classList.add("d-none");
  document.getElementById("btnBooking").classList.add("d-none");
}

// Ringkasan item
document.getElementById("ringkasanItem").innerHTML = data.map(function (item) {
  return `<li class="list-group-item d-flex justify-content-between">
    <span>${item.nama} <span class="text-muted">x${item.qty}</span></span>
    <span>${rupiah(item.harga * item.qty)} / hari</span>
  </li>`;
}).join("");

// Hitung total saat tanggal berubah
function hitungTotal() {
  const mulai = tglMulai.value;
  const selesai = tglSelesai.value;
  pesanError.classList.add("d-none");

  if (!mulai || !selesai) {
    document.getElementById("durasi").textContent = "0 hari";
    document.getElementById("totalBayar").textContent = "Rp 0";
    return 0;
  }

  const durasi = hitungDurasi(mulai, selesai);
  if (durasi < 1) {
    pesanError.textContent = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
    pesanError.classList.remove("d-none");
    document.getElementById("durasi").textContent = "0 hari";
    document.getElementById("totalBayar").textContent = "Rp 0";
    return 0;
  }

  const totalPerHari = data.reduce(function (jumlah, item) {
    return jumlah + item.harga * item.qty;
  }, 0);
  const total = totalPerHari * durasi;

  document.getElementById("durasi").textContent = durasi + " hari";
  document.getElementById("totalBayar").textContent = rupiah(total);
  return total;
}

tglMulai.addEventListener("change", hitungTotal);
tglSelesai.addEventListener("change", hitungTotal);

// Buat booking
document.getElementById("btnBooking").addEventListener("click", function () {
  if (!tglMulai.value || !tglSelesai.value) {
    pesanError.textContent = "Pilih tanggal mulai dan selesai dulu.";
    pesanError.classList.remove("d-none");
    return;
  }

  const total = hitungTotal();
  if (total === 0) return;

  document.getElementById("kodeBooking").textContent = kodeBookingBaru();
  document.getElementById("totalHasil").textContent = rupiah(total);
  document.getElementById("hasilBooking").classList.remove("d-none");
  document.getElementById("btnBooking").classList.add("d-none");

  // Keranjang dikosongkan setelah booking berhasil
  localStorage.removeItem(KUNCI);
});

// Mode gelap
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

function tambahKeranjang(nama, harga) {
  let data = [];
  try {
    data = JSON.parse(localStorage.getItem("pelita-keranjang")) || [];
  } catch (e) {}

  const ada = data.find(function (item) { return item.nama === nama; });
  if (ada) {
    ada.qty += 1;
  } else {
    data.push({ nama: nama, harga: harga, qty: 1 });
  }
  localStorage.setItem("pelita-keranjang", JSON.stringify(data));
  alert(nama + " ditambahkan ke keranjang.");
}