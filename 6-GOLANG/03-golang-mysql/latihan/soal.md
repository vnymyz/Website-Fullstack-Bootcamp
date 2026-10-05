# Latihan Materi 3 - Golang + MySQL

Persiapan: jalankan `database/schema.sql`, `database/seed.sql`, dan `database/latihan.sql` (membuat tabel `orders`).

## Soal 1 - Pencarian Produk (mudah)
Buat fungsi:
```go
func Cari(ctx context.Context, db *sql.DB, kata string, hargaMin float64) ([]Product, error)
```
- Mengembalikan produk yang `name` mengandung `kata` **dan** `price >= hargaMin`.
- Diurutkan dari harga tertinggi.
- Wajib memakai placeholder (tidak boleh menyambung string SQL).

**Kriteria:** `defer rows.Close()`, `rows.Err()`, dan tidak ada SQL injection.

## Soal 2 - Pembelian dengan Transaksi (sedang)
Buat fungsi:
```go
func Beli(ctx context.Context, db *sql.DB, produkID int64, qty int) (orderID int64, err error)
```
Aturan:
1. Jika produk tidak ada -> `ErrProdukTidakAda`.
2. Jika `stock < qty` -> `ErrStokKurang`.
3. Jika valid: kurangi stok **dan** simpan baris di `orders` (total = harga x qty) dalam **satu transaksi**.
4. Pakai `SELECT ... FOR UPDATE` agar dua pembelian bersamaan tidak membuat stok negatif.

## Soal 3 - Repository (sedang)
Ubah contoh `11-repository` menjadi paket terpisah `internal/product` dengan file:
- `model.go` (struct dan error)
- `repository.go` (interface)
- `mysql.go` (implementasi)

dan tulis satu `fakeRepository` di file test untuk menguji fungsi `HargaTotal(repo, id, qty)`.

## Soal 4 - Pagination (menantang)
Tambahkan `ListPage(ctx, page, limit)` yang mengembalikan produk beserta total baris (`COUNT(*)`). Validasi `page >= 1` dan `1 <= limit <= 100`.

## Soal Teori
1. Apa beda `sql.Open` dan `db.Ping`?
2. Mengapa `rows.Close()` dan `rows.Err()` penting?
3. Mengapa penyambungan string SQL berbahaya? Beri contoh serangan.
4. Kapan `RowsAffected()` bernilai 0 walaupun baris ada?
5. Apa guna `defer tx.Rollback()` setelah `BeginTx`?
6. Sebutkan dua kelebihan dan dua kekurangan ORM seperti GORM.

## Kunci Jawaban
- Kode: folder `latihan/solusi/` (Soal 1 dan 2).
- Teori: lihat bagian "Konsep" pada file materi terkait (`02`, `05`, `06`, `07`, `09`, `12`).
