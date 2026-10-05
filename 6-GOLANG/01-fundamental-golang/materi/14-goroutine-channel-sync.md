# 14 - Goroutine, Channel, dan Sinkronisasi

## Tujuan Pembelajaran
- Menjalankan fungsi secara konkuren dengan `go`.
- Berkomunikasi antar goroutine memakai channel.
- Memakai `sync.WaitGroup`, `sync.Mutex`, dan `select`.
- Mengenali *data race*.

## Konsep

### Concurrency vs parallelism
- **Concurrency**: mengelola banyak pekerjaan yang berjalan **bergantian/tumpang tindih**.
- **Parallelism**: benar-benar berjalan **bersamaan** di banyak core.
Go menyediakan alat untuk concurrency; runtime Go memanfaatkan parallelism bila ada banyak core.

### Goroutine
Fungsi yang berjalan konkuren. Ditulis dengan `go namaFungsi()`. Sangat ringan (mulai ~2 KB stack), ribuan goroutine itu wajar.
> Jika `main` selesai, **seluruh program berhenti** walau goroutine lain belum selesai. Karena itu perlu menunggu (WaitGroup/channel).

### Alat sinkronisasi
| Alat | Fungsi |
|---|---|
| `sync.WaitGroup` | Menunggu sekelompok goroutine selesai (`Add`, `Done`, `Wait`) |
| `sync.Mutex` | Hanya satu goroutine pada satu waktu mengakses data (`Lock`/`Unlock`) |
| `sync.RWMutex` | Banyak pembaca, satu penulis |
| `sync/atomic` | Operasi angka atomik (counter) |
| **Channel** | Pipa untuk mengirim nilai antar goroutine |
| `select` | Menunggu beberapa channel sekaligus (dengan timeout) |
| `context` | Pembatalan dan batas waktu |

### Channel
```go
ch := make(chan int)        // unbuffered: pengirim menunggu penerima
buf := make(chan int, 3)    // buffered: boleh menampung 3 nilai
ch <- 5                     // kirim
v := <-ch                   // terima
close(ch)                   // tutup (hanya pengirim yang menutup)
for v := range ch { ... }   // baca sampai ditutup
```
Arah channel pada parameter: `chan<- int` (hanya kirim), `<-chan int` (hanya terima).

Semboyan Go: *"Jangan berkomunikasi dengan berbagi memori; bagikan memori dengan berkomunikasi."*

### Data race
Dua goroutine mengakses variabel yang sama, minimal satu menulis, tanpa sinkronisasi -> hasil tak terduga. Deteksi:
```powershell
go run -race .
go test -race ./...
```

## Langkah
Buat `contoh-kode/14-goroutine/main.go`:

```go
// File: contoh-kode/14-goroutine/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/14-goroutine
go run -race ./contoh-kode/14-goroutine
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Goroutine + WaitGroup ==========
selesai kerja 1
selesai kerja 3
selesai kerja 2
semua goroutine selesai
========== [2] Channel tanpa buffer ==========
pesan dari goroutine
========== [3] Buffered channel ==========
2 3
1 2 
========== [4] Mutex melindungi data bersama ==========
counter (harus 1000): 1000
========== [5] select + timeout ==========
timeout! terlalu lama
========== [6] Worker pool ==========
jumlah kuadrat 1..6 = 91
```
Urutan baris "selesai kerja N" **bisa berbeda** setiap dijalankan karena goroutine bersaing; itu bukan bug.

## Kesalahan Umum
| Masalah | Penyebab |
|---|---|
| `fatal error: all goroutines are asleep - deadlock!` | Menunggu channel yang tidak pernah diisi/dibaca |
| Program selesai sebelum goroutine | Lupa `wg.Wait()` |
| `panic: send on closed channel` | Mengirim ke channel yang sudah ditutup |
| `panic: close of closed channel` | Menutup dua kali |
| Hasil counter berubah-ubah | Data race; tambahkan Mutex atau `atomic` |
| Goroutine bocor (*leak*) | Goroutine menunggu selamanya; sediakan jalur keluar (`context`) |

## Latihan
1. Ambil 5 URL secara bersamaan dengan `net/http` dan cetak status masing-masing (pakai WaitGroup).
2. Ubah `counter` memakai `sync/atomic` (`atomic.Int64`).
3. Buat pipeline: generator angka -> kuadrat -> cetak, memakai tiga goroutine dan dua channel.
4. Tambahkan `context.WithTimeout` pada worker pool.

## Catatan Instruktur
Estimasi 90 menit. Cukup dasar; tidak perlu semua pola. Tunjukkan hasil `-race` pada versi counter tanpa Mutex.
