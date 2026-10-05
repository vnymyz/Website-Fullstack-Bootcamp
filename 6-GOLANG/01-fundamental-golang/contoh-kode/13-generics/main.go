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
