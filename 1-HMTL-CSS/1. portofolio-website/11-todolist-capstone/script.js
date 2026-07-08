// ================================================
// TODO LIST CAPSTONE
// Gabungan: DOM (createElement/appendChild), events (addEventListener),
// forms (preventDefault), localStorage (persist data)
// ================================================

const formTodo = document.querySelector("#form-todo");
const inputTodo = document.querySelector("#input-todo");
const daftarTodo = document.querySelector("#daftar-todo");
const sisaTodoEl = document.querySelector("#sisa-todo");
const pesanKosong = document.querySelector("#pesan-kosong");
const tombolFilter = document.querySelectorAll(".btn-filter");

let filterAktif = "semua"; // "semua" | "aktif" | "selesai"

// ------------------------------------------------
// DATA LAYER — baca/tulis ke localStorage
// Setiap todo bentuknya: { id, teks, selesai }
// ------------------------------------------------

function ambilTodo() {
  const data = localStorage.getItem("daftar-todo");
  return data ? JSON.parse(data) : [];
}

function simpanTodo(todoList) {
  localStorage.setItem("daftar-todo", JSON.stringify(todoList));
}

// ------------------------------------------------
// RENDER — gambar ulang <ul> berdasarkan data + filter
// Pola umum: ubah data dulu → simpan ke localStorage → render()
// ------------------------------------------------

function render() {
  const semuaTodo = ambilTodo();

  // filter data sesuai tombol yang aktif
  const todoDitampilkan = semuaTodo.filter(function (todo) {
    if (filterAktif === "aktif") return !todo.selesai;
    if (filterAktif === "selesai") return todo.selesai;
    return true; // "semua"
  });

  daftarTodo.innerHTML = ""; // kosongkan dulu, render dari nol

  todoDitampilkan.forEach(function (todo) {
    const li = document.createElement("li");
    if (todo.selesai) li.classList.add("selesai");

    const checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.className = "checkbox-todo";
    checkbox.checked = todo.selesai;
    checkbox.addEventListener("change", function () {
      toggleSelesai(todo.id);
    });

    const teks = document.createElement("span");
    teks.className = "teks-todo";
    teks.textContent = todo.teks;

    const tombolHapus = document.createElement("button");
    tombolHapus.textContent = "✕";
    tombolHapus.className = "btn-hapus-todo";
    tombolHapus.addEventListener("click", function () {
      hapusTodo(todo.id);
    });

    li.appendChild(checkbox);
    li.appendChild(teks);
    li.appendChild(tombolHapus);
    daftarTodo.appendChild(li);
  });

  // hitung sisa yang belum selesai
  const jumlahAktif = semuaTodo.filter((t) => !t.selesai).length;
  sisaTodoEl.textContent = jumlahAktif + " item tersisa";

  // pesan kosong kalau nggak ada todo yang tampil
  pesanKosong.classList.toggle("hidden", todoDitampilkan.length > 0);
}

// ------------------------------------------------
// ACTIONS — ubah data, simpan, lalu render ulang
// ------------------------------------------------

function tambahTodo(teks) {
  const semuaTodo = ambilTodo();
  semuaTodo.push({
    id: Date.now(), // Date.now() = angka unik berdasarkan waktu saat ini
    teks: teks,
    selesai: false,
  });
  simpanTodo(semuaTodo);
  render();
}

function toggleSelesai(id) {
  const semuaTodo = ambilTodo();
  const todo = semuaTodo.find((t) => t.id === id); // cari todo dengan id itu
  todo.selesai = !todo.selesai; // balik nilai true/false-nya
  simpanTodo(semuaTodo);
  render();
}

function hapusTodo(id) {
  const semuaTodo = ambilTodo();
  const todoBaru = semuaTodo.filter((t) => t.id !== id); // buang yang id-nya cocok
  simpanTodo(todoBaru);
  render();
}

function hapusYangSelesai() {
  const semuaTodo = ambilTodo();
  const todoBaru = semuaTodo.filter((t) => !t.selesai); // sisain yang belum selesai aja
  simpanTodo(todoBaru);
  render();
}

// ------------------------------------------------
// EVENT LISTENERS
// ------------------------------------------------

formTodo.addEventListener("submit", function (e) {
  e.preventDefault(); // stop reload — ini yang dipelajari di Stage 8

  const teks = inputTodo.value.trim();
  if (teks === "") return;

  tambahTodo(teks);
  inputTodo.value = "";
  inputTodo.focus();
});

document.querySelector("#btn-hapus-selesai").addEventListener("click", hapusYangSelesai);

tombolFilter.forEach(function (tombol) {
  tombol.addEventListener("click", function () {
    // hapus "active" dari semua tombol filter, tambah ke yang diklik
    tombolFilter.forEach((t) => t.classList.remove("active"));
    tombol.classList.add("active");

    filterAktif = tombol.dataset.filter; // ambil dari atribut data-filter di HTML
    render();
  });
});

// render pertama kali saat halaman dibuka — tampilkan data lama dari localStorage
render();
