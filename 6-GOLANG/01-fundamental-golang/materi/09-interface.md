# 09 - Interface

## Tujuan Pembelajaran
- Memahami interface sebagai kontrak perilaku.
- Memahami bahwa interface di Go bersifat **implisit**.
- Memakai `any`, type assertion, dan type switch.

## Konsep
**Interface** menyatakan "apa yang bisa dilakukan", bukan "apa itu".

```go
type Bentuk interface {
    Luas() float64
    Keliling() float64
}
```

Tipe apa pun yang punya method `Luas()` dan `Keliling()` **otomatis** memenuhi `Bentuk`, tanpa kata `implements`. Ini disebut *structural typing*.

Manfaat:
- **Polimorfisme**: satu fungsi menerima banyak tipe.
- **Decoupling**: kode bergantung pada kontrak, bukan implementasi (mudah diganti, mudah dites dengan mock).

### Pedoman
1. **Interface kecil** (1-3 method). Contoh standar: `io.Reader`, `io.Writer`, `error`, `fmt.Stringer`.
2. "**Accept interfaces, return structs**": parameter berupa interface, nilai balik berupa tipe konkret.
3. Definisikan interface di sisi **pemakai**, bukan di sisi pembuat.

### Interface standar yang penting
| Interface | Method |
|---|---|
| `error` | `Error() string` |
| `fmt.Stringer` | `String() string` (mengatur hasil cetak) |
| `io.Reader` / `io.Writer` | `Read` / `Write` |
| `sort.Interface` | `Len`, `Less`, `Swap` |

### `any`
`any` (alias `interface{}`) dapat menampung nilai apa pun. Gunakan seperlunya karena kehilangan keamanan tipe. Untuk mengetahui tipe aslinya:
- **Type assertion**: `v, ok := x.(Persegi)`
- **Type switch**: `switch v := x.(type) { case int: ... }`

### Gotcha: interface bernilai nil
Interface berisi pasangan (tipe, nilai). Interface yang menyimpan **pointer nil bertipe** tidak sama dengan `nil`. Jangan mengembalikan `*MyErr(nil)` sebagai `error`; kembalikan `nil` literal.

## Langkah
Buat `contoh-kode/09-interface/main.go`:

```go
// File: contoh-kode/09-interface/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/09-interface
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Interface Bentuk (polimorfisme) ==========
main.Persegi | luas 16.00 | keliling 16.00
main.Lingkaran | luas 12.57 | keliling 12.57
========== [2] Method String() mengatur tampilan ==========
Persegi(3)
========== [3] Satu interface, banyak implementasi ==========
[LOG] nama=Budi
========== [4] any dan type switch ==========
int 42
string halo
slice int panjang 2
bentuk luas 3.1
tipe lain float64
nil
========== [5] Type assertion ==========
ini Persegi, sisi: 2
bukan Lingkaran
```

## Kesalahan Umum
| Masalah | Penyebab |
|---|---|
| `Persegi does not implement Bentuk (missing method ...)` | Method belum lengkap atau receiver salah (pointer vs nilai) |
| `panic: interface conversion` | Type assertion tanpa `, ok` pada tipe salah |
| Interface raksasa | Pecah menjadi interface kecil |

## Latihan
1. Tambahkan `Segitiga` ke contoh dan hitung total luas semua `Bentuk` dalam slice.
2. Buat interface `Notifier` dengan `Kirim(pesan string) error`, implementasi `Email` dan `SMS`, dan fungsi `Peringatkan(n Notifier)`.
3. Buat tipe `Suhu` dengan method `String()` sehingga `fmt.Println(Suhu(30))` mencetak `30°C`.

## Catatan Instruktur
Estimasi 75 menit. Hubungkan ke materi 3 (repository pattern memakai interface) dan project (mock di test).
