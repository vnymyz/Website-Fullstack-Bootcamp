# 08 - Pointer

## Tujuan Pembelajaran
- Memahami alamat memori, `&` dan `*`.
- Memahami pass-by-value dan kapan memakai pointer.
- Menghindari `nil pointer dereference`.

## Konsep
**Pointer** = variabel yang menyimpan **alamat memori** variabel lain.

```
 x  [ 10 ]  alamat: 0xc000012345
 p  [ 0xc000012345 ]      p := &x
```

| Simbol | Arti | Contoh |
|---|---|---|
| `&x` | Alamat dari `x` | `p := &x` |
| `*p` | Nilai yang ditunjuk `p` (dereference) | `*p = 99` mengubah `x` |
| `*int` | Tipe "pointer ke int" | `var p *int` |
| `new(T)` | Alokasi zero value, kembalikan `*T` | `q := new(int)` |

### Mengapa perlu pointer?
1. **Mengubah nilai asli** di dalam fungsi (karena Go pass-by-value).
2. **Efisiensi**: tidak menyalin struct besar.
3. **Opsional**: `nil` berarti "tidak ada nilai" (mis. kolom NULL di database).

### Yang sudah "seperti referensi"
Slice, map, channel, fungsi, dan interface menyimpan referensi internal; mengirimnya ke fungsi dan mengubah isinya akan mempengaruhi aslinya. (Namun menugaskan slice baru ke parameter tidak mengubah slice di pemanggil.)

### Beda dengan C
Go tidak punya aritmatika pointer, dan memori dikelola **garbage collector**. Aman mengembalikan alamat variabel lokal.

## Langkah
Buat `contoh-kode/08-pointer/main.go`:

```go
// File: contoh-kode/08-pointer/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/08-pointer
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Dasar pointer: & dan * ==========
x: 10 | p menyimpan alamat | *p: 10
x setelah *p = 99: 99
========== [2] Pass by value vs pointer (angka) ==========
setelah tambahSatuSalah: 5
setelah tambahSatu: 6
========== [3] Pass by value vs pointer (struct) ==========
setelah setorSalah: 100
setelah setor: 150
========== [4] new(T) ==========
7
========== [5] Pointer nil ==========
Zaki tidak ditemukan (pointer nil)
Umur Ani: 20
========== [6] Slice dan map berperilaku seperti referensi ==========
slice setelah ubahSlice: [100 2 3]
map setelah ubahMap: map[a:1 b:2]
========== [7] Dua pointer ke objek yang sama ==========
b1.Saldo: 500
```

## Kesalahan Umum
| Masalah | Solusi |
|---|---|
| `panic: runtime error: invalid memory address or nil pointer dereference` | Periksa `p != nil` sebelum `*p` |
| Struct tidak berubah setelah dikirim ke fungsi | Kirim pointer `&s` dan terima `*Struct` |
| Mencetak pointer menampilkan alamat | Gunakan `*p` untuk nilai |
| Terlalu banyak pointer "demi performa" | Mulai dengan nilai; pakai pointer bila perlu mengubah atau struct besar |

## Latihan
1. Fungsi `tukar(a, b *int)` yang menukar dua bilangan.
2. Fungsi `gandakan(s *[]int)` yang menggandakan isi slice.
3. Buat linked list sederhana (`type Node struct { Nilai int; Next *Node }`) dengan fungsi `TambahDiAkhir` dan `Cetak`.

## Catatan Instruktur
Estimasi 60 menit. Gambar kotak memori di papan tulis; ini topik yang paling sering membingungkan.
