# 09 - Transaction

## Tujuan Pembelajaran
- Memahami konsep ACID dan kapan memakai transaksi.
- Memakai `BeginTx`, `Commit`, `Rollback`.
- Pola `defer tx.Rollback()`.

## Konsep
**Transaksi** = sekelompok operasi yang dianggap satu kesatuan: **semua berhasil** atau **semua dibatalkan**.

Contoh klasik: transfer uang Rp200.000 dari Andi ke Budi.
1. Kurangi saldo Andi
2. Tambah saldo Budi

Jika langkah 1 sukses lalu program crash sebelum langkah 2, uang menguap. Transaksi mencegahnya.

**ACID**
| Huruf | Arti |
|---|---|
| Atomicity | Semua atau tidak sama sekali |
| Consistency | Aturan data (constraint) tetap terjaga |
| Isolation | Transaksi bersamaan tidak saling mengganggu |
| Durability | Setelah commit, data aman walau server mati |

> Tabel harus memakai engine **InnoDB** (default MySQL modern). MyISAM tidak mendukung transaksi.

### Pola di Go
```go
tx, err := db.BeginTx(ctx, nil)
if err != nil { return err }
defer tx.Rollback()      // jaring pengaman

// ... tx.ExecContext / tx.QueryRowContext ...

return tx.Commit()
```
- Semua query di dalam transaksi memakai **`tx`**, bukan `db`. Memakai `db` akan berjalan di luar transaksi.
- `defer tx.Rollback()` aman: setelah `Commit` sukses ia hanya mengembalikan `sql.ErrTxDone`.
- `SELECT ... FOR UPDATE` mengunci baris sampai transaksi selesai (mencegah dua transfer mengubah saldo yang sama bersamaan).

## Langkah
Buat `contoh-kode/09-transaction/main.go`:

```go
// File: contoh-kode/09-transaction/main.go
package main

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"log"

	"golangmysql/internal/koneksi"
)

var ErrSaldoKurang = errors.New("saldo tidak cukup")

// Transfer memindahkan uang antar akun. Dua UPDATE harus berhasil bersama-sama atau batal bersama-sama.
func Transfer(ctx context.Context, db *sql.DB, dariID, keID int64, jumlah float64) error {
	tx, err := db.BeginTx(ctx, nil)
	if err != nil {
		return err
	}
	// Jika fungsi berakhir tanpa Commit (error/return), semua dibatalkan.
	// Rollback setelah Commit sukses aman: hanya mengembalikan sql.ErrTxDone.
	defer tx.Rollback()

	// Kunci baris pengirim agar tidak ada transaksi lain yang mengubah saldo bersamaan.
	var saldo float64
	if err := tx.QueryRowContext(ctx,
		`SELECT balance FROM accounts WHERE id = ? FOR UPDATE`, dariID).Scan(&saldo); err != nil {
		return fmt.Errorf("ambil saldo: %w", err)
	}
	if saldo < jumlah {
		return ErrSaldoKurang
	}

	if _, err := tx.ExecContext(ctx,
		`UPDATE accounts SET balance = balance - ? WHERE id = ?`, jumlah, dariID); err != nil {
		return fmt.Errorf("kurangi saldo: %w", err)
	}
	if _, err := tx.ExecContext(ctx,
		`UPDATE accounts SET balance = balance + ? WHERE id = ?`, jumlah, keID); err != nil {
		return fmt.Errorf("tambah saldo: %w", err)
	}

	return tx.Commit()
}

func cetakSaldo(ctx context.Context, db *sql.DB) {
	rows, err := db.QueryContext(ctx, `SELECT owner, balance FROM accounts ORDER BY id`)
	if err != nil {
		log.Fatal(err)
	}
	defer rows.Close()
	for rows.Next() {
		var owner string
		var balance float64
		if err := rows.Scan(&owner, &balance); err != nil {
			log.Fatal(err)
		}
		fmt.Printf("  %-6s Rp%.0f\n", owner, balance)
	}
}

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()
	ctx := context.Background()

	fmt.Println("Saldo awal:")
	cetakSaldo(ctx, db)

	fmt.Println("\nTransfer Rp200.000 dari Andi (1) ke Budi (2):")
	if err := Transfer(ctx, db, 1, 2, 200000); err != nil {
		fmt.Println("  GAGAL:", err)
	} else {
		fmt.Println("  Berhasil")
	}
	cetakSaldo(ctx, db)

	fmt.Println("\nTransfer Rp99.000.000 dari Andi (1) ke Budi (2):")
	if err := Transfer(ctx, db, 1, 2, 99000000); err != nil {
		fmt.Println("  GAGAL:", err, "(saldo tidak berubah)")
	}
	cetakSaldo(ctx, db)
}
```

Jalankan:
```powershell
go run ./contoh-kode/09-transaction
```

## Output yang Diharapkan
Pada jalankan pertama (data seed: Andi 1.000.000, Budi 500.000):
```
Saldo awal:
  Andi   Rp1000000
  Budi   Rp500000

Transfer Rp200.000 dari Andi (1) ke Budi (2):
  Berhasil
  Andi   Rp800000
  Budi   Rp700000

Transfer Rp99.000.000 dari Andi (1) ke Budi (2):
  GAGAL: saldo tidak cukup (saldo tidak berubah)
  Andi   Rp800000
  Budi   Rp700000
```
Jalankan ulang `database/seed.sql` bagian `accounts` (atau `schema.sql` lalu `seed.sql`) untuk mengembalikan saldo awal.

## Kesalahan Umum
- Memakai `db.Exec` di dalam transaksi (seharusnya `tx.Exec`).
- Lupa `Commit` -> perubahan hilang.
- Transaksi dibiarkan terbuka lama (mengunci baris, menahan koneksi pool).

## Latihan
Tambahkan tabel `transfers (id, from_id, to_id, amount, created_at)` dan catat tiap transfer **di dalam transaksi yang sama**.

## Catatan Instruktur
Estimasi 60 menit. Demo: komentari `tx.Commit()`, ganti dengan `return nil`, lalu tunjukkan data tidak tersimpan.
