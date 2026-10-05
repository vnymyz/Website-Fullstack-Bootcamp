# 03 - Connection Pool

## Tujuan Pembelajaran
- Memahami mengapa koneksi database dikelola dalam pool.
- Mengatur `SetMaxOpenConns`, `SetMaxIdleConns`, `SetConnMaxLifetime`.
- Membaca `db.Stats()`.

## Konsep
Membuka koneksi ke database itu **mahal** (handshake TCP, autentikasi). Pool menyimpan beberapa koneksi yang siap dipakai ulang.

```
goroutine 1 --\
goroutine 2 ---+--> [ POOL: koneksi A, B, C ... ] --> MySQL
goroutine 3 --/
```

| Pengaturan | Arti | Saran awal |
|---|---|---|
| `SetMaxOpenConns(n)` | Batas total koneksi terbuka. Kelebihan permintaan **menunggu** | 10-25 |
| `SetMaxIdleConns(n)` | Koneksi menganggur yang disimpan siap pakai | 5-10 (<= MaxOpen) |
| `SetConnMaxLifetime(d)` | Umur maksimum koneksi sebelum diganti | 5 menit |
| `SetConnMaxIdleTime(d)` | Waktu menganggur maksimum | 1-5 menit |

Tanpa batas (`MaxOpenConns` default = tak terbatas) lonjakan trafik dapat menghabiskan koneksi MySQL (`Too many connections`).

## Langkah
Pengaturan pool sudah ada di `internal/koneksi/koneksi.go` (dari materi 02). Sekarang kita amati efeknya.

Buat `contoh-kode/03-pool/main.go`:

```go
// File: contoh-kode/03-pool/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/03-pool
```

## Penjelasan
- 20 goroutine meminta koneksi bersamaan, tetapi pool hanya mengizinkan 10 terbuka. Sisanya menunggu giliran (`WaitCount` > 0).
- `SELECT SLEEP(0.2)` membuat query butuh 0,2 detik, sehingga antrean terlihat.
- Setelah selesai, koneksi kembali ke pool dan berstatus **Idle**.

## Output yang Diharapkan (kira-kira)
```
MaxOpenConnections : 10
OpenConnections    : 5
InUse              : 0
Idle               : 5
WaitCount          : 10
```
Angka persisnya dapat berbeda; yang penting `OpenConnections <= 10` dan `WaitCount > 0`.

## Kesalahan Umum
- Memanggil `sql.Open` di dalam setiap handler -> membuat pool baru tiap request (bocor koneksi).
- Lupa `rows.Close()` -> koneksi tidak kembali ke pool; aplikasi lama-lama "hang".

## Latihan
Ubah `SetMaxOpenConns` menjadi 2 dan bandingkan `WaitCount` serta lama eksekusi program.

## Catatan Instruktur
Estimasi 30 menit. Analogi: loket bank. Teller (koneksi) terbatas, nasabah (request) mengantre.
