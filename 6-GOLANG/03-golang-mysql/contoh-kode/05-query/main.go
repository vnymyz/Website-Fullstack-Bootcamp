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
