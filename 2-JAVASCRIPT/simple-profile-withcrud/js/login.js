function login() {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const errorMsg = document.getElementById("errorMsg");

  const correctUsername = "admin";
  const correctPassword = "admin123";

  if (username !== correctUsername && password !== correctPassword) {
    errorMsg.textContent = "Username dan Password salah!";
  } else if (username !== correctUsername) {
    errorMsg.textContent = "Username salah!";
  } else if (password !== correctPassword) {
    errorMsg.textContent = "Password salah!";
  } else {
    localStorage.setItem("isAdminLoggedIn", "true");
    window.location.href = "admin.html";
  }
}
