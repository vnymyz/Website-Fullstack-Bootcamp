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
