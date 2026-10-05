package main

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"log"

	"golangmysql/internal/koneksi"
)

var (
	ErrProdukTidakAda = errors.New("produk tidak ditemukan")
	ErrStokKurang     = errors.New("stok tidak cukup")
)

// Beli mengurangi stok dan mencatat order dalam SATU transaksi.
// Jika salah satu gagal, keduanya dibatalkan.
func Beli(ctx context.Context, db *sql.DB, produkID int64, qty int) (int64, error) {
	tx, err := db.BeginTx(ctx, nil)
	if err != nil {
		return 0, err
	}
	defer tx.Rollback()

	var harga float64
	var stok int
	err = tx.QueryRowContext(ctx,
		`SELECT price, stock FROM products WHERE id = ? FOR UPDATE`, produkID).Scan(&harga, &stok)
	if errors.Is(err, sql.ErrNoRows) {
		return 0, ErrProdukTidakAda
	}
	if err != nil {
		return 0, err
	}
	if stok < qty {
		return 0, ErrStokKurang
	}

	if _, err := tx.ExecContext(ctx,
		`UPDATE products SET stock = stock - ? WHERE id = ?`, qty, produkID); err != nil {
		return 0, err
	}

	res, err := tx.ExecContext(ctx,
		`INSERT INTO orders (product_id, qty, total) VALUES (?, ?, ?)`,
		produkID, qty, harga*float64(qty))
	if err != nil {
		return 0, err
	}
	orderID, err := res.LastInsertId()
	if err != nil {
		return 0, err
	}

	return orderID, tx.Commit()
}

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()
	ctx := context.Background()

	id, err := Beli(ctx, db, 1, 2)
	fmt.Println("Beli 2 unit produk 1 ->", id, err)

	id, err = Beli(ctx, db, 1, 9999)
	fmt.Println("Beli 9999 unit produk 1 ->", id, err)

	id, err = Beli(ctx, db, 12345, 1)
	fmt.Println("Beli produk 12345 ->", id, err)
}
