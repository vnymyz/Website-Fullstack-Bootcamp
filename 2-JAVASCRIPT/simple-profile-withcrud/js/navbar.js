// deklarasi variabel untuk tombol dan menu
const toggleBtn = document.querySelector(".menu-toggle");
const menu = document.querySelector(".nav-menu");
const navLogin = document.getElementById("navLogin");

// cek status login admin di local storage, kalau sudah login maka tampilkan "Admin" dan link ke halaman admin, kalau belum maka tampilkan "Login" dan link ke halaman login
if (localStorage.getItem("isAdminLoggedIn") === "true") {
  navLogin.innerHTML = '<i class="fa-solid fa-user"></i> Admin';
  navLogin.href = "../admin/admin.html";
} else {
  navLogin.textContent = "Login";
  navLogin.href = "../admin/login.html";
}

// kalau tombol diklik, maka menu akan
// di toggle (ditampilkan/disembunyikan)
toggleBtn.addEventListener("click", () => {
  menu.classList.toggle("active");
});
