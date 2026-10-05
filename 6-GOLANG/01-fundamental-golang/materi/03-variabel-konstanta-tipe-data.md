# 03 - Variabel, Konstanta, dan Tipe Data

## Tujuan Pembelajaran
- Mendeklarasikan variabel dengan `var` dan `:=`.
- Memahami zero value, konstanta, dan `iota`.
- Mengenal tipe data dasar dan konversi tipe.

## Konsep

### Cara deklarasi
| Bentuk | Contoh | Catatan |
|---|---|---|
| `var nama tipe = nilai` | `var umur int = 25` | Lengkap |
| `var nama = nilai` | `var nama = "Sari"` | Tipe ditebak |
| `nama := nilai` | `kota := "Bandung"` | Singkat, **hanya di dalam fungsi** |
| `const` | `const Pi = 3.14` | Nilai tetap, ditentukan saat kompilasi |

### Zero value
Variabel yang belum diisi **tidak pernah "kosong tak tentu"**; ia berisi zero value:
| Tipe | Zero value |
|---|---|
| angka | `0` |
| `string` | `""` |
| `bool` | `false` |
| pointer, slice, map, interface, fungsi | `nil` |

### Tipe dasar
| Kategori | Tipe |
|---|---|
| Bilangan bulat | `int`, `int8`, `int16`, `int32`, `int64`, `uint`, `uint8` (= `byte`), ... |
| Desimal | `float32`, `float64` (pakai `float64` sebagai default) |
| Teks | `string` (immutable, UTF-8), `rune` (= `int32`, satu karakter Unicode) |
| Logika | `bool` |
| Kompleks | `complex64`, `complex128` (jarang dipakai) |

`int` mengikuti arsitektur (64 bit di komputer modern). Gunakan `int` kecuali ada alasan khusus.

### Konversi harus eksplisit
Go **tidak** mengubah tipe otomatis: `int` + `float64` = error. Tulis `float64(x)`.

### Uang: jangan pakai float
`0.1 + 0.2` pada float = `0.30000000000000004`. Untuk uang simpan dalam satuan terkecil (`int64` rupiah/sen) atau pakai library `decimal`.

## Langkah
Buat `contoh-kode/03-variabel/main.go`:

```go
// File: contoh-kode/03-variabel/main.go
package main

import "fmt"

// Konstanta tingkat package
const Pi = 3.14159

// iota: penghitung otomatis untuk konstanta berurutan (mirip enum)
type Hari int

const (
	Senin  Hari = iota // 0
	Selasa             // 1
	Rabu               // 2
)

const (
	_  = iota
	KB = 1 << (10 * iota) // 1024
	MB                    // 1048576
)

// Variabel tingkat package (tidak boleh pakai :=)
var namaAplikasi = "Kelas Go"

func main() {
	// [1] Deklarasi variabel
	fmt.Println("========== [1] Deklarasi variabel ==========")
	var umur int = 25
	var nama = "Sari" // tipe ditebak otomatis: string
	kota := "Bandung" // := deklarasi singkat (hanya di dalam fungsi)
	var a, b = 3, 4   // beberapa sekaligus

	fmt.Println(umur, nama, kota, a, b, namaAplikasi)

	// [2] Zero value
	fmt.Println("========== [2] Zero value ==========")
	// Zero value: nilai awal jika tidak diisi
	var i int
	var f float64
	var s string
	var ok bool
	var p *int
	fmt.Printf("int=%d float=%.1f string=%q bool=%t pointer=%v\n", i, f, s, ok, p)

	// [3] Tipe data dasar
	fmt.Println("========== [3] Tipe data dasar ==========")
	var (
		bulat    int     = 42
		kecil    int8    = 127 // -128..127
		tanpaMin uint    = 7   // tidak negatif
		desimal  float64 = 3.14
		kompleks         = 2 + 3i
		huruf    rune    = 'G'  // karakter Unicode (int32)
		byteVal  byte    = 'a'  // uint8
		teks             = "Go" // string (tidak bisa diubah per karakter)
		benar            = true
	)
	fmt.Println(bulat, kecil, tanpaMin, desimal, kompleks, huruf, byteVal, teks, benar)
	fmt.Printf("huruf sebagai karakter: %c, tipe: %T\n", huruf, huruf)

	// [4] Konversi tipe harus eksplisit
	fmt.Println("========== [4] Konversi tipe harus eksplisit ==========")
	// Konversi tipe harus eksplisit (Go tidak melakukan konversi otomatis)
	x := 10
	y := 3.5
	hasil := float64(x) * y
	fmt.Println("10 * 3.5 =", hasil)
	fmt.Println("Pembagian bulat 10/3 =", 10/3, "sisa:", 10%3)
	pecahan := 9.99
	fmt.Println("float -> int memotong:", int(pecahan)) // int(9.99) langsung = error kompilasi (konstanta)

	// [5] String: byte vs karakter
	fmt.Println("========== [5] String: byte vs karakter ==========")
	// String
	kata := "Halo, 世界"
	fmt.Println("len (byte):", len(kata), "| jumlah karakter:", len([]rune(kata)))
	fmt.Println("Gabung:", "Go"+" "+"Lang")

	// [6] Konstanta dan iota
	fmt.Println("========== [6] Konstanta dan iota ==========")
	// Konstanta
	fmt.Println("Pi:", Pi, "| Selasa:", Selasa, "| 1 MB =", MB, "byte")

	// [7] Mengabaikan nilai dengan _
	fmt.Println("========== [7] Mengabaikan nilai dengan _ ==========")
	// Variabel yang tidak dipakai = error kompilasi. Gunakan _ untuk mengabaikan.
	_, sisa := 17/5, 17%5
	fmt.Println("sisa:", sisa)
}
```

Jalankan:
```powershell
go run ./contoh-kode/03-variabel
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Deklarasi variabel ==========
25 Sari Bandung 3 4 Kelas Go
========== [2] Zero value ==========
int=0 float=0.0 string="" bool=false pointer=<nil>
========== [3] Tipe data dasar ==========
42 127 7 3.14 (2+3i) 71 97 Go true
huruf sebagai karakter: G, tipe: int32
========== [4] Konversi tipe harus eksplisit ==========
10 * 3.5 = 35
Pembagian bulat 10/3 = 3 sisa: 1
float -> int memotong: 9
========== [5] String: byte vs karakter ==========
len (byte): 12 | jumlah karakter: 8
Gabung: Go Lang
========== [6] Konstanta dan iota ==========
Pi: 3.14159 | Selasa: 1 | 1 MB = 1048576 byte
========== [7] Mengabaikan nilai dengan _ ==========
sisa: 2
```

## Penjelasan Penting
- `len("Halo, 世界")` = 12 **byte**, tetapi hanya 8 **karakter**: karakter Cina memakai 3 byte tiap huruf. Gunakan `[]rune(s)` untuk menghitung karakter.
- `10/3` pada bilangan bulat menghasilkan `3` (pembagian bulat).
- `int(9.99)` langsung (konstanta) menyebabkan error kompilasi; lewat variabel hasilnya `9` (dipotong, bukan dibulatkan).
- `iota` mulai dari 0 dan bertambah per baris dalam blok `const`.

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `declared and not used` | Variabel tidak dipakai |
| `non-declaration statement outside function body` | `:=` dipakai di luar fungsi |
| `mismatched types int and float64` | Operasi dua tipe berbeda tanpa konversi |
| `no new variables on left side of :=` | `:=` untuk variabel yang sudah ada; pakai `=` |
| `cannot use "5" (untyped string constant) as int value` | Salah tipe |

## Latihan
1. Hitung luas dan keliling lingkaran dengan jari-jari 7.5 (pakai konstanta `Pi`).
2. Konversi suhu Celsius ke Fahrenheit: `F = C * 9/5 + 32` (hati-hati pembagian bulat!).
3. Buat konstanta `iota` untuk tingkat `Rendah`, `Sedang`, `Tinggi`.

## Catatan Instruktur
Estimasi 60 menit. Demo perbedaan `len` byte vs rune pada emoji.
