package main

import (
	"context"
	"database/sql"
	"fmt"
	"log"

	"golangmysql/internal/koneksi"
)

type Product struct {
	ID    int64
	Name  string
	Price float64
	Stock int
}

// Cari mencari produk berdasarkan potongan nama dan harga minimal.
// Semua nilai dikirim lewat placeholder sehingga aman dari SQL injection.
func Cari(ctx context.Context, db *sql.DB, kata string, hargaMin float64) ([]Product, error) {
	rows, err := db.QueryContext(ctx,
		`SELECT id, name, price, stock FROM products
		 WHERE name LIKE ? AND price >= ?
		 ORDER BY price DESC`,
		"%"+kata+"%", hargaMin)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var hasil []Product
	for rows.Next() {
		var p Product
		if err := rows.Scan(&p.ID, &p.Name, &p.Price, &p.Stock); err != nil {
			return nil, err
		}
		hasil = append(hasil, p)
	}
	return hasil, rows.Err()
}

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	produk, err := Cari(context.Background(), db, "o", 100000)
	if err != nil {
		log.Fatal(err)
	}
	for _, p := range produk {
		fmt.Printf("%d | %-20s | Rp%.0f | stok %d\n", p.ID, p.Name, p.Price, p.Stock)
	}
	fmt.Println("Ditemukan:", len(produk))
}
