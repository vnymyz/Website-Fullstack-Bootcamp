package main

import "fmt"

func main() {
	// [1] if / else
	fmt.Println("========== [1] if / else ==========")
	nilai := 78
	if nilai >= 90 {
		fmt.Println("A")
	} else if nilai >= 75 {
		fmt.Println("B")
	} else {
		fmt.Println("C")
	}

	// [2] if dengan statement singkat
	fmt.Println("========== [2] if dengan statement singkat ==========")
	// if dengan statement singkat (variabel hanya hidup di blok if)
	if sisa := nilai % 2; sisa == 0 {
		fmt.Println(nilai, "genap")
	} else {
		fmt.Println(nilai, "ganjil")
	}

	// [3] switch dengan nilai
	fmt.Println("========== [3] switch dengan nilai ==========")
	hari := 3
	switch hari {
	case 1:
		fmt.Println("Senin")
	case 2, 3: // beberapa nilai
		fmt.Println("Selasa atau Rabu")
	default:
		fmt.Println("Hari lain")
	}

	// [4] switch tanpa ekspresi
	fmt.Println("========== [4] switch tanpa ekspresi ==========")
	// switch tanpa ekspresi = pengganti if-else panjang
	suhu := 31
	switch {
	case suhu < 20:
		fmt.Println("Dingin")
	case suhu < 30:
		fmt.Println("Sejuk")
	default:
		fmt.Println("Panas")
	}
	// Go tidak butuh "break": case otomatis berhenti. Gunakan "fallthrough" bila ingin lanjut.

	// [5] for gaya klasik
	fmt.Println("========== [5] for gaya klasik ==========")
	for i := 1; i <= 3; i++ { // gaya klasik
		fmt.Print(i, " ")
	}
	fmt.Println()

	// [6] for gaya while
	fmt.Println("========== [6] for gaya while ==========")
	n := 1
	for n < 50 { // gaya while
		n *= 2
	}
	fmt.Println("n =", n)

	// [7] for tak berhingga + break/continue
	fmt.Println("========== [7] for tak berhingga + break/continue ==========")
	hitung := 0
	for { // tak berhingga, hentikan dengan break
		hitung++
		if hitung == 2 {
			continue // lewati sisa blok, lanjut putaran berikut
		}
		if hitung > 4 {
			break
		}
		fmt.Print("putaran ", hitung, "; ")
	}
	fmt.Println()

	// [8] range pada bilangan bulat
	fmt.Println("========== [8] range pada bilangan bulat ==========")
	for i := range 3 { // Go 1.22+: range pada bilangan bulat
		fmt.Print("i=", i, " ")
	}
	fmt.Println()

	// [9] range pada slice
	fmt.Println("========== [9] range pada slice ==========")
	buah := []string{"apel", "jeruk", "mangga"}
	for indeks, nama := range buah {
		fmt.Println(indeks, nama)
	}
	for _, nama := range buah { // abaikan indeks dengan _
		fmt.Print(nama, " ")
	}
	fmt.Println()

	// [10] range pada string (rune)
	fmt.Println("========== [10] range pada string (rune) ==========")
	for i, r := range "Go♥" { // pada string, yang diiterasi adalah rune
		fmt.Printf("%d:%c ", i, r)
	}
	fmt.Println()

	// [11] label untuk keluar dari loop bersarang
	fmt.Println("========== [11] label untuk keluar dari loop bersarang ==========")
cari:
	for baris := 1; baris <= 3; baris++ {
		for kolom := 1; kolom <= 3; kolom++ {
			if baris*kolom == 4 {
				fmt.Println("ketemu di", baris, kolom)
				break cari
			}
		}
	}

	// [12] FizzBuzz
	fmt.Println("========== [12] FizzBuzz ==========")
	for i := 1; i <= 15; i++ {
		switch {
		case i%15 == 0:
			fmt.Print("FizzBuzz ")
		case i%3 == 0:
			fmt.Print("Fizz ")
		case i%5 == 0:
			fmt.Print("Buzz ")
		default:
			fmt.Print(i, " ")
		}
	}
	fmt.Println()
}
