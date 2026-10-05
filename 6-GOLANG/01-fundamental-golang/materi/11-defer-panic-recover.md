# 11 - Defer, Panic, dan Recover

## Tujuan Pembelajaran
- Memakai `defer` untuk membersihkan sumber daya.
- Memahami `panic` dan kapan terjadi.
- Memakai `recover` untuk menangkap panic.

## Konsep

### defer
`defer` menunda pemanggilan fungsi sampai fungsi pembungkusnya **selesai** (return, error, atau panic).
- Beberapa `defer` berjalan **LIFO** (yang terakhir didaftarkan, pertama dijalankan).
- **Argumen** defer dievaluasi saat `defer` ditulis, bukan saat dijalankan.
- Pemakaian utama: `defer f.Close()`, `defer mu.Unlock()`, `defer rows.Close()`, `defer tx.Rollback()`, `defer cancel()`.

### panic
Kondisi fatal: alur normal berhenti, semua `defer` dijalankan, lalu program **crash** dengan *stack trace*. Terjadi otomatis pada:
- akses indeks di luar batas
- dereference pointer `nil`
- menulis ke `nil` map
- pembagian bilangan bulat dengan nol
- type assertion gagal tanpa `, ok`

### recover
`recover()` menghentikan panic dan mengembalikan nilainya, **hanya jika dipanggil langsung di dalam fungsi yang di-defer**.

### Kapan memakai apa?
| Situasi | Pakai |
|---|---|
| Error yang wajar (file tidak ada, input salah, DB gagal) | **return error** |
| Kesalahan programmer / kondisi mustahil | `panic` |
| Server harus tetap hidup walau satu request panic | `recover` di middleware (Gin: `gin.Recovery()`) |

## Langkah
Buat `contoh-kode/11-defer-panic/main.go`:

```go
// File: contoh-kode/11-defer-panic/main.go
package main

import (
	"fmt"
	"os"
)

// defer: tunda eksekusi sampai fungsi selesai. Beberapa defer berjalan LIFO (terbalik).
func contohDefer() {
	fmt.Println("mulai")
	defer fmt.Println("defer 1")
	defer fmt.Println("defer 2")
	defer fmt.Println("defer 3")
	fmt.Println("selesai")
	// Output: mulai, selesai, defer 3, defer 2, defer 1
}

// Pemakaian utama defer: membersihkan sumber daya (file, koneksi, lock)
func tulisFile(nama, isi string) error {
	f, err := os.Create(nama)
	if err != nil {
		return err
	}
	defer f.Close() // dijamin dijalankan, di jalur manapun fungsi keluar

	_, err = f.WriteString(isi)
	return err
}

// Argumen defer dihitung SAAT defer didaftarkan
func argumenDefer() {
	x := 1
	defer fmt.Println("nilai x saat defer didaftarkan:", x)
	x = 100
}

// panic: kondisi fatal. Menghentikan alur normal, menjalankan semua defer, lalu program crash.
// recover: menangkap panic (hanya bekerja di dalam fungsi yang di-defer).
func amanBagi(a, b int) (hasil int, err error) {
	defer func() {
		if r := recover(); r != nil {
			err = fmt.Errorf("pulih dari panic: %v", r)
		}
	}()
	return a / b, nil // b == 0 -> panic: integer divide by zero
}

func akses(slice []int, i int) (v int, err error) {
	defer func() {
		if r := recover(); r != nil {
			err = fmt.Errorf("index di luar batas: %v", r)
		}
	}()
	return slice[i], nil
}

func main() {
	// [1] defer berjalan terbalik (LIFO)
	fmt.Println("========== [1] defer berjalan terbalik (LIFO) ==========")
	contohDefer()

	// [2] defer untuk menutup file dan argumen defer
	fmt.Println("========== [2] defer untuk menutup file dan argumen defer ==========")

	if err := tulisFile("tmp-defer.txt", "halo"); err != nil {
		fmt.Println("error:", err)
	}
	os.Remove("tmp-defer.txt")

	argumenDefer()

	// [3] recover menangkap panic
	fmt.Println("========== [3] recover menangkap panic ==========")

	fmt.Println(amanBagi(10, 2))
	fmt.Println(amanBagi(10, 0))
	fmt.Println(akses([]int{1, 2, 3}, 5))

	// [4] panic (program sengaja berhenti)
	fmt.Println("========== [4] panic (program sengaja berhenti) ==========")
	// Kapan memakai panic? Hampir tidak pernah untuk error biasa (pakai return error).
	// Hanya untuk kesalahan programmer atau kondisi yang tidak mungkin pulih
	// (mis. konfigurasi wajib hilang saat startup).
	defer fmt.Println("defer di main tetap berjalan sebelum crash")
	panic("sesuatu yang fatal terjadi")
}
```

Jalankan:
```powershell
go run ./contoh-kode/11-defer-panic
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] defer berjalan terbalik (LIFO) ==========
mulai
selesai
defer 3
defer 2
defer 1
========== [2] defer untuk menutup file dan argumen defer ==========
nilai x saat defer didaftarkan: 1
========== [3] recover menangkap panic ==========
5 <nil>
0 pulih dari panic: runtime error: integer divide by zero
0 index di luar batas: runtime error: index out of range [5] with length 3
========== [4] panic (program sengaja berhenti) ==========
defer di main tetap berjalan sebelum crash
panic: sesuatu yang fatal terjadi

goroutine 1 [running]:
main.main()
	...
exit status 2
```
Program **sengaja crash** di akhir untuk memperlihatkan perilaku `panic`. Itu normal.

## Kesalahan Umum
| Masalah | Penyebab |
|---|---|
| `defer` di dalam loop menumpuk | Bungkus isi loop dalam fungsi tersendiri |
| `recover()` tidak menangkap | Tidak dipanggil langsung di fungsi yang di-defer |
| Mengabaikan error `Close()` pada file tulis | Pada penulisan file, periksa error `Close()` |
| `defer` untuk file yang gagal dibuka | Letakkan `defer` **setelah** memeriksa `err` |

## Latihan
1. Tulis fungsi `SalinFile(src, dst string) error` dengan `defer` untuk kedua file.
2. Buat fungsi `JalankanAman(f func()) (err error)` yang menangkap panic dan mengubahnya menjadi error.
3. Prediksi urutan output tiga `defer` yang mencetak nilai variabel loop.

## Catatan Instruktur
Estimasi 45 menit. Tunjukkan `gin.Recovery()` sebagai contoh `recover` di dunia nyata.
