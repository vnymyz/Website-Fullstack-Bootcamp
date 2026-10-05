# 04 - Menyimpan Data (INSERT)

## Tujuan Pembelajaran
- Menjalankan perintah yang tidak mengembalikan baris dengan `ExecContext`.
- Memakai placeholder `?`.
- Membaca `LastInsertId` dan `RowsAffected`.

## Konsep
| Method | Dipakai untuk |
|---|---|
| `Exec` / `ExecContext` | INSERT, UPDATE, DELETE, DDL (tidak mengembalikan baris) |
| `Query` / `QueryContext` | SELECT banyak baris |
| `QueryRow` / `QueryRowContext` | SELECT satu baris |

Hasil `Exec` adalah `sql.Result` dengan:
- `LastInsertId()` -> ID auto-increment baris yang baru dibuat
- `RowsAffected()` -> jumlah baris yang terpengaruh

**Placeholder `?`**: nilai dikirim terpisah dari SQL, sehingga aman dari SQL injection (dibahas di materi 07).

## Langkah
Buat `contoh-kode/04-insert/main.go`:

```go
// File: contoh-kode/04-insert/main.go
package main

import (
	"context"
	"fmt"
	"log"
	"time"

	"golangmysql/internal/koneksi"
)

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	res, err := db.ExecContext(ctx,
		`INSERT INTO products (name, price, stock, description) VALUES (?, ?, ?, ?)`,
		"Headset Gaming", 350000, 8, "Surround 7.1",
	)
	if err != nil {
		log.Fatal(err)
	}

	id, err := res.LastInsertId()
	if err != nil {
		log.Fatal(err)
	}
	n, _ := res.RowsAffected()
	fmt.Printf("Berhasil! ID baru = %d, baris terpengaruh = %d\n", id, n)
}
```

Jalankan:
```powershell
go run ./contoh-kode/04-insert
```

## Output yang Diharapkan
```
Berhasil! ID baru = 4, baris terpengaruh = 1
```
Periksa di phpMyAdmin: tabel `products` sekarang punya baris "Headset Gaming".

## Penjelasan
- `context.WithTimeout(..., 5*time.Second)`: bila query lebih dari 5 detik, dibatalkan.
- Urutan argumen setelah SQL **harus sama** dengan urutan `?`.
- `description` (kolom `TEXT NULL`) diisi string; untuk mengisi NULL kirim `nil` (lihat materi 08).

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `Error 1136: Column count doesn't match value count` | Jumlah `?` tidak sama dengan jumlah kolom |
| `sql: expected 4 arguments, got 3` | Jumlah argumen kurang |
| `Error 1364: Field 'name' doesn't have a default value` | Kolom NOT NULL tidak diisi |

## Latihan
Tambahkan 2 produk lagi lewat program (ubah nilai di kode), lalu cek di phpMyAdmin.

## Catatan Instruktur
Estimasi 30 menit. Minta siswa sengaja membuat jumlah `?` salah untuk membaca pesan error.
