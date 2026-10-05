# 06 - Mengubah dan Menghapus Data

## Tujuan Pembelajaran
- Menjalankan `UPDATE` dan `DELETE` dengan aman.
- Memakai `RowsAffected` untuk mengetahui hasilnya.

## Konsep
> **Selalu pakai `WHERE`.** `UPDATE products SET price = 0` tanpa `WHERE` mengubah **semua** baris. Begitu juga `DELETE`.

`RowsAffected()` pada MySQL secara default menghitung baris yang **benar-benar berubah**. Jika nilai baru sama dengan nilai lama, hasilnya 0 walaupun baris ada. Jadi 0 bisa berarti:
1. ID tidak ditemukan, atau
2. data sama sehingga tidak ada yang berubah.

Jika perlu membedakan, cek keberadaan dengan `SELECT` terlebih dahulu (atau tambahkan `clientFoundRows=true` pada DSN agar yang dihitung baris yang cocok).

## Langkah
Buat `contoh-kode/06-update-delete/main.go`:

```go
// File: contoh-kode/06-update-delete/main.go
package main

import (
	"context"
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

	// UPDATE
	res, err := db.ExecContext(ctx,
		`UPDATE products SET price = ?, stock = stock + ? WHERE id = ?`, 475000, 5, 1)
	if err != nil {
		log.Fatal(err)
	}
	n, _ := res.RowsAffected()
	fmt.Println("UPDATE: baris terpengaruh =", n)

	// DELETE produk yang stoknya 0 (mungkin tidak ada)
	res, err = db.ExecContext(ctx, `DELETE FROM products WHERE stock = ?`, 0)
	if err != nil {
		log.Fatal(err)
	}
	n, _ = res.RowsAffected()
	fmt.Println("DELETE: baris terhapus =", n)

	// Tips: RowsAffected() = 0 bisa berarti id tidak ada, ATAU nilai baru sama dengan nilai lama.
}
```

Jalankan:
```powershell
go run ./contoh-kode/06-update-delete
```

## Output yang Diharapkan
```
UPDATE: baris terpengaruh = 1
DELETE: baris terhapus = 0
```

## Penjelasan
- `stock = stock + ?` melakukan perhitungan di database, aman dari *race condition* dibanding membaca-lalu-menulis di Go.
- Jalankan program dua kali: pada kali kedua harga sudah 475000, tetapi `stock` tetap berubah (+5), sehingga `RowsAffected` tetap 1.

## Kesalahan Umum
- Lupa `WHERE` (data hilang semua). Biasakan menulis `WHERE` dulu sebelum bagian lain.
- Mengira `RowsAffected = 0` selalu "data tidak ada".

## Latihan
Buat fungsi `HapusJikaAda(db, id) (bool, error)` yang mengembalikan `true` jika ada baris terhapus.

## Catatan Instruktur
Estimasi 30 menit. Cerita insiden nyata `DELETE` tanpa `WHERE` membuat siswa ingat.
