// fungsi untuk menangani login admin
function login() {
  // variabel konstanta untuk username dan password yang benar
  // konstanta adalah variabel yang nilainya tidak bisa diubah atau permanen
  // .value ini adalah nilai yang akan diinput oleh user
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  // buat nampilin pesan error
  const errorMsg = document.getElementById("errorMsg");

  // username dan password ini adalah benar
  const correctUsername = "admin";
  const correctPassword = "admin123";

  // karena kondisi ini bisa munculin pesan error
  // dengan kondisi yang banyak
  if (username !== correctUsername && password !== correctPassword) {
    errorMsg.textContent = "Username dan Password salah!";
  } else if (username !== correctUsername) {
    errorMsg.textContent = "Username salah!";
  } else if (password !== correctPassword) {
    errorMsg.textContent = "Password salah!";
  } else {
    // kalau misal username dan password benar
    // berarti kita bisa login
    localStorage.setItem("isAdminLoggedIn", "true");
    window.location.href = "admin.html";
  }
}
