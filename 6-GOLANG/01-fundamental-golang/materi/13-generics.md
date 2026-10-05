# 13 - Generics

## Tujuan Pembelajaran
- Memahami masalah yang dipecahkan generics.
- Menulis fungsi dan tipe generik dengan type parameter dan constraint.
- Mengetahui kapan sebaiknya **tidak** memakai generics.

## Konsep
Sebelum Go 1.18, fungsi seperti `Max` harus ditulis berulang (`MaxInt`, `MaxFloat`, `MaxString`) atau memakai `any` yang tidak aman tipe.

**Generics** memungkinkan satu fungsi/tipe bekerja untuk banyak tipe, tetap aman saat kompilasi:

```go
func Max[T cmp.Ordered](a, b T) T { ... }
```
- `[T cmp.Ordered]` = **type parameter** `T` dengan **constraint** `cmp.Ordered` (bisa dibandingkan dengan `<`, `>`).
- Go biasanya menyimpulkan `T` dari argumen: `Max(3, 7)`.

### Constraint
| Constraint | Arti |
|---|---|
| `any` | Tipe apa pun |
| `comparable` | Bisa `==` (cocok untuk key map) |
| `cmp.Ordered` | Bisa `<`, `>` (angka dan string) |
| Buatan sendiri `interface{ ~int \| ~float64 }` | Gabungan tipe. `~` berarti "termasuk tipe turunan" |

### Tipe generik
```go
type Stack[T any] struct { items []T }
func (s *Stack[T]) Push(v T) { ... }
```

### Kapan memakai?
| Pakai generics | Jangan |
|---|---|
| Fungsi koleksi (`Map`, `Filter`, `Reduce`) | Cukup satu tipe konkret |
| Struktur data (Stack, Queue, Set, cache) | Perilaku berbeda per tipe -> pakai **interface** |
| Menghilangkan duplikasi kode identik | Menambah kerumitan tanpa manfaat |

Paket standar `slices` dan `maps` (Go 1.21+) sudah menyediakan banyak fungsi generik siap pakai: `slices.Sort`, `slices.Contains`, `slices.Max`, `maps.Keys`, dll.

## Langkah
Buat `contoh-kode/13-generics/main.go`:

```go
// File: contoh-kode/13-generics/main.go
package main

import (
	"cmp"
	"fmt"
)

// Tanpa generics: harus menulis fungsi terpisah untuk tiap tipe (MaxInt, MaxFloat, ...).
// Dengan generics: satu fungsi untuk banyak tipe.

// [T cmp.Ordered] = T boleh tipe apa pun yang bisa dibandingkan dengan < >
func Max[T cmp.Ordered](a, b T) T {
	if a > b {
		return a
	}
	return b
}

// Constraint buatan sendiri: gabungan tipe
type Angka interface {
	~int | ~int64 | ~float64
}

func Jumlah[T Angka](data []T) T {
	var total T
	for _, d := range data {
		total += d
	}
	return total
}

// Fungsi higher-order generik: Map, Filter, Reduce
func Map[T, U any](data []T, f func(T) U) []U {
	hasil := make([]U, 0, len(data))
	for _, d := range data {
		hasil = append(hasil, f(d))
	}
	return hasil
}

func Filter[T any](data []T, f func(T) bool) []T {
	var hasil []T
	for _, d := range data {
		if f(d) {
			hasil = append(hasil, d)
		}
	}
	return hasil
}

func Reduce[T, A any](data []T, awal A, f func(A, T) A) A {
	acc := awal
	for _, d := range data {
		acc = f(acc, d)
	}
	return acc
}

// Tipe generik: Stack yang bisa menampung tipe apa pun
type Stack[T any] struct {
	items []T
}

func (s *Stack[T]) Push(v T) { s.items = append(s.items, v) }

func (s *Stack[T]) Pop() (T, bool) {
	var nol T
	if len(s.items) == 0 {
		return nol, false
	}
	v := s.items[len(s.items)-1]
	s.items = s.items[:len(s.items)-1]
	return v, true
}

func (s *Stack[T]) Len() int { return len(s.items) }

// Pasangan key-value generik
type Pair[K comparable, V any] struct {
	Key   K
	Value V
}

type Derajat float64

func main() {
	// [1] Fungsi generik Max
	fmt.Println("========== [1] Fungsi generik Max ==========")
	fmt.Println(Max(3, 7), Max(2.5, 1.5), Max("apel", "jeruk"))

	// [2] Constraint buatan sendiri (Jumlah)
	fmt.Println("========== [2] Constraint buatan sendiri (Jumlah) ==========")
	fmt.Println(Jumlah([]int{1, 2, 3}), Jumlah([]float64{1.5, 2.5}))
	fmt.Println(Jumlah([]Derajat{10, 20})) // ~float64 mengizinkan tipe turunan

	// [3] Map, Filter, Reduce
	fmt.Println("========== [3] Map, Filter, Reduce ==========")
	angka := []int{1, 2, 3, 4, 5, 6}
	kuadrat := Map(angka, func(n int) int { return n * n })
	genap := Filter(angka, func(n int) bool { return n%2 == 0 })
	teks := Map(angka, func(n int) string { return fmt.Sprintf("#%d", n) })
	total := Reduce(angka, 0, func(acc, n int) int { return acc + n })
	fmt.Println(kuadrat, genap, teks, total)

	// [4] Tipe generik: Stack
	fmt.Println("========== [4] Tipe generik: Stack ==========")
	s := &Stack[string]{}
	s.Push("a")
	s.Push("b")
	v, _ := s.Pop()
	fmt.Println(v, s.Len())

	// [5] Tipe generik: Pair
	fmt.Println("========== [5] Tipe generik: Pair ==========")
	p := Pair[string, int]{"umur", 20}
	fmt.Printf("%+v\n", p)

	// Tips: pakai generics hanya bila benar-benar menghilangkan duplikasi.
	// Untuk perilaku yang berbeda per tipe, gunakan interface biasa.
}
```

Jalankan:
```powershell
go run ./contoh-kode/13-generics
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Fungsi generik Max ==========
7 2.5 jeruk
========== [2] Constraint buatan sendiri (Jumlah) ==========
6 4
30
========== [3] Map, Filter, Reduce ==========
[1 4 9 16 25 36] [2 4 6] [#1 #2 #3 #4 #5 #6] 21
========== [4] Tipe generik: Stack ==========
b 1
========== [5] Tipe generik: Pair ==========
{Key:umur Value:20}
```

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `cannot use generic function Max without instantiation` | Tipe tidak bisa disimpulkan; tulis `Max[int]` |
| `invalid operation: operator > not defined on a` | Constraint kurang tepat (`any` tidak mengizinkan `>`) |
| Method generik | Go tidak mengizinkan method memiliki type parameter sendiri; gunakan fungsi biasa |

## Latihan
1. Buat `Contains[T comparable](data []T, nilai T) bool`.
2. Buat `Keys[K comparable, V any](m map[K]V) []K`.
3. Buat tipe `Set[T comparable]` berbasis `map[T]struct{}` dengan `Add`, `Has`, `Len`.
4. Buat `Queue[T any]` (`Enqueue`, `Dequeue`).

## Catatan Instruktur
Estimasi 60 menit. Topik ini bisa dipersingkat; yang penting siswa bisa **membaca** kode generik di library.
