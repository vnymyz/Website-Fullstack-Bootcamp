package main

import (
	"fmt"
	"log"
	"sync"

	"golangmysql/internal/koneksi"
)

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	// Jalankan 20 query bersamaan. Pool membatasi koneksi terbuka maksimal 10.
	var wg sync.WaitGroup
	for i := 1; i <= 20; i++ {
		wg.Add(1)
		go func(n int) {
			defer wg.Done()
			var hasil int
			if err := db.QueryRow("SELECT SLEEP(0.2) + ?", n).Scan(&hasil); err != nil {
				log.Println("error:", err)
				return
			}
		}(i)
	}
	wg.Wait()

	s := db.Stats()
	fmt.Println("MaxOpenConnections :", s.MaxOpenConnections)
	fmt.Println("OpenConnections    :", s.OpenConnections)
	fmt.Println("InUse              :", s.InUse)
	fmt.Println("Idle               :", s.Idle)
	fmt.Println("WaitCount          :", s.WaitCount)
}
