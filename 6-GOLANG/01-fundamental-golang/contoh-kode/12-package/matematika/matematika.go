// Package matematika berisi fungsi hitung sederhana.
// Komentar di atas "package" menjadi dokumentasi paket (lihat: go doc ./contoh-kode/12-package/matematika).
package matematika

// Nama berhuruf BESAR di awal = diekspor (bisa dipakai paket lain).
// Nama berhuruf kecil = privat (hanya di dalam paket ini).

// Pi adalah konstanta yang diekspor.
const Pi = 3.14159

// batasMaks tidak diekspor.
const batasMaks = 1000

// Tambah menjumlahkan dua bilangan.
func Tambah(a, b int) int {
	return a + b
}

// Faktorial menghitung n!. Mengembalikan -1 jika n negatif atau terlalu besar.
func Faktorial(n int) int {
	if n < 0 || n > 20 {
		return -1
	}
	return faktorialRekursif(n)
}

// faktorialRekursif adalah helper privat.
func faktorialRekursif(n int) int {
	if n <= 1 {
		return 1
	}
	return n * faktorialRekursif(n-1)
}

// Kalkulator adalah struct yang diekspor, tetapi field "riwayat" bersifat privat.
type Kalkulator struct {
	Nama    string
	riwayat []string
}

// NewKalkulator adalah constructor.
func NewKalkulator(nama string) *Kalkulator {
	return &Kalkulator{Nama: nama}
}

// Catat menyimpan catatan ke riwayat (akses lewat method, bukan langsung ke field).
func (k *Kalkulator) Catat(s string) {
	if len(k.riwayat) < batasMaks {
		k.riwayat = append(k.riwayat, s)
	}
}

// Riwayat mengembalikan salinan riwayat.
func (k *Kalkulator) Riwayat() []string {
	salin := make([]string, len(k.riwayat))
	copy(salin, k.riwayat)
	return salin
}
