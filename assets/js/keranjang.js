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

function simpanKeranjang(data) {
  localStorage.setItem(KUNCI, JSON.stringify(data));
}

function rupiah(angka) {
  return "Rp " + angka.toLocaleString("id-ID");
}

function tampilkanKeranjang() {
  const data = ambilKeranjang();
  const isi = document.getElementById("isiKeranjang");
  const kosong = document.getElementById("keranjangKosong");
  const kartu = document.getElementById("kartuKeranjang");
  const lanjut = document.getElementById("lanjut");

  if (data.length === 0) {
    kosong.classList.remove("d-none");
    kartu.classList.add("d-none");
    lanjut.classList.add("disabled");
    document.getElementById("totalPerHari").textContent = "Rp 0";
    return;
  }

  kosong.classList.add("d-none");
  kartu.classList.remove("d-none");
  lanjut.classList.remove("disabled");

  isi.innerHTML = data.map(function (item, i) {
    return `
      <tr>
        <td class="fw-semibold">${item.nama}</td>
        <td>${rupiah(item.harga)}</td>
        <td>
          <input type="number" min="1" class="form-control form-control-sm" value="${item.qty}" data-index="${i}">
        </td>
        <td class="text-end">${rupiah(item.harga * item.qty)}</td>
        <td class="text-end">
          <button class="btn btn-outline-danger btn-sm" data-hapus="${i}">Hapus</button>
        </td>
      </tr>`;
  }).join("");

  const total = data.reduce(function (jumlah, item) {
    return jumlah + item.harga * item.qty;
  }, 0);
  document.getElementById("totalPerHari").textContent = rupiah(total);
}

// Ubah jumlah
document.getElementById("isiKeranjang").addEventListener("change", function (e) {
  if (!e.target.dataset.index) return;
  const data = ambilKeranjang();
  const jumlah = Math.max(1, parseInt(e.target.value, 10) || 1);
  data[Number(e.target.dataset.index)].qty = jumlah;
  simpanKeranjang(data);
  tampilkanKeranjang();
});

// Hapus item
document.getElementById("isiKeranjang").addEventListener("click", function (e) {
  if (!e.target.dataset.hapus) return;
  const data = ambilKeranjang();
  data.splice(Number(e.target.dataset.hapus), 1);
  simpanKeranjang(data);
  tampilkanKeranjang();
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
tampilkanKeranjang();