package main

import (
	"fmt"
	"sync"
	"time"
)

func kerja(id int) {
	time.Sleep(100 * time.Millisecond)
	fmt.Println("selesai kerja", id)
}

// Worker pool: sejumlah goroutine tetap memproses antrean pekerjaan
func worker(id int, pekerjaan <-chan int, hasil chan<- int, wg *sync.WaitGroup) {
	defer wg.Done()
	for n := range pekerjaan {
		hasil <- n * n
	}
	_ = id
}

func main() {
	// [1] Goroutine + WaitGroup
	fmt.Println("========== [1] Goroutine + WaitGroup ==========")
	var wg sync.WaitGroup
	for i := 1; i <= 3; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			kerja(i) // Go 1.22+: tiap putaran punya salinan i sendiri
		}()
	}
	wg.Wait() // tunggu semua selesai
	fmt.Println("semua goroutine selesai")

	// [2] Channel tanpa buffer
	fmt.Println("========== [2] Channel tanpa buffer ==========")
	// "Don't communicate by sharing memory; share memory by communicating."
	ch := make(chan string)
	go func() {
		ch <- "pesan dari goroutine" // kirim (menunggu sampai ada yang menerima)
	}()
	fmt.Println(<-ch) // terima

	// [3] Buffered channel
	fmt.Println("========== [3] Buffered channel ==========")
	// Buffered channel: boleh menampung beberapa nilai tanpa penerima
	buf := make(chan int, 3)
	buf <- 1
	buf <- 2
	fmt.Println(len(buf), cap(buf))
	close(buf) // penutup memberi tanda "tidak ada data lagi"
	for v := range buf {
		fmt.Print(v, " ")
	}
	fmt.Println()

	// [4] Mutex melindungi data bersama
	fmt.Println("========== [4] Mutex melindungi data bersama ==========")
	var mu sync.Mutex
	counter := 0
	var wg2 sync.WaitGroup
	for i := 0; i < 1000; i++ {
		wg2.Add(1)
		go func() {
			defer wg2.Done()
			mu.Lock()
			counter++
			mu.Unlock()
		}()
	}
	wg2.Wait()
	fmt.Println("counter (harus 1000):", counter)
	// Tanpa Mutex hasilnya tidak pasti (data race). Cek dengan: go run -race .

	// [5] select + timeout
	fmt.Println("========== [5] select + timeout ==========")
	lambat := make(chan string)
	go func() {
		time.Sleep(300 * time.Millisecond)
		lambat <- "hasil"
	}()
	select {
	case h := <-lambat:
		fmt.Println(h)
	case <-time.After(100 * time.Millisecond):
		fmt.Println("timeout! terlalu lama")
	}

	// [6] Worker pool
	fmt.Println("========== [6] Worker pool ==========")
	pekerjaan := make(chan int, 10)
	hasil := make(chan int, 10)
	var wg3 sync.WaitGroup
	for w := 1; w <= 3; w++ {
		wg3.Add(1)
		go worker(w, pekerjaan, hasil, &wg3)
	}
	for n := 1; n <= 6; n++ {
		pekerjaan <- n
	}
	close(pekerjaan)
	wg3.Wait()
	close(hasil)

	total := 0
	for h := range hasil {
		total += h
	}
	fmt.Println("jumlah kuadrat 1..6 =", total) // 91
}
