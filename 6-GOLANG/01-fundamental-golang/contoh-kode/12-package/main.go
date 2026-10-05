package main

import (
	"fmt"

	// Path import = nama module (dari go.mod) + folder paket
	"fundamental/contoh-kode/12-package/matematika"
)

func main() {
	fmt.Println(matematika.Tambah(2, 3))
	fmt.Println(matematika.Faktorial(5))
	fmt.Println(matematika.Pi)

	k := matematika.NewKalkulator("Casio")
	k.Catat("2+3=5")
	k.Catat("5!=120")
	fmt.Println(k.Nama, k.Riwayat())

	// Baris di bawah akan ERROR kompilasi karena tidak diekspor:
	// matematika.faktorialRekursif(3)
	// fmt.Println(k.riwayat)
	// fmt.Println(matematika.batasMaks)
}
