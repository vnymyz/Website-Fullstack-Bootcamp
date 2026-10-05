package main

import "fmt"

// Go memanggil fungsi dengan PASS BY VALUE: argumen disalin.
func tambahSatuSalah(n int) {
	n++ // hanya mengubah salinan
}

func tambahSatu(n *int) {
	*n++ // mengubah nilai asli lewat alamatnya
}

type Akun struct {
	Saldo int
}

func setorSalah(a Akun, jumlah int) { a.Saldo += jumlah } // salinan
func setor(a *Akun, jumlah int)     { a.Saldo += jumlah } // asli

// Pointer sebagai nilai "opsional" (nil = tidak ada)
func cariUmur(nama string) *int {
	data := map[string]int{"Ani": 20}
	if u, ok := data[nama]; ok {
		return &u
	}
	return nil
}

func main() {
	// [1] Dasar pointer: & dan *
	fmt.Println("========== [1] Dasar pointer: & dan * ==========")
	x := 10
	p := &x // & = ambil alamat; p bertipe *int
	fmt.Println("x:", x, "| p menyimpan alamat | *p:", *p)

	*p = 99 // * = ikuti alamat, ubah nilai di sana
	fmt.Println("x setelah *p = 99:", x)

	// [2] Pass by value vs pointer (angka)
	fmt.Println("========== [2] Pass by value vs pointer (angka) ==========")
	a := 5
	tambahSatuSalah(a)
	fmt.Println("setelah tambahSatuSalah:", a)
	tambahSatu(&a)
	fmt.Println("setelah tambahSatu:", a)

	// [3] Pass by value vs pointer (struct)
	fmt.Println("========== [3] Pass by value vs pointer (struct) ==========")
	akun := Akun{Saldo: 100}
	setorSalah(akun, 50)
	fmt.Println("setelah setorSalah:", akun.Saldo)
	setor(&akun, 50)
	fmt.Println("setelah setor:", akun.Saldo)

	// [4] new(T)
	fmt.Println("========== [4] new(T) ==========")
	// new(T) membuat zero value dan mengembalikan pointer
	q := new(int)
	*q = 7
	fmt.Println(*q)

	// [5] Pointer nil
	fmt.Println("========== [5] Pointer nil ==========")
	// nil pointer: dereference = PANIC
	if u := cariUmur("Zaki"); u == nil {
		fmt.Println("Zaki tidak ditemukan (pointer nil)")
	}
	if u := cariUmur("Ani"); u != nil {
		fmt.Println("Umur Ani:", *u)
	}

	// [6] Slice dan map berperilaku seperti referensi
	fmt.Println("========== [6] Slice dan map berperilaku seperti referensi ==========")
	// Slice, map, dan channel sudah berperilaku seperti "referensi":
	s := []int{1, 2, 3}
	ubahSlice(s)
	fmt.Println("slice setelah ubahSlice:", s)

	m := map[string]int{"a": 1}
	ubahMap(m)
	fmt.Println("map setelah ubahMap:", m)

	// [7] Dua pointer ke objek yang sama
	fmt.Println("========== [7] Dua pointer ke objek yang sama ==========")
	b1 := &Akun{Saldo: 1}
	b2 := b1
	b2.Saldo = 500
	fmt.Println("b1.Saldo:", b1.Saldo)
}

func ubahSlice(s []int)        { s[0] = 100 }
func ubahMap(m map[string]int) { m["b"] = 2 }
