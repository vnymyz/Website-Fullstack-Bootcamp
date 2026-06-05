<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Stock.php';

class StockController extends Controller {
    private Stock $stockModel;

    public function __construct() {
        $this->requireAuth();
        $this->stockModel = new Stock();
    }

    public function index(): void {
        $search  = trim($this->get('search', ''));
        $stocks  = $this->stockModel->all($search);
        $summary = $this->stockModel->summary();
        $this->view('stock/index', compact('stocks', 'search', 'summary'));
    }

    public function create(): void {
        $errors = $_SESSION['flash_errors'] ?? [];
        $old    = $_SESSION['flash_old']    ?? [];
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);
        $this->view('stock/create', compact('errors', 'old'));
    }

    public function store(): void {
        $data = [
            'kode_barang' => trim($this->post('kode_barang')),
            'nama_barang' => trim($this->post('nama_barang')),
            'kategori'    => trim($this->post('kategori')),
            'satuan'      => trim($this->post('satuan')),
            'stok'        => $this->post('stok', 0),
            'harga'       => $this->post('harga', 0),
            'keterangan'  => trim($this->post('keterangan')),
        ];

        $errors = $this->validateStock($data);

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old']    = $data;
            $this->redirect('/stock/create');
        }

        $this->stockModel->create($data);
        $_SESSION['flash_success'] = 'Barang berhasil ditambahkan.';
        $this->redirect('/stock');
    }

    public function edit(): void {
        $id    = (int)$this->get('id');
        $stock = $this->stockModel->find($id);
        if (!$stock) {
            $this->redirect('/stock');
        }
        $errors = $_SESSION['flash_errors'] ?? [];
        $old    = $_SESSION['flash_old']    ?? $stock;
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);
        $this->view('stock/edit', compact('stock', 'errors', 'old'));
    }

    public function update(): void {
        $id   = (int)$this->post('id');
        $data = [
            'kode_barang' => trim($this->post('kode_barang')),
            'nama_barang' => trim($this->post('nama_barang')),
            'kategori'    => trim($this->post('kategori')),
            'satuan'      => trim($this->post('satuan')),
            'stok'        => $this->post('stok', 0),
            'harga'       => $this->post('harga', 0),
            'keterangan'  => trim($this->post('keterangan')),
        ];

        $errors = $this->validateStock($data, $id);

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old']    = $data;
            $this->redirect('/stock/edit?id=' . $id);
        }

        $this->stockModel->update($id, $data);
        $_SESSION['flash_success'] = 'Barang berhasil diperbarui.';
        $this->redirect('/stock');
    }

    public function delete(): void {
        $id = (int)$this->post('id');
        if ($id > 0) {
            $this->stockModel->delete($id);
            $_SESSION['flash_success'] = 'Barang berhasil dihapus.';
        }
        $this->redirect('/stock');
    }

    private function validateStock(array $data, int $excludeId = 0): array {
        $errors = [];
        if (empty($data['kode_barang'])) {
            $errors['kode_barang'] = 'Kode barang wajib diisi.';
        } elseif ($this->stockModel->kodeExists($data['kode_barang'], $excludeId)) {
            $errors['kode_barang'] = 'Kode barang sudah digunakan.';
        }
        if (empty($data['nama_barang'])) {
            $errors['nama_barang'] = 'Nama barang wajib diisi.';
        }
        if (empty($data['satuan'])) {
            $errors['satuan'] = 'Satuan wajib diisi.';
        }
        if (!is_numeric($data['stok']) || (int)$data['stok'] < 0) {
            $errors['stok'] = 'Stok harus angka positif.';
        }
        if (!is_numeric($data['harga']) || (float)$data['harga'] < 0) {
            $errors['harga'] = 'Harga harus angka positif.';
        }
        return $errors;
    }
}
