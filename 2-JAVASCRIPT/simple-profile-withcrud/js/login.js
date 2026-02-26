// Ambil element form dari halaman HTML
const form = document.getElementById("login-form")

// Function ini akan kita gunakan untuk menghandle proses login
form.addEventListener("submit", function(event) {
    // Kita gunakan ini untuk mencegah reload
    event.preventDefault()
    
    // Kita get dulu data username
    const username = document.getElementById("username").value
    console.log(username)

    // Get data password dari form
    const password = document.getElementById("password").value
    console.log(password)

    // Verifikasi password yang diinput oleh user
    // Ini hanya contoh, sebaiknya data login/akun disimpan dalam database yang aman
    const validUsername = "admin" 
    const validPassword = "admin123"

    // Cek apakah username dan password yang diinput oleh user
    // Sesuai dengan yang ada di program
    if(username == validUsername && password == validPassword) {
        // Ketika berhasil login
        window.location.href = "../dashboard.html"

        // TODO: Implement save sesi login ke localStorage
    } else {
        // Ketika gagal login
        alert("Username atau password salah!")
    }
})