<?php
// ===================================================
// FORM GET vs FORM POST
// ===================================================
//
// GET  -> data dikirim lewat URL (?nama=budi&umur=20), keliatan di address bar
//         cocok buat: pencarian, filter, halaman yang boleh di-bookmark/share
// POST -> data dikirim "tersembunyi" di body request, gak keliatan di URL
//         cocok buat: login, register, kirim data sensitif, ubah data di database
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Latihan Form</title>
</head>
<body>

    <h2>Form pakai method GET</h2>
    <form action="process.php" method="GET">
        <label>Nama: <input type="text" name="nama"></label><br><br>
        <label>Umur: <input type="number" name="umur"></label><br><br>
        <button type="submit">Kirim (GET)</button>
    </form>
    <p><i>Coba submit, perhatikan URL di address bar berubah jadi ?nama=...&umur=...</i></p>

    <hr>

    <h2>Form pakai method POST</h2>
    <form action="process.php" method="POST">
        <label>Nama: <input type="text" name="nama"></label><br><br>
        <label>Umur: <input type="number" name="umur"></label><br><br>
        <button type="submit">Kirim (POST)</button>
    </form>
    <p><i>Coba submit, perhatikan URL TIDAK berubah, tapi data tetap terkirim.</i></p>

</body>
</html>
