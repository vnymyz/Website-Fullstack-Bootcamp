<?php
// Ini file footer, isinya bagian bawah halaman
// $tahunSekarang dikirim dari halaman yang manggil include ini
$tahunSekarang = date("Y");
?>
<footer style="background:#333; color:white; padding:10px; margin-top:20px;">
    <p>&copy; <?php echo $tahunSekarang; ?> - Latihan PHP Fundamental</p>
</footer>
