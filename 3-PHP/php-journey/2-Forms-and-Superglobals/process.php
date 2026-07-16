<?php
// ===================================================
// SUPERGLOBALS: $_GET, $_POST, $_REQUEST, $_SERVER
// ===================================================
// Superglobal = variabel bawaan PHP yang bisa diakses dari mana aja
// tanpa perlu "import" atau declare dulu.

echo "<h2>Isi \$_GET</h2>";
echo "<pre>";
print_r($_GET); // kosong kalau kamu submit form yang method="POST"
echo "</pre>";

echo "<h2>Isi \$_POST</h2>";
echo "<pre>";
print_r($_POST); // kosong kalau kamu submit form yang method="GET"
echo "</pre>";

echo "<h2>Isi \$_REQUEST</h2>";
echo "<pre>";
print_r($_REQUEST); // gabungan GET + POST + COOKIE, tapi HINDARI pakai ini
// karena gak jelas datanya dari mana -- lebih baik eksplisit pakai $_GET atau $_POST
echo "</pre>";

echo "<h2>Beberapa isi \$_SERVER yang sering dipakai</h2>";
echo "Request method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "Alamat script ini: " . $_SERVER['PHP_SELF'] . "<br>";
echo "User agent (browser kamu): " . $_SERVER['HTTP_USER_AGENT'] . "<br>";


// ===================================================
// AMBIL DATA DENGAN AMAN: isset() dan htmlspecialchars()
// ===================================================

// Gabung data dari GET atau POST, mana aja yang ada isinya
$nama = $_POST['nama'] ?? $_GET['nama'] ?? null;
$umur = $_POST['umur'] ?? $_GET['umur'] ?? null;

// "?? null" ini disebut null coalescing operator:
// artinya "kalau $_POST['nama'] gak ada / gak diisi, pakai null aja, jangan error"

if ($nama !== null) {
    echo "<hr>";
    echo "<h2>Halo, " . htmlspecialchars($nama) . "!</h2>";
    echo "<p>Umur kamu: " . htmlspecialchars($umur) . "</p>";

    // KENAPA htmlspecialchars() PENTING:
    // Coba isi input "Nama" di form.php dengan: <script>alert('kena XSS')</script>
    // Kalau kamu echo LANGSUNG tanpa htmlspecialchars, script itu akan DIEKSEKUSI
    // oleh browser -- ini namanya XSS (Cross-Site Scripting), celah keamanan serius.
    // htmlspecialchars() mengubah karakter < > " ' jadi entity HTML biasa,
    // jadi script-nya cuma ditampilin sebagai TEKS, bukan dieksekusi.
} else {
    echo "<p>Belum ada data dikirim. <a href='form.php'>Balik ke form</a></p>";
}

// Catatan buat kamu:
// - Buka process.php?nama=Test&umur=99 langsung dari URL (tanpa lewat form),
//   lihat kenapa itu bisa jalan -- karena GET emang nempel di URL
// - Coba juga isi form dengan <script>alert(1)</script> di kolom nama, lihat bedanya
//   sebelum dan sesudah pakai htmlspecialchars()
// - Lanjut ke validation-demo.php
