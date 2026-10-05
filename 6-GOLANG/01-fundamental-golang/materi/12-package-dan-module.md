# 12 - Package dan Module

## Tujuan Pembelajaran
- Memahami beda **package** dan **module**.
- Membuat package sendiri dan mengimpornya.
- Memahami aturan ekspor (huruf besar), `internal/`, dan `go get`.

## Konsep

| Istilah | Arti |
|---|---|
| **Package** | Satu folder berisi file `.go` dengan nama `package` yang sama. Unit pemakaian ulang kode |
| **Module** | Kumpulan package yang diberi versi, ditandai file `go.mod`. Biasanya = satu repository |
| **Import path** | `nama-module` + `/` + `folder-package`, mis. `fundamental/contoh-kode/12-package/matematika` |

Aturan:
1. Semua file dalam satu folder harus memakai nama `package` yang sama.
2. **Huruf besar** di awal nama (`Tambah`, `Kalkulator`, `Pi`) = **diekspor**. Huruf kecil = **privat** untuk package itu.
3. Tidak boleh ada siklus import (A mengimpor B dan B mengimpor A).
4. Folder bernama **`internal/`**: package di dalamnya hanya dapat diimpor oleh kode di dalam induk `internal/` tersebut.
5. `package main` + `func main()` = program; package lain = *library*.

### Struktur project umum
```
myapp/
├── go.mod                    module myapp
├── cmd/api/main.go           package main
└── internal/
    ├── config/config.go      package config
    ├── handlers/...          package handlers
    └── repository/...        package repository
```
Import: `import "myapp/internal/config"`.

### Dependency eksternal
```powershell
go get github.com/gin-gonic/gin     # menambah ke go.mod & go.sum
go mod tidy                          # rapikan
go list -m all                       # daftar dependency
go get github.com/gin-gonic/gin@v1.10.0   # versi tertentu
```
- `go.mod`: daftar dependency + versi minimum.
- `go.sum`: checksum untuk memastikan isi dependency tidak berubah. **Ikut di-commit.**

## Langkah
Contoh memakai dua file di dua folder.

`contoh-kode/12-package/matematika/matematika.go`:

```go
// File: contoh-kode/12-package/matematika/matematika.go
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
```

`contoh-kode/12-package/main.go`:

```go
// File: contoh-kode/12-package/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/12-package
```

Lihat dokumentasi package otomatis:
```powershell
go doc ./contoh-kode/12-package/matematika
go doc ./contoh-kode/12-package/matematika Faktorial
```

## Output yang Diharapkan
```
5
120
3.14159
Casio [2+3=5 5!=120]
```

## Penjelasan Penting
- Import path memakai **nama module** (`fundamental`, dari `go.mod`), bukan nama folder induk.
- Field `riwayat` privat; paket lain hanya bisa mengaksesnya lewat method (`Catat`, `Riwayat`). Ini bentuk **enkapsulasi** di Go.
- Hapus tanda komentar pada baris yang ditandai ERROR untuk melihat pesan kompilasi: `undefined (cannot refer to unexported ...)`.

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `package xxx is not in std` | Import path salah; cek nama module di `go.mod` |
| `import cycle not allowed` | Dua package saling mengimpor; ekstrak kode bersama ke package ketiga |
| `cannot refer to unexported name` | Nama berhuruf kecil dipakai dari package lain |
| `found packages a and b in /folder` | Dua nama `package` berbeda dalam satu folder |
| `use of internal package not allowed` | Mengimpor `internal/` dari luar induknya |

## Latihan
1. Buat package `konversi` berisi `CelsiusKeFahrenheit` dan `KmKeMil`, impor dari `main`.
2. Buat package `internal/validasi` berisi `Email(string) bool`.
3. Tambahkan dependency eksternal `github.com/google/uuid` dan cetak satu UUID.

## Catatan Instruktur
Estimasi 60 menit. Siapkan peta konsep: module > package > file.
