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
                    </div>    
                </div>
            </div>
        `;
  });
}

// function buat nambahin data baru ke array experiences
// pindah ke admin.js aja ya, biar lebih rapi

// buat delete data berdasarkan index yang dikasih
// di admin.js juga ya, biar lebih rapi

// buat edit data berdasarkan index yang dikasih
// di admin.js juga ya, biar lebih rapi

// ini buat update data juga
// di admin.js juga ya, biar lebih rapi

// buat kosongin form setelah data ditambah atau diupdate
// di admin.js juga ya, biar lebih rapi

renderExperiences();
