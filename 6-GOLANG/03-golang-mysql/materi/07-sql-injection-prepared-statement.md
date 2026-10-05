# 07 - SQL Injection dan Prepared Statement

## Tujuan Pembelajaran
- Memahami apa itu SQL injection dan mengapa berbahaya.
- Selalu memakai query berparameter.
- Memakai prepared statement untuk eksekusi berulang.

## Konsep
**SQL injection**: penyerang menyisipkan potongan SQL lewat input (kolom login, pencarian, URL) sehingga makna query berubah.

Query rentan:
```go
q := "SELECT * FROM users WHERE email = '" + email + "' AND password = '" + pass + "'"
```
Jika `email` diisi `' OR '1'='1' -- `, query menjadi:
```sql
SELECT * FROM users WHERE email = '' OR '1'='1' -- ' AND password = ''
```
Kondisi selalu benar -> penyerang masuk tanpa password. Variasi lain bisa menghapus tabel atau membaca data seluruh pengguna.

### Solusi: query berparameter
```go
db.QueryRowContext(ctx, "SELECT ... WHERE email = ? AND ...", email, pass)
```
SQL dan data dikirim **terpisah**. Data tidak pernah ditafsirkan sebagai SQL.

### Prepared statement
`db.PrepareContext` meminta MySQL mengompilasi query sekali, lalu dijalankan berkali-kali dengan nilai berbeda. Cocok untuk insert massal dalam loop. Ingat `defer stmt.Close()`.

> Untuk query satu kali, cukup `QueryContext/ExecContext` dengan `?`; driver sudah menangani pengiriman parameter dengan aman.

### Bagian yang TIDAK bisa diberi placeholder
Nama tabel/kolom dan arah `ORDER BY` tidak dapat memakai `?`. Jika harus dinamis, **cocokkan dengan daftar putih (whitelist)**:
```go
allowed := map[string]bool{"name": true, "price": true}
if !allowed[sortBy] { sortBy = "name" }
q := "SELECT ... ORDER BY " + sortBy
```

## Langkah
Buat `contoh-kode/07-prepared-statement/main.go`:

```go
// File: contoh-kode/07-prepared-statement/main.go
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

	// Input "jahat" dari user
	input := "x' OR '1'='1"

	// SALAH: menyambung string -> rentan SQL injection
	bahaya := "SELECT COUNT(*) FROM products WHERE name = '" + input + "'"
	var salah int
	if err := db.QueryRowContext(ctx, bahaya).Scan(&salah); err != nil {
		log.Fatal(err)
	}
	fmt.Println("Query:", bahaya)
	fmt.Println("[SALAH] Hasil:", salah, "(semua baris ikut terambil!)")

	// BENAR: placeholder ?
	var benar int
	if err := db.QueryRowContext(ctx, `SELECT COUNT(*) FROM products WHERE name = ?`, input).Scan(&benar); err != nil {
		log.Fatal(err)
	}
	fmt.Println("[BENAR] Hasil:", benar)

	// Prepared statement: dipakai berulang kali dengan nilai berbeda
	stmt, err := db.PrepareContext(ctx, `INSERT INTO products (name, price, stock) VALUES (?, ?, ?)`)
	if err != nil {
		log.Fatal(err)
	}
	defer stmt.Close()

	for i := 1; i <= 3; i++ {
		if _, err := stmt.ExecContext(ctx, fmt.Sprintf("Barang Massal %d", i), 10000*i, i); err != nil {
			log.Fatal(err)
		}
	}
	fmt.Println("3 produk berhasil ditambahkan lewat prepared statement")
}
```

Jalankan:
```powershell
go run ./contoh-kode/07-prepared-statement
```

## Output yang Diharapkan
```
Query: SELECT COUNT(*) FROM products WHERE name = 'x' OR '1'='1'
[SALAH] Hasil: 5 (semua baris ikut terambil!)
[BENAR] Hasil: 0
3 produk berhasil ditambahkan lewat prepared statement
```
(Angka `[SALAH]` = jumlah semua baris di tabel Anda.)

## Kesalahan Umum
- Memakai `fmt.Sprintf` untuk membuat SQL dengan input user.
- Mengira `strings.Replace(input, "'", "")` cukup. Jangan; pakai placeholder.

## Latihan
Tulis fungsi `Login(db, email, password string) (bool, error)` versi **rentan** dan versi **aman** untuk tabel imajiner `users`; demonstrasikan serangannya hanya pada versi rentan di database lokal Anda.

## Catatan Instruktur
Estimasi 45 menit. Ini materi keamanan terpenting; sisakan waktu untuk diskusi dan demo di terminal.
