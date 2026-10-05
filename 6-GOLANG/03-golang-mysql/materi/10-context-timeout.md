# 10 - Context dan Timeout

## Tujuan Pembelajaran
- Memahami `context.Context` dalam konteks database.
- Memberi batas waktu pada query.
- Membatalkan query ketika client terputus.

## Konsep
`context.Context` membawa **batas waktu** dan **sinyal pembatalan** melintasi pemanggilan fungsi.

Mengapa penting? Tanpa timeout, satu query lambat dapat menahan koneksi pool selamanya. Saat banyak request mengalami hal itu, seluruh aplikasi macet.

| Pembuat context | Fungsi |
|---|---|
| `context.Background()` | Context kosong, titik awal |
| `context.WithTimeout(ctx, d)` | Batal otomatis setelah durasi `d` |
| `context.WithCancel(ctx)` | Batal manual lewat fungsi `cancel()` |

Pada server HTTP, `c.Request.Context()` (Gin) otomatis dibatalkan jika browser menutup koneksi. Meneruskannya ke query = query ikut berhenti. Itulah yang kita lakukan di project materi 4.

**Aturan:** selalu panggil `defer cancel()` agar sumber daya dibersihkan.

## Langkah
Buat `contoh-kode/10-context/main.go`:

```go
// File: contoh-kode/10-context/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/10-context
```

## Output yang Diharapkan
```
Query dibatalkan karena timeout setelah 1s
```
(Pada beberapa versi driver/MySQL pesan error bisa berbeda; program tetap berhenti ~1 detik, bukan 3 detik.)

## Penjelasan
`SELECT SLEEP(3)` membuat MySQL menunggu 3 detik, tetapi context kita hanya memberi waktu 1 detik, sehingga driver membatalkan query.

## Kesalahan Umum
- Memakai versi tanpa context (`db.Query`) di kode server: tidak bisa dibatalkan.
- Timeout terlalu pendek untuk query berat (laporan). Sesuaikan per kasus.

## Latihan
Ubah timeout menjadi 5 detik dan amati bahwa query selesai normal.

## Catatan Instruktur
Estimasi 30 menit. Hubungkan dengan pengalaman pengguna: halaman yang "loading terus".
