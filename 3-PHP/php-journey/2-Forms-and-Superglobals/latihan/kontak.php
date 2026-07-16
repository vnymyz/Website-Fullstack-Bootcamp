<?php
// ===================================================
// LATIHAN: FORM KONTAK STICKY
// ===================================================
//
// Soal:
// Bikin form kontak dengan field: nama, email, pesan
//
// Syarat:
// 1. Semua field WAJIB diisi (gak boleh kosong)
// 2. Email harus format valid (pakai filter_var + FILTER_VALIDATE_EMAIL)
// 3. Pesan minimal 10 karakter (pakai strlen())
// 4. Kalau ada error, tampilin semua pesan errornya, DAN form harus STICKY
//    (nilai yang udah diisi gak boleh hilang)
// 5. Kalau semua valid, redirect ke halaman "sukses" pakai header("Location: ...")
//    dan tampilkan pesan sukses (boleh di file ini juga pakai flag di URL,
//    misal: kontak.php?sukses=1)
// 6. Semua output ke browser WAJIB pakai htmlspecialchars() (cegah XSS)
// 7. Semua input WAJIB di-trim() dulu sebelum divalidasi
//
// Petunjuk struktur (boleh dicontoh dari validation-demo.php di folder sebelah,
// tapi ketik ulang sendiri, jangan copy-paste):
// - Cek $_SERVER['REQUEST_METHOD'] === 'POST'
// - Ambil & trim semua input
// - Validasi satu-satu, push ke array $errors kalau gagal
// - Kalau $errors kosong -> proses sukses (redirect atau tampilkan pesan)
// - Render HTML form di bawah, dengan value="" isinya variabel biar sticky

$errors = [];
$nama = "";
$email = "";
$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: ambil & trim input nama, email, pesan dari $_POST

    // TODO: validasi nama (wajib diisi)

    // TODO: validasi email (wajib diisi + format valid)

    // TODO: validasi pesan (wajib diisi + minimal 10 karakter)

    // TODO: kalau $errors kosong, proses sukses
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Latihan Form Kontak</title>
</head>
<body>

    <h2>Form Kontak</h2>

    <?php // TODO: tampilkan daftar error di sini kalau ada ?>

    <form action="kontak.php" method="POST">
        <label>Nama: <input type="text" name="nama" value=""></label><br><br>
        <label>Email: <input type="text" name="email" value=""></label><br><br>
        <label>Pesan:<br>
            <textarea name="pesan" rows="4" cols="40"></textarea>
        </label><br><br>
        <button type="submit">Kirim</button>
    </form>

</body>
</html>
