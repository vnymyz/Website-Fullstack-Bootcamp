// PROTECT ADMIN PAGE
if (localStorage.getItem("isAdminLoggedIn") !== "true") {
  window.location.href = "login.html";
}

let experiences = JSON.parse(localStorage.getItem("experiences")) || [];

function renderTable(data = experiences) {
  const tbody = document.getElementById("adminTableBody");
  tbody.innerHTML = "";

  data.forEach((exp, index) => {
    tbody.innerHTML += `
      <tr>
        <td>${exp.title}</td>
        <td>${exp.date}</td>
        <td>${exp.author}</td>
        <td>${exp.category}</td>
        <td>
          <button onclick="editData(${index})">Edit</button>
          <button onclick="deleteData(${index})">Delete</button>
        </td>
      </tr>
    `;
  });
}

function searchData() {
  const keyword = document.getElementById("searchInput").value.toLowerCase();

  const filtered = experiences.filter((exp) =>
    exp.title.toLowerCase().includes(keyword),
  );

  renderTable(filtered);
}

function deleteData(index) {
  experiences.splice(index, 1);
  localStorage.setItem("experiences", JSON.stringify(experiences));
  renderTable();
}

function logout() {
  localStorage.removeItem("isAdminLoggedIn");
  window.location.href = "../index.html";
}

renderTable();
