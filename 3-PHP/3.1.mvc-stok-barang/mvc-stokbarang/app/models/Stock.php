<?php
require_once __DIR__ . '/../../core/Model.php';

class Stock extends Model {
    public function all(string $search = ''): array {
        if ($search !== '') {
            return $this->query(
                'SELECT * FROM stocks WHERE nama_barang LIKE ? OR kode_barang LIKE ? OR kategori LIKE ? ORDER BY nama_barang',
                ["%$search%", "%$search%", "%$search%"]
            )->fetchAll();
        }
        return $this->query('SELECT * FROM stocks ORDER BY nama_barang')->fetchAll();
    }

    public function find(int $id): array|false {
        return $this->query('SELECT * FROM stocks WHERE id = ? LIMIT 1', [$id])->fetch();
    }

    public function create(array $data): bool {
        return $this->query(
            'INSERT INTO stocks (kode_barang, nama_barang, kategori, satuan, stok, harga, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['kode_barang'],
                $data['nama_barang'],
                $data['kategori'],
                $data['satuan'],
                (int)$data['stok'],
                (float)$data['harga'],
                $data['keterangan'],
            ]
        )->rowCount() > 0;
    }

    public function update(int $id, array $data): bool {
        return $this->query(
            'UPDATE stocks SET kode_barang=?, nama_barang=?, kategori=?, satuan=?, stok=?, harga=?, keterangan=?, updated_at=NOW() WHERE id=?',
            [
                $data['kode_barang'],
                $data['nama_barang'],
                $data['kategori'],
                $data['satuan'],
                (int)$data['stok'],
                (float)$data['harga'],
                $data['keterangan'],
                $id,
            ]
        )->rowCount() > 0;
    }

    public function delete(int $id): bool {
        return $this->query('DELETE FROM stocks WHERE id = ?', [$id])->rowCount() > 0;
    }

    public function kodeExists(string $kode, int $excludeId = 0): bool {
        return (bool)$this->query(
            'SELECT id FROM stocks WHERE kode_barang = ? AND id != ? LIMIT 1',
            [$kode, $excludeId]
        )->fetch();
    }

    public function summary(): array {
        return $this->query(
            'SELECT COUNT(*) as total_items, SUM(stok) as total_stok, SUM(stok * harga) as total_nilai FROM stocks'
        )->fetch();
    }
}
