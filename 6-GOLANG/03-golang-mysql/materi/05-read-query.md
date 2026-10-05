# 05 - Membaca Data (SELECT)

## Tujuan Pembelajaran
- Mengambil banyak baris dengan `QueryContext` dan `rows.Next`.
- Mengambil satu baris dengan `QueryRowContext`.
- Menangani `sql.ErrNoRows`.
- Memahami `rows.Close()` dan `rows.Err()`.

## Konsep
```
QueryContext --> *sql.Rows --> for rows.Next() { rows.Scan(...) } --> rows.Err()
```
- `Scan(&a, &b, ...)` mengisi variabel sesuai **urutan kolom** di `SELECT`.
- Tipe variabel harus cocok: `BIGINT` -> `int64`, `VARCHAR` -> `string`, `DECIMAL` -> `float64`, `TIMESTAMP` -> `time.Time` (butuh `parseTime=true`).
- **`defer rows.Close()`** wajib: mengembalikan koneksi ke pool.
- **`rows.Err()`** wajib setelah loop: error di tengah iterasi tidak terlihat dari `Next()` yang hanya mengembalikan `false`.
- `QueryRow` + `Scan` mengembalikan `sql.ErrNoRows` bila tidak ada baris. Itu bukan error sistem, tetapi "data tidak ada".

## Langkah
Buat `contoh-kode/05-query/main.go`:

```go
// File: contoh-kode/05-query/main.go
package main

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"log"
	"time"

	"golangmysql/internal/koneksi"
)

type Product struct {
	ID        int64
	Name      string
	Price     float64
	Stock     int
	CreatedAt time.Time
}

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()
	ctx := context.Background()

	// 1. Banyak baris: QueryContext + rows.Next
	rows, err := db.QueryContext(ctx,
		`SELECT id, name, price, stock, created_at FROM products ORDER BY id`)
	if err != nil {
		log.Fatal(err)
	}
	defer rows.Close() // wajib agar koneksi dikembalikan ke pool

	fmt.Println("== Semua produk ==")
	for rows.Next() {
		var p Product
		if err := rows.Scan(&p.ID, &p.Name, &p.Price, &p.Stock, &p.CreatedAt); err != nil {
			log.Fatal(err)
		}
		fmt.Printf("%d | %-20s | Rp%10.0f | stok %d\n", p.ID, p.Name, p.Price, p.Stock)
	}
	if err := rows.Err(); err != nil { // cek error setelah iterasi selesai
		log.Fatal(err)
	}

	// 2. Satu baris: QueryRowContext
	var p Product
	err = db.QueryRowContext(ctx,
		`SELECT id, name, price, stock, created_at FROM products WHERE id = ?`, 1).
		Scan(&p.ID, &p.Name, &p.Price, &p.Stock, &p.CreatedAt)
	switch {
	case errors.Is(err, sql.ErrNoRows):
		fmt.Println("Produk tidak ditemukan")
	case err != nil:
		log.Fatal(err)
	default:
		fmt.Printf("\nProduk id 1: %+v\n", p)
	}

	// 3. Agregat
	var jumlah int
	if err := db.QueryRowContext(ctx, `SELECT COUNT(*) FROM products`).Scan(&jumlah); err != nil {
		log.Fatal(err)
	}
	fmt.Println("Jumlah produk:", jumlah)
}
```

Jalankan:
```powershell
go run ./contoh-kode/05-query
```

## Output yang Diharapkan (kira-kira)
```
== Semua produk ==
1 | Keyboard Mekanik     | Rp    450000 | stok 10
2 | Mouse Wireless       | Rp    150000 | stok 25
3 | Monitor 24 inci      | Rp   1750000 | stok 5
...
Produk id 1: {ID:1 Name:Keyboard Mekanik Price:450000 Stock:10 CreatedAt:2026-...}
Jumlah produk: 4
```

## Penjelasan
1. Struct `Product` mewakili satu baris.
2. Bagian 1: iterasi semua baris.
3. Bagian 2: satu baris, dengan `switch` menangani tiga kemungkinan (tidak ada / error / sukses).
4. Bagian 3: `COUNT(*)` selalu mengembalikan tepat satu baris.

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `sql: Scan error ... unsupported Scan, storing driver.Value type []uint8 into type *time.Time` | `parseTime=true` hilang di DSN |
| `sql: expected 5 destination arguments in Scan, not 4` | Jumlah kolom SELECT != jumlah argumen Scan |
| `converting NULL to string is unsupported` | Kolom NULL (lihat materi 08) |
| Aplikasi lama-lama macet | Lupa `rows.Close()` |

## Latihan
1. Tampilkan hanya produk dengan harga di atas 200.000.
2. Buat fungsi `CariByNama(db, kata string) ([]Product, error)` dengan `LIKE ?` (ingat: `"%" + kata + "%"` tetap lewat placeholder).

## Catatan Instruktur
Estimasi 60 menit. Tunjukkan bug: hapus `defer rows.Close()` lalu jalankan di loop banyak kali.
