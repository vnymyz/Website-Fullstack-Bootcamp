// PROTECT ADMIN PAGE
if (localStorage.getItem("isAdminLoggedIn") !== "true") {
  window.location.href = "login.html";
}

let experiences = JSON.parse(localStorage.getItem("experiences")) || [];

function renderTable(data = experiences) {
  const tbody = document.getElementById("adminTableBody");
  tbody.innerHTML = "";

  data.forEach((exp) => {
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
}

// buat search data berdasarkan keyword yang dimasukkan di input search
function searchData() {
  const keyword = document.getElementById("searchInput").value.toLowerCase();

  const filtered = experiences.filter((exp) =>
    exp.title.toLowerCase().includes(keyword),
  );

  renderTable(filtered);
}

// function buat edit data berdasarkan id atau index yang dikasih
function editData(index) {
  window.location.href = "admin-form.html?edit=" + index;
}

// buat delete data berdasarkan id atau index yang dikasih
function deleteData(index) {
  experiences.splice(index, 1);
  localStorage.setItem("experiences", JSON.stringify(experiences));
  renderTable();
}

// buat logout admin, hapus status login di local storage dan redirect ke halaman login
function logout() {
  localStorage.removeItem("isAdminLoggedIn");
  window.location.href = "../index.html";
}

renderTable();
