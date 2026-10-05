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
