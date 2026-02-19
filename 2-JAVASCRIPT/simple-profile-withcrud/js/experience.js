let experiences = JSON.parse(localStorage.getItem("experiences")) || [];
let editIndex = null;

function saveToLocal() {
  localStorage.setItem("experiences", JSON.stringify(experiences));
}

function renderExperiences() {
  const grid = document.getElementById("experienceGrid");
  grid.innerHTML = "";

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

function addExperience() {
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

  experiences.push({
    title,
    date,
    author,
    image,
    description,
    category,
  });

  saveToLocal();
  renderExperiences();
  clearForm();
}

function deleteExperience(index) {
  experiences.splice(index, 1);
  saveToLocal();
  renderExperiences();
}

function editExperience(index) {
  const exp = experiences[index];

  document.getElementById("title").value = exp.title;
  document.getElementById("date").value = exp.date;
  document.getElementById("author").value = exp.author;
  document.getElementById("image").value = exp.image;
  document.getElementById("description").value = exp.description;
  document.getElementById("category").value = exp.category;

  editIndex = index;

  document.querySelector("button[onclick='addExperience()']").style.display =
    "none";
  document.getElementById("updateBtn").style.display = "inline-block";
}

function updateExperience() {
  experiences[editIndex] = {
    title: document.getElementById("title").value,
    date: document.getElementById("date").value,
    author: document.getElementById("author").value,
    image: document.getElementById("image").value,
    description: document.getElementById("description").value,
    category: document.getElementById("category").value,
  };

  editIndex = null;

  saveToLocal();
  renderExperiences();
  clearForm();

  document.querySelector("button[onclick='addExperience()']").style.display =
    "inline-block";
  document.getElementById("updateBtn").style.display = "none";
}

function clearForm() {
  document.getElementById("title").value = "";
  document.getElementById("date").value = "";
  document.getElementById("author").value = "";
  document.getElementById("image").value = "";
  document.getElementById("description").value = "";
}

renderExperiences();
