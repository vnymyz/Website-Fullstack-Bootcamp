package main

import (
	"context"
	"fmt"
	"log"
	"time"

	"golangmysql/internal/koneksi"
)

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	res, err := db.ExecContext(ctx,
		`INSERT INTO products (name, price, stock, description) VALUES (?, ?, ?, ?)`,
		"Headset Gaming", 350000, 8, "Surround 7.1",
	)
	if err != nil {
		log.Fatal(err)
	}

	id, err := res.LastInsertId()
	if err != nil {
		log.Fatal(err)
	}
	n, _ := res.RowsAffected()
	fmt.Printf("Berhasil! ID baru = %d, baris terpengaruh = %d\n", id, n)
}
