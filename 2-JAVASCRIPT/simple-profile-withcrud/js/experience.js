// buat nyiapin tempat buat nampung data yang kita punya
let experiences = JSON.parse(localStorage.getItem("experiences")) || [];
// tempat buat index semisal mau diedit atau dihapus
let editIndex = null;

// function buat nyimpen data ke local storage
// biar data tidak hilang pas di refresh atau ditutup browsernya
function saveToLocal() {
  localStorage.setItem("experiences", JSON.stringify(experiences));
}

// ini buat nampilin semua data nya
function renderExperiences() {
  const grid = document.getElementById("experienceGrid");
  grid.innerHTML = "";

  // perulangan buat nampilin semua data yang ada di array experiences
  experiences.forEach((exp, index) => {
    grid.innerHTML += `
            <div class="experience-card">
                <img src="${exp.image}" alt="${exp.title}">
                <div class="experience-content">
                    <h3>${exp.title}</h3>
                    <p class="meta">${exp.date} · ${exp.author}</p>
                    <p>${exp.description}</p>
                    <div class="tags">
                        <span>${exp.category}</span>
                    </div>
                    <br>
                    <div class="card-actions">
                  
                        <button onclick="editExperience(${index})" class="icon-btn edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    
                        <button onclick="deleteExperience(${index})" class="icon-btn delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>    
                </div>
            </div>
        `;
  });
}

// function buat nambahin data baru ke array experiences
function addExperience() {
  // ambil value dari inputan form
  const title = document.getElementById("title").value;
  const date = document.getElementById("date").value;
  const author = document.getElementById("author").value;
  const image = document.getElementById("image").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;

  // kalau misal salah satu data ada yang belum diisi maka
  if (!title || !date || !author || !image || !description) {
    // dia bakal ngasih peringatan buat ngisi semua field yang ada
    alert("Semua field wajib diisi!");
    return;
  }

  // kalau semua data udah keisi maka baru bisa ditambah
  experiences.push({
    title,
    date,
    author,
    image,
    description,
    category,
  });

  // tersimpan ke local storage
  saveToLocal();
  // render ulang semua data yang ada di array experiences
  renderExperiences();
  // kosongin form
  clearForm();
}

// buat delete data berdasarkan index yang dikasih
function deleteExperience(index) {
  experiences.splice(index, 1);
  saveToLocal();
  renderExperiences();
}

// buat edit data berdasarkan index yang dikasih
function editExperience(index) {
  const exp = experiences[index];

  // get value atau get data
  document.getElementById("title").value = exp.title;
  document.getElementById("date").value = exp.date;
  document.getElementById("author").value = exp.author;
  document.getElementById("image").value = exp.image;
  document.getElementById("description").value = exp.description;
  document.getElementById("category").value = exp.category;

  editIndex = index;

  // buat sembunyiin tombol add experience dan nampilin tombol update experience
  document.querySelector("button[onclick='addExperience()']").style.display =
    "none";
  document.getElementById("updateBtn").style.display = "inline-block";
}

// ini buat update data juga
function updateExperience() {
  experiences[editIndex] = {
    title: document.getElementById("title").value,
    date: document.getElementById("date").value,
    author: document.getElementById("author").value,
    image: document.getElementById("image").value,
    description: document.getElementById("description").value,
    category: document.getElementById("category").value,
  };

  // setelah data diupdate maka indexnya kita reset lagi ke null
  editIndex = null;

  saveToLocal();
  renderExperiences();
  clearForm();

  document.querySelector("button[onclick='addExperience()']").style.display =
    "inline-block";
  document.getElementById("updateBtn").style.display = "none";
}

// buat kosongin form setelah data ditambah atau diupdate
function clearForm() {
  document.getElementById("title").value = "";
  document.getElementById("date").value = "";
  document.getElementById("author").value = "";
  document.getElementById("image").value = "";
  document.getElementById("description").value = "";
}

renderExperiences();
