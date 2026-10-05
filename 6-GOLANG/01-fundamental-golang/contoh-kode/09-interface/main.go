package main

import (
	"fmt"
	"math"
)

// Interface = kumpulan method. Tipe apa pun yang punya method tsb otomatis memenuhinya
// (implisit, tanpa kata "implements").
type Bentuk interface {
	Luas() float64
	Keliling() float64
}

type Persegi struct{ Sisi float64 }
type Lingkaran struct{ R float64 }

func (p Persegi) Luas() float64       { return p.Sisi * p.Sisi }
func (p Persegi) Keliling() float64   { return 4 * p.Sisi }
func (l Lingkaran) Luas() float64     { return math.Pi * l.R * l.R }
func (l Lingkaran) Keliling() float64 { return 2 * math.Pi * l.R }

// Fungsi menerima interface -> bisa dipakai oleh semua tipe yang cocok (polimorfisme)
func cetak(b Bentuk) {
	fmt.Printf("%T | luas %.2f | keliling %.2f\n", b, b.Luas(), b.Keliling())
}

// Method String() memenuhi interface fmt.Stringer -> mengatur tampilan saat dicetak
func (p Persegi) String() string {
	return fmt.Sprintf("Persegi(%.0f)", p.Sisi)
}

// Interface kecil lebih baik. Contoh yang realistis: penyimpanan data
type Penyimpan interface {
	Simpan(kunci, nilai string) error
}

type Memori struct{ data map[string]string }

func (m *Memori) Simpan(k, v string) error {
	m.data[k] = v
	return nil
}

type Log struct{}

func (Log) Simpan(k, v string) error {
	fmt.Printf("[LOG] %s=%s\n", k, v)
	return nil
}

func jalankan(p Penyimpan) {
	p.Simpan("nama", "Budi")
}

// Interface kosong / any: dapat menampung nilai tipe apa pun
func deskripsi(v any) string {
	switch x := v.(type) { // type switch
	case int:
		return fmt.Sprintf("int %d", x)
	case string:
		return "string " + x
	case []int:
		return fmt.Sprintf("slice int panjang %d", len(x))
	case Bentuk:
		return fmt.Sprintf("bentuk luas %.1f", x.Luas())
	case nil:
		return "nil"
	default:
		return fmt.Sprintf("tipe lain %T", x)
	}
}

func main() {
	// [1] Interface Bentuk (polimorfisme)
	fmt.Println("========== [1] Interface Bentuk (polimorfisme) ==========")
	bentuk := []Bentuk{Persegi{4}, Lingkaran{2}}
	for _, b := range bentuk {
		cetak(b)
	}

	// [2] Method String() mengatur tampilan
	fmt.Println("========== [2] Method String() mengatur tampilan ==========")
	fmt.Println(Persegi{3}) // memakai String()

	// [3] Satu interface, banyak implementasi
	fmt.Println("========== [3] Satu interface, banyak implementasi ==========")
	// Satu fungsi, implementasi berbeda
	jalankan(&Memori{data: map[string]string{}})
	jalankan(Log{})

	// [4] any dan type switch
	fmt.Println("========== [4] any dan type switch ==========")
	// any + type switch
	fmt.Println(deskripsi(42))
	fmt.Println(deskripsi("halo"))
	fmt.Println(deskripsi([]int{1, 2}))
	fmt.Println(deskripsi(Lingkaran{1}))
	fmt.Println(deskripsi(3.5))
	fmt.Println(deskripsi(nil))

	// [5] Type assertion
	fmt.Println("========== [5] Type assertion ==========")
	var b Bentuk = Persegi{2}
	if p, ok := b.(Persegi); ok {
		fmt.Println("ini Persegi, sisi:", p.Sisi)
	}
	if _, ok := b.(Lingkaran); !ok {
		fmt.Println("bukan Lingkaran")
	}
}
