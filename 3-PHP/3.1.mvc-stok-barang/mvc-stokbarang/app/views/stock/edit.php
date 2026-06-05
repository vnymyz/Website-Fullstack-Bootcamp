<?php
$pageTitle = 'Edit Barang: ' . htmlspecialchars($stock['nama_barang']);
ob_start();
$action      = '/stock/update';
$submitLabel = 'Perbarui Barang';
$hiddenId    = $stock['id'];
require_once __DIR__ . '/_form.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
