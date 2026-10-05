# 08 - Menangani NULL

## Tujuan Pembelajaran
- Memahami mengapa `NULL` menyulitkan di Go.
- Memakai `sql.NullString`, pointer, dan `COALESCE`.
- Menyimpan nilai NULL.

## Konsep
Tipe `string` Go tidak bisa bernilai "kosong yang bukan string kosong". Padahal di SQL, `NULL` berarti **tidak ada nilai**, berbeda dengan `''`.

Jika Anda `Scan` kolom NULL ke `string`:
```
sql: Scan error on column index 2, name "description": converting NULL to string is unsupported
```

Tiga cara menanganinya:

| Cara | Kelebihan | Kekurangan |
|---|---|---|
| `sql.NullString` (juga `NullInt64`, `NullFloat64`, `NullTime`, `NullBool`) | Eksplisit, ada `.Valid` | Bertele-tele; JSON-nya berbentuk objek |
| Pointer (`*string`) | Ringkas, `nil` = NULL, JSON-nya `null` | Hati-hati dereference `nil` |
| `COALESCE(kolom, 'default')` di SQL | Kode Go paling sederhana | Membuang informasi "NULL vs kosong" |

Dalam project fullstack (materi 4) kita memakai `COALESCE` untuk `description` dan pointer `*time.Time` untuk `due_date`.

## Langkah
Buat `contoh-kode/08-null/main.go`:

```go
// File: contoh-kode/08-null/main.go
package main

import (
	"context"
	"database/sql"
	"fmt"
	"log"

	"golangmysql/internal/koneksi"
)

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()
	ctx := context.Background()

	rows, err := db.QueryContext(ctx, `SELECT id, name, description FROM products ORDER BY id`)
	if err != nil {
		log.Fatal(err)
	}
	defer rows.Close()

	for rows.Next() {
		var (
			id   int64
			name string
			// Cara 1: sql.NullString
			descNull sql.NullString
		)
		if err := rows.Scan(&id, &name, &descNull); err != nil {
			log.Fatal(err)
		}
		if descNull.Valid {
			fmt.Printf("%d %s -> %s\n", id, name, descNull.String)
		} else {
			fmt.Printf("%d %s -> (tanpa deskripsi)\n", id, name)
		}
	}
	if err := rows.Err(); err != nil {
		log.Fatal(err)
	}

	// Cara 2: pointer. nil = NULL
	var desc *string
	err = db.QueryRowContext(ctx, `SELECT description FROM products WHERE name = ?`, "Mouse Wireless").Scan(&desc)
	if err != nil {
		log.Fatal(err)
	}
	if desc == nil {
		fmt.Println("\n[pointer] Mouse Wireless tidak punya deskripsi")
	} else {
		fmt.Println("\n[pointer] Deskripsi:", *desc)
	}

	// Cara 3: COALESCE di SQL
	var aman string
	if err := db.QueryRowContext(ctx,
		`SELECT COALESCE(description, '-') FROM products WHERE name = ?`, "Mouse Wireless").Scan(&aman); err != nil {
		log.Fatal(err)
	}
	fmt.Println("[COALESCE] Deskripsi:", aman)

	// Menyimpan NULL: kirim nil
	if _, err := db.ExecContext(ctx,
		`INSERT INTO products (name, price, stock, description) VALUES (?, ?, ?, ?)`,
		"Produk Tanpa Deskripsi", 1000, 1, nil); err != nil {
		log.Fatal(err)
	}
	fmt.Println("Produk dengan description NULL berhasil disimpan")
}
```

Jalankan:
```powershell
go run ./contoh-kode/08-null
```

## Output yang Diharapkan (kira-kira)
```
1 Keyboard Mekanik -> Switch biru, layout 87 tombol
2 Mouse Wireless -> (tanpa deskripsi)
3 Monitor 24 inci -> Panel IPS Full HD
...
[pointer] Mouse Wireless tidak punya deskripsi
[COALESCE] Deskripsi: -
Produk dengan description NULL berhasil disimpan
```

## Kesalahan Umum
- Men-`Scan` kolom nullable ke tipe biasa.
- Mendereferensi pointer `nil` (`*desc` ketika `desc == nil`) -> panic. Selalu cek `!= nil`.
- Menyimpan `""` padahal maksudnya NULL. Kirim `nil` untuk NULL.

## Latihan
Tambahkan kolom `discount DECIMAL(5,2) NULL` ke `products`, lalu baca nilainya memakai `sql.NullFloat64` dan pointer `*float64`.

## Catatan Instruktur
Estimasi 30 menit. Jelaskan perbedaan NULL, string kosong, dan angka 0.
