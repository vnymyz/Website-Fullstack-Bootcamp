// PROTECT ADMIN PAGE
if (localStorage.getItem("isAdminLoggedIn") !== "true") {
  window.location.href = "login.html";
}

let experiences = JSON.parse(localStorage.getItem("experiences")) || [];
let deleteIndex = null;

// LOGIKA BUAT PAGINATION
let currentPage = 1; // halaman awal atau default
const rowsPerPage = 3; // batas data per halaman

// nampilin data di tabel berdasarkan halaman yang aktif
// menggunakan pagination
function renderTable(data = experiences) {
  const tbody = document.getElementById("adminTableBody");
  const pagination = document.getElementById("pagination");

  tbody.innerHTML = "";
  pagination.innerHTML = "";

  // hitung total halaman jumlah data dibagi baris per halaman
  const totalPages = Math.ceil(data.length / rowsPerPage);

  // ambil data sesuai page
  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const paginatedData = data.slice(start, end);

  paginatedData.forEach((exp) => {
    const originalIndex = experiences.indexOf(exp);

    tbody.innerHTML += `
      <tr>
        <td>${exp.title}</td>
        <td>${exp.date}</td>
        <td>${exp.author}</td>
        <td>
          <span class="badge badge-${exp.category.toLowerCase()}">
            ${exp.category}
          </span>
        </td>
        <td class="action-buttons">
          <button class="btn-edit" onclick="editData(${originalIndex})">
            <i class="fa-solid fa-pen"></i>
          </button>
          <button class="btn-delete" onclick="deleteData(${originalIndex})">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      </tr>
    `;
  });

  // buat tombol pagination
  for (let i = 1; i <= totalPages; i++) {
    pagination.innerHTML += `
      <button class="${i === currentPage ? "active" : ""}"
        onclick="changePage(${i})">
        ${i}
      </button>
    `;
  }
}

// function buat ganti halaman saat tombol pagination diklik
function changePage(page) {
  currentPage = page;
  renderTable();
}

// buat search data berdasarkan keyword yang dimasukkan di input search
function searchData() {
  const keyword = document.getElementById("searchInput").value.toLowerCase();

  const filtered = experiences.filter((exp) =>
    exp.title.toLowerCase().includes(keyword),
  );

  currentPage = 1;
  renderTable(filtered);
}

// function buat edit data berdasarkan id atau index yang dikasih
function editData(index) {
  window.location.href = "admin-form.html?edit=" + index;
}

// buat delete data berdasarkan id atau index yang dikasih
// dengan modal konfirmasi
function deleteData(index) {
  deleteIndex = index;
  document.getElementById("confirmModal").style.display = "flex";
}

// buat logout admin, hapus status login di local storage dan redirect ke halaman login
function logout() {
  localStorage.removeItem("isAdminLoggedIn");
  window.location.href = "../index.html";
}

// FUNCTION NAMPILIN TOAST NOTIFICATION
function showToast(message) {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = "toast";
  toast.innerHTML = `
    <i class="fa-solid fa-circle-check"></i>
    ${message}
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 3000);
}

renderTable();

// BACA TOAST SAAT PAGE LOAD
const toastMessage = localStorage.getItem("toastMessage");

if (toastMessage) {
  showToast(toastMessage);
  localStorage.removeItem("toastMessage");
}

// LOGIKA BUAT MODAL DELETE DATA
// tombol YA
document.getElementById("confirmYes").onclick = function () {
  if (deleteIndex !== null) {
    experiences.splice(deleteIndex, 1);
    localStorage.setItem("experiences", JSON.stringify(experiences));
    renderTable();
    showToast("Data berhasil dihapus");
  }

  document.getElementById("confirmModal").style.display = "none";
  deleteIndex = null;
};

// tombol BATAL
document.getElementById("confirmNo").onclick = function () {
  document.getElementById("confirmModal").style.display = "none";
  deleteIndex = null;
};
