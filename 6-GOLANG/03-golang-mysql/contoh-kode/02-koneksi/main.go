package main

import (
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

	var versi string
	if err := db.QueryRow("SELECT VERSION()").Scan(&versi); err != nil {
		log.Fatal(err)
	}
	fmt.Println("Berhasil terhubung! Versi MySQL:", versi)
}
