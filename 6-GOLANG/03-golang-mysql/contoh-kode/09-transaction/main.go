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
