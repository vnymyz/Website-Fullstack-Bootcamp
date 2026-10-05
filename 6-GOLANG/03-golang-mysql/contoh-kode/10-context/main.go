package main

import (
	"context"
	"errors"
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

	// Query yang sengaja lambat (SLEEP 3 detik) dengan batas waktu 1 detik.
	ctx, cancel := context.WithTimeout(context.Background(), 1*time.Second)
	defer cancel()

	mulai := time.Now()
	var hasil int
	err = db.QueryRowContext(ctx, `SELECT SLEEP(3)`).Scan(&hasil)
	durasi := time.Since(mulai).Round(100 * time.Millisecond)

	switch {
	case errors.Is(err, context.DeadlineExceeded):
		fmt.Printf("Query dibatalkan karena timeout setelah %v\n", durasi)
	case err != nil:
		// Pada sebagian versi driver/MySQL, pembatalan muncul sebagai error lain.
		fmt.Printf("Query berhenti setelah %v dengan error: %v\n", durasi, err)
	default:
		fmt.Println("Query selesai, hasil:", hasil)
	}
}
