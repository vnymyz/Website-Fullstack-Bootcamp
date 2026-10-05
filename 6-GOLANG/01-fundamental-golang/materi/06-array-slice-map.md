# 06 - Array, Slice, dan Map

## Tujuan Pembelajaran
- Membedakan array dan slice.
- Memakai `append`, `copy`, `make`, slicing, dan memahami `len` vs `cap`.
- Memakai map, termasuk "comma ok" dan `delete`.

## Konsep

| Struktur | Ukuran | Sifat |
|---|---|---|
| **Array** `[3]int` | Tetap (bagian dari tipe) | **Nilai**: menyalin array = menyalin semua isi. Jarang dipakai langsung |
| **Slice** `[]int` | Dinamis | "Jendela" ke array di baliknya. **Paling sering dipakai** |
| **Map** `map[K]V` | Dinamis | Pasangan key-value, pencarian cepat. Urutan iterasi **acak** |

### Slice di balik layar
Slice = (pointer ke array, `len`, `cap`).
- `len`: jumlah elemen saat ini.
- `cap`: kapasitas sebelum perlu alokasi baru.
- `append` yang melebihi `cap` membuat array baru (kapasitas biasanya berlipat) lalu menyalin isi.
- **Pemotongan (`s[a:b]`) berbagi memori** dengan aslinya. Mengubah salah satu mengubah yang lain. Gunakan `copy` untuk salinan mandiri.

```
asli:     [1 2 3 4 5]
potongan:    [2 3]      <- menunjuk ke memori yang sama
```

### Aturan Map
- Key harus *comparable* (string, angka, bool, struct sederhana; **bukan** slice/map).
- Membaca key yang tidak ada mengembalikan zero value. Gunakan `nilai, ada := m[k]`.
- Map `nil` boleh dibaca tetapi **menulis ke nil map = panic**. Buat dengan `make` atau literal.
- Map **tidak aman** dipakai bersamaan oleh beberapa goroutine tanpa Mutex.

## Langkah
Buat `contoh-kode/06-koleksi/main.go`:

```go
// File: contoh-kode/06-koleksi/main.go
package main

import (
	"fmt"
	"maps"
	"slices"
	"sort"
)

func main() {
	// [1] Array
	fmt.Println("========== [1] Array ==========")
	var arr [3]int // [0 0 0]
	arr[0] = 10
	arr2 := [3]string{"a", "b", "c"}
	arr3 := [...]int{1, 2, 3, 4} // ukuran dihitung otomatis
	fmt.Println(arr, arr2, arr3, len(arr3))

	// [2] Array adalah nilai (disalin)
	fmt.Println("========== [2] Array adalah nilai (disalin) ==========")
	// Array adalah NILAI: menyalin array = menyalin seluruh isi
	salin := arr
	salin[0] = 99
	fmt.Println("asli:", arr, "salinan:", salin)

	// [3] Slice dan append
	fmt.Println("========== [3] Slice dan append ==========")
	angka := []int{10, 20, 30}
	angka = append(angka, 40, 50)
	fmt.Println(angka, "len:", len(angka), "cap:", cap(angka))

	// [4] make dan kapasitas bertambah
	fmt.Println("========== [4] make dan kapasitas bertambah ==========")
	// make(tipe, panjang, kapasitas)
	s := make([]int, 0, 5)
	for i := 0; i < 7; i++ {
		s = append(s, i)
		fmt.Printf("len=%d cap=%d\n", len(s), cap(s)) // kapasitas bertambah otomatis
	}

	// [5] Slicing
	fmt.Println("========== [5] Slicing ==========")
	// Slicing [awal:akhir) -> akhir tidak ikut
	fmt.Println(angka[1:3], angka[:2], angka[3:])

	// [6] Slice berbagi memori
	fmt.Println("========== [6] Slice berbagi memori ==========")
	// Slice berbagi memori dengan array di baliknya!
	asli := []int{1, 2, 3, 4, 5}
	potongan := asli[1:3]
	potongan[0] = 999
	fmt.Println("asli ikut berubah:", asli)

	// [7] Salinan dengan copy
	fmt.Println("========== [7] Salinan dengan copy ==========")
	// Salinan sungguhan dengan copy
	tiruan := make([]int, len(asli))
	copy(tiruan, asli)
	tiruan[0] = -1
	fmt.Println("asli:", asli, "tiruan:", tiruan)

	// [8] Hapus elemen
	fmt.Println("========== [8] Hapus elemen ==========")
	// Hapus elemen indeks 1
	data := []string{"a", "b", "c", "d"}
	data = append(data[:1], data[2:]...)
	fmt.Println(data)

	// [9] Slice nil
	fmt.Println("========== [9] Slice nil ==========")
	// Slice nil vs kosong
	var nilSlice []int
	fmt.Println(nilSlice == nil, len(nilSlice)) // true 0 (aman dipakai append)

	// [10] Paket slices
	fmt.Println("========== [10] Paket slices ==========")
	// Paket slices (Go 1.21+)
	u := []int{5, 2, 8, 1}
	slices.Sort(u)
	idx, ketemu := slices.BinarySearch(u, 5)
	fmt.Println(u, slices.Contains(u, 8), slices.Max(u), idx, ketemu, slices.Index(u, 8))

	// [11] Slice 2 dimensi
	fmt.Println("========== [11] Slice 2 dimensi ==========")
	grid := [][]int{{1, 2}, {3, 4}}
	fmt.Println(grid[1][0])

	// [12] Map dasar
	fmt.Println("========== [12] Map dasar ==========")
	umur := map[string]int{"Ani": 20, "Budi": 22}
	umur["Cici"] = 19 // tambah
	umur["Ani"] = 21  // ubah
	delete(umur, "Budi")
	fmt.Println(umur, len(umur))

	// [13] Cek key dengan comma ok
	fmt.Println("========== [13] Cek key dengan comma ok ==========")
	// Cek keberadaan key dengan "comma ok"
	if nilai, ada := umur["Dodi"]; !ada {
		fmt.Println("Dodi tidak ada, nilai default:", nilai)
	}

	// [14] nil map
	fmt.Println("========== [14] nil map ==========")
	// map kosong WAJIB dibuat dengan make atau literal sebelum ditulis
	var peta map[string]int // nil
	fmt.Println(peta["x"])  // membaca nil map aman -> 0
	// peta["x"] = 1        // menulis ke nil map -> PANIC
	peta = make(map[string]int)
	peta["x"] = 1

	// [15] Iterasi map berurutan
	fmt.Println("========== [15] Iterasi map berurutan ==========")
	// Urutan iterasi map ACAK. Urutkan key bila perlu urutan.
	kunci := make([]string, 0, len(umur))
	for k := range umur {
		kunci = append(kunci, k)
	}
	sort.Strings(kunci)
	for _, k := range kunci {
		fmt.Println(k, "=>", umur[k])
	}

	// [16] Paket maps
	fmt.Println("========== [16] Paket maps ==========")
	// Paket maps (Go 1.21+)
	k2 := slices.Sorted(maps.Keys(umur))
	fmt.Println(k2)

	// [17] Contoh: frekuensi huruf
	fmt.Println("========== [17] Contoh: frekuensi huruf ==========")
	// Contoh nyata: menghitung frekuensi huruf
	frek := map[rune]int{}
	for _, r := range "banana" {
		frek[r]++
	}
	fmt.Println(frek['a'], frek['n'], frek['b'])
}
```

Jalankan:
```powershell
go run ./contoh-kode/06-koleksi
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Array ==========
[10 0 0] [a b c] [1 2 3 4] 4
========== [2] Array adalah nilai (disalin) ==========
asli: [10 0 0] salinan: [99 0 0]
========== [3] Slice dan append ==========
[10 20 30 40 50] len: 5 cap: 6
========== [4] make dan kapasitas bertambah ==========
len=1 cap=5
len=2 cap=5
len=3 cap=5
len=4 cap=5
len=5 cap=5
len=6 cap=10
len=7 cap=10
========== [5] Slicing ==========
[20 30] [10 20] [40 50]
========== [6] Slice berbagi memori ==========
asli ikut berubah: [1 999 3 4 5]
========== [7] Salinan dengan copy ==========
asli: [1 999 3 4 5] tiruan: [-1 999 3 4 5]
========== [8] Hapus elemen ==========
[a c d]
========== [9] Slice nil ==========
true 0
========== [10] Paket slices ==========
[1 2 5 8] true 8 2 true 3
========== [11] Slice 2 dimensi ==========
3
========== [12] Map dasar ==========
map[Ani:21 Cici:19] 2
========== [13] Cek key dengan comma ok ==========
Dodi tidak ada, nilai default: 0
========== [14] nil map ==========
0
========== [15] Iterasi map berurutan ==========
Ani => 21
Cici => 19
========== [16] Paket maps ==========
[Ani Cici]
========== [17] Contoh: frekuensi huruf ==========
3 2 1
```
Nilai `cap` setelah `append` dapat berbeda antar versi Go.

## Kesalahan Umum
| Masalah | Penyebab |
|---|---|
| `panic: index out of range [5] with length 3` | Akses indeks di luar `len` |
| `panic: assignment to entry in nil map` | Menulis ke map yang belum di-`make` |
| Data "aneh" berubah setelah slicing | Slice berbagi memori; pakai `copy` |
| Lupa menampung hasil `append` | Tulis `s = append(s, x)` |
| Urutan map berbeda tiap dijalankan | Memang acak; urutkan key dahulu |

## Latihan
1. Balikkan urutan slice tanpa membuat slice baru.
2. Hitung frekuensi kata pada kalimat memakai map.
3. Gabungkan dua slice dan hilangkan duplikat.
4. Buat fungsi `Grup(kata []string) map[int][]string` yang mengelompokkan kata berdasarkan panjangnya.

## Catatan Instruktur
Estimasi 90 menit. Gambarkan slice sebagai tiga kotak (pointer, len, cap) dan tunjukkan memori bersama.
