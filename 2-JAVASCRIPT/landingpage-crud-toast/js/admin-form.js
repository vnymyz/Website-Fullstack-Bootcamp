// protect admin
// kalau misal kita belum login, dan kita coba login
// berarti kita akan diarahkan ke halaman login.
if (localStorage.getItem("isAdminLoggedIn") !== "true") {
  window.location.href = "login.html";
}

// deklrasi variabel untuk menyimpan data crud kita
// wadah buat naro data yang kita tambah, edit, atau delete
// localstorage
// kalau misal data nya ada berarti di render semua || enggak ada berarti
// buat array baru yang kosong
let experiences = JSON.parse(localStorage.getItem("experiences")) || [];

// constanta itu permanen enggak bisa diubah
// const params buat searching
const params = new URLSearchParams(window.location.search);
// const edit buat ngambil data yang diedit
const editParam = params.get("edit");
// const editIndex buat nampung index yang diedit,
// kalau misal editParam nya null berarti null,
// kalau enggak berarti parseInt buat ubah string ke number
const editIndex = editParam !== null ? parseInt(editParam) : null;

// edit data berdasarkan index yang kita pilih
if (editIndex !== null && experiences[editIndex]) {
  const data = experiences[editIndex];

  // getelementbyid buat ngambil element berdasarkan id nya,
  // textcontent buat ubah isi teks nya
  document.getElementById("formTitle").textContent = "Edit Data";

  // buat ngisi data untuk diedit
  document.getElementById("title").value = data.title;
  document.getElementById("date").value = data.date;
  document.getElementById("author").value = data.author;
  document.getElementById("image").value = data.image;
  document.getElementById("description").value = data.description;
  document.getElementById("category").value = data.category;
}

// FUNCTION SAVE DATA
// kita ubah dengan toast
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

  // if edit index semisal kalau kita mau edit
  // semisal kita mau edit
  if (editIndex !== null) {
    experiences[editIndex] = {
      title,
      date,
      author,
      image,
      description,
      category,
    };
    // semisal kalau kita mau tambah data baru
  } else {
    experiences.push({ title, date, author, image, description, category });
  }

  // selanjutnya disimpan di local storage,
  // biar data nya enggak hilang pas di refresh atau ditutup browsernya
  localStorage.setItem("experiences", JSON.stringify(experiences));

  // kirim pesan toast ke admin.html
  localStorage.setItem(
    "toastMessage",
    editIndex !== null ? "Data berhasil diubah" : "Data berhasil ditambahkan",
  );

  window.location.href = "admin.html";
}

// buat logout, hapus data login di local storage,
//  terus arahkan ke halaman index.html
function logout() {
  localStorage.removeItem("isAdminLoggedIn");
  window.location.href = "../index.html";
}
