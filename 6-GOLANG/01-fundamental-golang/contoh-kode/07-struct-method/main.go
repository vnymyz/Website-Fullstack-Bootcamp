package main

import "fmt"

// Struct: kumpulan field. Go tidak punya class; struct + method adalah gantinya.
type Siswa struct {
	Nama  string
	Umur  int
	Nilai []float64
}

// Method dengan VALUE receiver: bekerja pada SALINAN
func (s Siswa) Perkenalan() string {
	return fmt.Sprintf("Saya %s, umur %d", s.Nama, s.Umur)
}

// Method dengan POINTER receiver: bisa MENGUBAH struct asli
func (s *Siswa) TambahNilai(n float64) {
	s.Nilai = append(s.Nilai, n)
}

func (s Siswa) RataRata() float64 {
	if len(s.Nilai) == 0 {
		return 0
	}
	total := 0.0
	for _, n := range s.Nilai {
		total += n
	}
	return total / float64(len(s.Nilai))
}

// Method yang salah: value receiver tidak mengubah aslinya
func (s Siswa) UbahNamaSalah(baru string) {
	s.Nama = baru
}

// Constructor: fungsi biasa berawalan "New" (konvensi)
func NewSiswa(nama string, umur int) *Siswa {
	return &Siswa{Nama: nama, Umur: umur}
}

// Embedding (komposisi): Go memilih komposisi daripada pewarisan
type Orang struct {
	Nama string
}

func (o Orang) Sapa() string { return "Halo, " + o.Nama }

type Guru struct {
	Orang // embedded: Guru otomatis punya field Nama dan method Sapa
	Mapel string
}

// Struct dengan tag (dipakai untuk JSON, dibahas di materi 2)
type Produk struct {
	ID    int     `json:"id"`
	Nama  string  `json:"nama"`
	Harga float64 `json:"harga"`
}

func main() {
	// [1] Cara membuat struct
	fmt.Println("========== [1] Cara membuat struct ==========")
	s1 := Siswa{Nama: "Ani", Umur: 20}     // dengan nama field (disarankan)
	s2 := Siswa{"Budi", 21, []float64{80}} // berurutan (rapuh jika struct berubah)
	var s3 Siswa                           // zero value
	s4 := NewSiswa("Cici", 19)             // pointer
	fmt.Println(s1, s2, s3, *s4)

	// [2] Method: pointer receiver vs value receiver
	fmt.Println("========== [2] Method: pointer receiver vs value receiver ==========")
	// Akses & ubah field
	s1.Umur = 22
	s1.TambahNilai(90) // Go otomatis memakai &s1
	s1.TambahNilai(70)
	fmt.Println(s1.Perkenalan(), "| rata-rata:", s1.RataRata())

	s1.UbahNamaSalah("Zzz")
	fmt.Println("Nama setelah UbahNamaSalah:", s1.Nama, "(tidak berubah)")

	// [3] Pointer ke struct
	fmt.Println("========== [3] Pointer ke struct ==========")
	// Pointer ke struct: akses field langsung tanpa tanda *
	s4.Umur = 30
	fmt.Println(s4.Nama, s4.Umur)

	// [4] Membandingkan struct
	fmt.Println("========== [4] Membandingkan struct ==========")
	// Struct bisa dibandingkan jika semua field comparable
	p1 := Produk{1, "Pulpen", 3000}
	p2 := Produk{1, "Pulpen", 3000}
	fmt.Println("p1 == p2:", p1 == p2)

	// [5] Embedding
	fmt.Println("========== [5] Embedding ==========")
	g := Guru{Orang: Orang{Nama: "Pak Joko"}, Mapel: "Matematika"}
	fmt.Println(g.Nama, "|", g.Sapa(), "|", g.Mapel)

	// [6] Struct anonim
	fmt.Println("========== [6] Struct anonim ==========")
	// Struct anonim (sekali pakai)
	titik := struct{ X, Y int }{3, 4}
	fmt.Println(titik)

	// [7] Slice dan map berisi struct
	fmt.Println("========== [7] Slice dan map berisi struct ==========")
	daftar := []Produk{{1, "A", 100}, {2, "B", 200}}
	for _, p := range daftar {
		fmt.Printf("%d %s Rp%.0f\n", p.ID, p.Nama, p.Harga)
	}
	katalog := map[int]Produk{1: {1, "A", 100}}
	fmt.Println(katalog[1].Nama)
}
