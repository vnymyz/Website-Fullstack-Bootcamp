<?php
// ===================================================
// VALIDASI FORM + STICKY FORM (nilai gak hilang pas error)
// ===================================================
// Pola di file ini: form dan proses validasinya digabung di SATU file yang
// sama, submit ke dirinya sendiri. Ini pola umum buat form yang perlu
// nampilin error tanpa pindah halaman.

$errors = [];
$nama = "";
$email = "";

// Cek apakah form ini baru aja di-submit (method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // trim() -- buang spasi kosong di depan/belakang, biar "   " gak dianggap valid
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // empty() -- cek apakah string kosong / "0" / null / belum diisi sama sekali
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi.";
    }

    if (empty($email)) {
        $errors[] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // filter_var dengan FILTER_VALIDATE_EMAIL -- cara gampang cek format email valid
        $errors[] = "Format email tidak valid.";
    }

    // Kalau gak ada error sama sekali, baru dianggap sukses
    if (empty($errors)) {
        echo "<h2 style='color:green;'>Berhasil! Terima kasih, " . htmlspecialchars($nama) . "</h2>";
        echo "<p><a href='validation-demo.php'>Isi lagi</a></p>";
        exit; // stop di sini, jangan tampilin form lagi di bawah
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi Form</title>
</head>
<body>

    <h2>Form Pendaftaran</h2>

    <?php if (!empty($errors)): ?>
        <div style="color:red; border:1px solid red; padding:10px;">
            <strong>Ada kesalahan:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="validation-demo.php" method="POST">
        <label>
            Nama:
            <input type="text" name="nama" value="<?php echo htmlspecialchars($nama); ?>">
        </label><br><br>

        <label>
            Email:
            <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
        </label><br><br>

        <button type="submit">Daftar</button>
    </form>

</body>
</html>
<?php
// Catatan buat kamu:
// - "Sticky form" artinya: kalau ada error, nilai yang UDAH DIISI tadi gak hilang,
//   user gak perlu isi ulang dari awal. Perhatikan value="<?php echo $nama ?>"
// - Coba submit form kosong, lihat errornya. Coba isi nama tapi email salah format.
// - Lanjut ke latihan/kontak.php
