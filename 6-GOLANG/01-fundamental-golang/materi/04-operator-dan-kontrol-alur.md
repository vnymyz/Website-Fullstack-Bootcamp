# 04 - Operator dan Kontrol Alur

## Tujuan Pembelajaran
- Memakai operator aritmatika, perbandingan, dan logika.
- Menulis `if`, `switch`, dan `for`.
- Memakai `range`, `break`, `continue`, dan label.

## Konsep

### Operator
| Jenis | Operator |
|---|---|
| Aritmatika | `+ - * / %` |
| Penugasan | `= += -= *= /= %=`, `++`, `--` (hanya sebagai pernyataan, bukan ekspresi) |
| Perbandingan | `== != < > <= >=` |
| Logika | `&&` (dan), `\|\|` (atau), `!` (bukan) |
| Bitwise | `& \| ^ << >>` |

### Struktur kontrol
- **`if`**: tanpa tanda kurung pada kondisi; kurung kurawal wajib. Boleh ada statement singkat: `if x := f(); x > 0 { }`.
- **`switch`**: tidak perlu `break`; bisa tanpa ekspresi; satu case boleh banyak nilai.
- **`for`**: **satu-satunya** perulangan di Go. Ada 4 bentuk: klasik, seperti `while`, tak berhingga, dan `range`.
- **`range`**: iterasi slice, array, string (per rune), map, channel, dan (Go 1.22+) bilangan bulat.
- **`break`/`continue`**: keluar dari / lewati putaran. **Label** memungkinkan keluar dari loop bersarang.

## Langkah
Buat `contoh-kode/04-kontrol-alur/main.go`:

```go
// File: contoh-kode/04-kontrol-alur/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/04-kontrol-alur
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] if / else ==========
B
========== [2] if dengan statement singkat ==========
78 genap
========== [3] switch dengan nilai ==========
Selasa atau Rabu
========== [4] switch tanpa ekspresi ==========
Panas
========== [5] for gaya klasik ==========
1 2 3 
========== [6] for gaya while ==========
n = 64
========== [7] for tak berhingga + break/continue ==========
putaran 1; putaran 3; putaran 4; 
========== [8] range pada bilangan bulat ==========
i=0 i=1 i=2 
========== [9] range pada slice ==========
0 apel
1 jeruk
2 mangga
apel jeruk mangga 
========== [10] range pada string (rune) ==========
0:G 1:o 2:♥ 
========== [11] label untuk keluar dari loop bersarang ==========
ketemu di 2 2
========== [12] FizzBuzz ==========
1 2 Fizz 4 Buzz Fizz 7 8 Fizz Buzz 11 Fizz 13 14 FizzBuzz 
```

## Penjelasan Penting
- Pada `range` string, indeks yang dicetak adalah **posisi byte**, bukan nomor karakter. Karakter `♥` memakai 3 byte; seandainya ada huruf setelahnya, indeksnya akan melompat dari 2 ke 5.
- `switch` tanpa ekspresi dievaluasi dari atas; case pertama yang benar dijalankan.
- Urutan iterasi `range` pada **map acak** (dibahas di materi 06).

## Kesalahan Umum
| Masalah | Penyebab |
|---|---|
| `else` di baris baru -> error | Harus `} else {` pada baris yang sama |
| `i++` dipakai dalam ekspresi (`x := i++`) | Tidak diperbolehkan di Go |
| Loop tak berhenti | Lupa mengubah variabel kondisi |
| `unused variable` pada `for i, v := range` | Pakai `_` untuk yang tak dipakai |

## Latihan
1. Cetak tabel perkalian 1-10.
2. Cari semua bilangan prima di bawah 100.
3. Tebak angka: program memilih angka acak 1-100 (`math/rand`) dan pengguna menebak lewat `fmt.Scan` sampai benar.
4. Hitung jumlah huruf vokal dalam sebuah kalimat.

## Catatan Instruktur
Estimasi 60-75 menit. FizzBuzz adalah latihan klasik; minta siswa menulisnya tanpa melihat contoh.
