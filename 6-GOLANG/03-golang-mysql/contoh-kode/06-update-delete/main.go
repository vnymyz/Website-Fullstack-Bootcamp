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
