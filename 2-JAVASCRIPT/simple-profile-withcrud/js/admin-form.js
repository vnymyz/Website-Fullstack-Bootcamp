// protect admin
if (localStorage.getItem("isAdminLoggedIn") !== "true") {
  window.location.href = "login.html";
}

let experiences = JSON.parse(localStorage.getItem("experiences")) || [];

const params = new URLSearchParams(window.location.search);
const editParam = params.get("edit");
const editIndex = editParam !== null ? parseInt(editParam) : null;

if (editIndex !== null && experiences[editIndex]) {
  const data = experiences[editIndex];

  document.getElementById("formTitle").textContent = "Edit Artikel";

  document.getElementById("title").value = data.title;
  document.getElementById("date").value = data.date;
  document.getElementById("author").value = data.author;
  document.getElementById("image").value = data.image;
  document.getElementById("description").value = data.description;
  document.getElementById("category").value = data.category;
}

function saveData() {
  const title = document.getElementById("title").value;
  const date = document.getElementById("date").value;
  const author = document.getElementById("author").value;
  const image = document.getElementById("image").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;

  if (!title || !date || !author || !image || !description) {
    alert("Semua field wajib diisi!");
    return;
  }

  if (editIndex !== null) {
    experiences[editIndex] = {
      title,
      date,
      author,
      image,
      description,
      category,
    };
  } else {
    experiences.push({ title, date, author, image, description, category });
  }

  localStorage.setItem("experiences", JSON.stringify(experiences));
  window.location.href = "admin.html";
}

function logout() {
  localStorage.removeItem("isAdminLoggedIn");
  window.location.href = "../index.html";
}
