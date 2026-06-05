<?php
$pageTitle = 'Tambah Barang';
ob_start();
$action      = '/stock/store';
$submitLabel = 'Simpan Barang';
require_once __DIR__ . '/_form.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
