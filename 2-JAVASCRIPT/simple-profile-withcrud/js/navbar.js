// deklarasi variabel untuk tombol dan menu
const toggleBtn = document.querySelector(".menu-toggle");
const menu = document.querySelector(".nav-menu");

// kalau tombol diklik, maka menu akan
// di toggle (ditampilkan/disembunyikan)
toggleBtn.addEventListener("click", () => {
  menu.classList.toggle("active");
});
