// ================================================
// Active link highlight
// window.location.pathname → nama file HTML yang lagi dibuka
// Bandingkan tiap link nav sama nama file itu, kasih class "active"
// kalau cocok
// ================================================

const semuaLink = document.querySelectorAll("nav a");
// location.pathname contoh: "/10-multi-page-nav/about.html"
// split("/") pecah jadi array, pop() ambil elemen terakhir = nama filenya
const halamanSekarang = window.location.pathname.split("/").pop();

semuaLink.forEach(function (link) {
  const hrefLink = link.getAttribute("href");

  if (hrefLink === halamanSekarang) {
    link.classList.add("active");
  }
});
