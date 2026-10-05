# 05 - Fungsi

## Tujuan Pembelajaran
- Membuat fungsi dengan parameter dan nilai balik.
- Memakai multiple return, named return, dan variadic.
- Memahami fungsi sebagai nilai, closure, dan rekursi.

## Konsep
```go
func namaFungsi(param1 tipe, param2 tipe) tipeBalik {
    return nilai
}
```

| Fitur | Penjelasan |
|---|---|
| Multiple return | `func bagi(a, b float64) (float64, error)`; fondasi **error handling** di Go |
| Named return | Nilai balik diberi nama; `return` kosong mengembalikannya (pakai seperlunya) |
| Variadic | `func jumlah(n ...int)`; di dalam fungsi `n` bertipe `[]int`. Panggil dengan `jumlah(slice...)` |
| Fungsi = nilai | Dapat disimpan di variabel, dikirim sebagai argumen, dikembalikan |
| Closure | Fungsi yang menangkap variabel dari lingkup luarnya |
| `main` / `init` | `init()` otomatis dijalankan sebelum `main` |

**Go selalu pass-by-value**: argumen disalin. (Untuk mengubah aslinya, pakai pointer: materi 08.)

## Langkah
Buat `contoh-kode/05-fungsi/main.go`:

```go
// File: contoh-kode/05-fungsi/main.go
package main

import (
	"errors"
	"fmt"
)

// Fungsi dasar: parameter bertipe, mengembalikan satu nilai
func tambah(a int, b int) int {
	return a + b
}

// Tipe yang sama boleh digabung
func kali(a, b int) int { return a * b }

// Multiple return value: ciri khas Go
func bagi(a, b float64) (float64, error) {
	if b == 0 {
		return 0, errors.New("tidak bisa membagi dengan nol")
	}
	return a / b, nil
}

// Named return: nilai balik diberi nama, "return" kosong mengembalikannya
func luasKeliling(p, l float64) (luas, keliling float64) {
	luas = p * l
	keliling = 2 * (p + l)
	return
}

// Variadic: jumlah argumen bebas (di dalam fungsi bertipe slice)
func jumlah(angka ...int) int {
	total := 0
	for _, n := range angka {
		total += n
	}
	return total
}

// Fungsi sebagai nilai (first-class function)
func terapkan(angka []int, f func(int) int) []int {
	hasil := make([]int, 0, len(angka))
	for _, n := range angka {
		hasil = append(hasil, f(n))
	}
	return hasil
}

// Closure: fungsi yang "mengingat" variabel di sekitarnya
func pencacah() func() int {
	hitung := 0
	return func() int {
		hitung++
		return hitung
	}
}

// Rekursi
func faktorial(n int) int {
	if n <= 1 {
		return 1
	}
	return n * faktorial(n-1)
}

func main() {
	// [1] Fungsi dasar
	fmt.Println("========== [1] Fungsi dasar ==========")
	fmt.Println(tambah(2, 3), kali(4, 5))

	// [2] Multiple return value (nilai dan error)
	fmt.Println("========== [2] Multiple return value (nilai dan error) ==========")
	hasil, err := bagi(10, 4)
	fmt.Println(hasil, err)
	_, err = bagi(1, 0)
	fmt.Println("error:", err)

	// [3] Named return
	fmt.Println("========== [3] Named return ==========")
	l, k := luasKeliling(3, 4)
	fmt.Println("luas:", l, "keliling:", k)

	// [4] Variadic
	fmt.Println("========== [4] Variadic ==========")
	fmt.Println(jumlah(), jumlah(1, 2, 3))
	angka := []int{4, 5, 6}
	fmt.Println(jumlah(angka...)) // "membuka" slice menjadi argumen

	// [5] Fungsi anonim sebagai argumen
	fmt.Println("========== [5] Fungsi anonim sebagai argumen ==========")
	// Fungsi anonim
	kuadrat := func(x int) int { return x * x }
	fmt.Println(terapkan([]int{1, 2, 3}, kuadrat))
	fmt.Println(terapkan([]int{1, 2, 3}, func(x int) int { return x + 100 }))

	// [6] Closure
	fmt.Println("========== [6] Closure ==========")
	c1 := pencacah()
	c2 := pencacah()
	fmt.Println(c1(), c1(), c1(), "|", c2()) // 1 2 3 | 1

	// [7] Rekursi
	fmt.Println("========== [7] Rekursi ==========")
	fmt.Println("5! =", faktorial(5))

	// [8] Fungsi anonim langsung dipanggil
	fmt.Println("========== [8] Fungsi anonim langsung dipanggil ==========")
	func(pesan string) {
		fmt.Println("IIFE:", pesan)
	}("halo")
}
```

Jalankan:
```powershell
go run ./contoh-kode/05-fungsi
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Fungsi dasar ==========
5 20
========== [2] Multiple return value (nilai dan error) ==========
2.5 <nil>
error: tidak bisa membagi dengan nol
========== [3] Named return ==========
luas: 12 keliling: 14
========== [4] Variadic ==========
0 6
15
========== [5] Fungsi anonim sebagai argumen ==========
[1 4 9]
[101 102 103]
========== [6] Closure ==========
1 2 3 | 1
========== [7] Rekursi ==========
5! = 120
========== [8] Fungsi anonim langsung dipanggil ==========
IIFE: halo
```

## Penjelasan Penting
- `pencacah()` mengembalikan fungsi yang "mengingat" `hitung`. `c1` dan `c2` masing-masing punya `hitung` sendiri.
- Pola `hasil, err := fungsi()` + `if err != nil` akan Anda tulis ribuan kali.
- Fungsi diberi nama huruf besar (`Tambah`) jika ingin diekspor ke paket lain.

## Kesalahan Umum
| Masalah | Penyebab |
|---|---|
| `missing return` | Ada jalur yang tidak mengembalikan nilai |
| `assignment mismatch: 1 variable but bagi returns 2 values` | Nilai balik tidak ditangkap semua. Pakai `_` |
| Rekursi tanpa kondisi berhenti | `stack overflow` |

## Latihan
1. Fungsi `MinMax(angka ...int) (min, max int)`.
2. Fungsi `Fibonacci(n int) int` (rekursif), lalu versi iteratif. Bandingkan untuk n=40.
3. Fungsi `Filter(angka []int, f func(int) bool) []int`.
4. Buat closure `pengali(faktor int) func(int) int`.

## Catatan Instruktur
Estimasi 75 menit. Tekankan multiple return -> error, karena menjadi dasar materi 10.
