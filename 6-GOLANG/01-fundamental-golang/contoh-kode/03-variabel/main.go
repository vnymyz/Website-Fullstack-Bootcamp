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
