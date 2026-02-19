// Array untuk menyimpan data siswa
let dataSiswa = [];

// Menyimpan index data yang sedang diedit
let indexEdit = null;

// Fungsi untuk menampilkan ulang isi tabel
function renderTable() {
  // Ambil elemen tbody
  let tableBody = document.getElementById("tableBody");

  // Kosongkan isi tabel sebelum dirender ulang
  tableBody.innerHTML = "";

  // Looping semua data dalam array
  dataSiswa.forEach((item, index) => {
    // Tambahkan baris baru ke tabel
    tableBody.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.nama}</td>
                <td>${item.umur}</td>
                <td>
                    <button onclick="editData(${index})">Edit</button>
                    <button onclick="hapusData(${index})">Hapus</button>
                </td>
            </tr>
        `;
  });
}

// Fungsi untuk menambahkan data baru
function tambahData() {
  // Ambil nilai dari input
  let nama = document.getElementById("nama").value;
  let umur = document.getElementById("umur").value;

  // Validasi jika input kosong
  if (nama === "" || umur === "") {
    alert("Nama dan Umur tidak boleh kosong!");
    return; // hentikan fungsi
  }

  // Tambahkan data ke dalam array
  dataSiswa.push({
    nama: nama,
    umur: umur,
  });

  // Bersihkan form
  clearForm();

  // Tampilkan ulang tabel
  renderTable();
}

// Fungsi untuk mengisi form saat tombol Edit ditekan
function editData(index) {
  // Ambil data berdasarkan index
  let siswa = dataSiswa[index];

  // Isi input dengan data yang dipilih
  document.getElementById("nama").value = siswa.nama;
  document.getElementById("umur").value = siswa.umur;

  // Simpan index yang sedang diedit
  indexEdit = index;

  // Sembunyikan tombol tambah
  document.querySelector("button[onclick='tambahData()']").style.display =
    "none";

  // Tampilkan tombol update
  document.getElementById("btnUpdate").style.display = "inline";
}

// Fungsi untuk menyimpan perubahan data
function updateData() {
  // Ambil nilai terbaru dari input
  let nama = document.getElementById("nama").value;
  let umur = document.getElementById("umur").value;

  // Update data berdasarkan index yang disimpan
  dataSiswa[indexEdit] = {
    nama: nama,
    umur: umur,
  };

  // Reset index edit
  indexEdit = null;

  // Tampilkan kembali tombol tambah
  document.querySelector("button[onclick='tambahData()']").style.display =
    "inline";

  // Sembunyikan tombol update
  document.getElementById("btnUpdate").style.display = "none";

  // Bersihkan form
  clearForm();

  // Render ulang tabel
  renderTable();
}

// Fungsi untuk menghapus data berdasarkan index
function hapusData(index) {
  // Hapus 1 data dari array berdasarkan posisi index
  dataSiswa.splice(index, 1);

  // Render ulang tabel setelah data dihapus
  renderTable();
}

// Fungsi untuk mengosongkan input form
function clearForm() {
  document.getElementById("nama").value = "";
  document.getElementById("umur").value = "";
}
