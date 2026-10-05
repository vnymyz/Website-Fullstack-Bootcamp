# 15 - Unit Testing Dasar

## Tujuan Pembelajaran
- Menulis test dengan paket `testing`.
- Memakai pola **table-driven test** dan sub-test.
- Menjalankan test, melihat coverage, dan benchmark.

## Konsep
Test adalah kode yang memeriksa kode lain. Manfaat: mencegah regresi, menjadi dokumentasi, dan membuat berani mengubah kode.

Aturan Go:
| Aturan | Contoh |
|---|---|
| File berakhiran `_test.go` | `hitung_test.go` |
| Fungsi berawalan `Test` + huruf besar | `func TestBagi(t *testing.T)` |
| Package sama dengan kode yang diuji | `package hitung` |
| Benchmark: `BenchmarkXxx(b *testing.B)` | `for i := 0; i < b.N; i++` |

Method penting `*testing.T`:
| Method | Efek |
|---|---|
| `t.Errorf(...)` | Tandai gagal, **lanjut** |
| `t.Fatalf(...)` | Tandai gagal, **berhenti** |
| `t.Run("nama", func(t *testing.T){...})` | Sub-test |
| `t.Helper()` | Tandai fungsi bantu (baris error menunjuk pemanggil) |

### Table-driven test
Daftar kasus (input + hasil diharapkan) lalu satu loop. Menambah kasus baru = menambah satu baris.

### Prinsip
- Uji **perilaku**, bukan detail implementasi.
- Satu test, satu alasan gagal.
- Uji kasus tepi: kosong, nol, negatif, batas.
- Pesan gagal harus berisi **input, hasil, dan harapan**.

## Langkah
Buat dua file di folder `contoh-kode/15-testing/`.

`hitung.go`:

```go
// File: contoh-kode/15-testing/hitung.go
package hitung

import (
	"errors"
	"strings"
)

var ErrBagiNol = errors.New("pembagian dengan nol")

// Bagi membagi a dengan b.
func Bagi(a, b float64) (float64, error) {
	if b == 0 {
		return 0, ErrBagiNol
	}
	return a / b, nil
}

// Palindrom memeriksa apakah teks sama jika dibaca terbalik (abaikan huruf besar & spasi).
func Palindrom(s string) bool {
	s = strings.ToLower(strings.ReplaceAll(s, " ", ""))
	r := []rune(s)
	for i, j := 0, len(r)-1; i < j; i, j = i+1, j-1 {
		if r[i] != r[j] {
			return false
		}
	}
	return true
}

// Grade mengubah nilai angka menjadi huruf.
func Grade(nilai int) string {
	switch {
	case nilai < 0 || nilai > 100:
		return "invalid"
	case nilai >= 85:
		return "A"
	case nilai >= 70:
		return "B"
	case nilai >= 55:
		return "C"
	default:
		return "D"
	}
}
```

`hitung_test.go`:

```go
// File: contoh-kode/15-testing/hitung_test.go
package hitung

import (
	"errors"
	"testing"
)

// Test sederhana
func TestBagi(t *testing.T) {
	hasil, err := Bagi(10, 4)
	if err != nil {
		t.Fatalf("tidak diharapkan error: %v", err)
	}
	if hasil != 2.5 {
		t.Errorf("Bagi(10,4) = %v; diharapkan 2.5", hasil)
	}
}

func TestBagiNol(t *testing.T) {
	_, err := Bagi(1, 0)
	if !errors.Is(err, ErrBagiNol) {
		t.Errorf("diharapkan ErrBagiNol, dapat %v", err)
	}
}

// Table-driven test: pola idiomatik Go. Satu fungsi, banyak kasus.
func TestPalindrom(t *testing.T) {
	tests := []struct {
		nama       string
		input      string
		diharapkan bool
	}{
		{"kata palindrom", "katak", true},
		{"bukan palindrom", "golang", false},
		{"huruf campur dan spasi", "Kasur Rusak", true},
		{"string kosong", "", true},
		{"satu huruf", "a", true},
	}

	for _, tc := range tests {
		t.Run(tc.nama, func(t *testing.T) {
			if got := Palindrom(tc.input); got != tc.diharapkan {
				t.Errorf("Palindrom(%q) = %v; diharapkan %v", tc.input, got, tc.diharapkan)
			}
		})
	}
}

func TestGrade(t *testing.T) {
	tests := map[int]string{
		100: "A", 85: "A", 84: "B", 70: "B", 69: "C", 55: "C", 54: "D", 0: "D",
		-1: "invalid", 101: "invalid",
	}
	for nilai, diharapkan := range tests {
		if got := Grade(nilai); got != diharapkan {
			t.Errorf("Grade(%d) = %s; diharapkan %s", nilai, got, diharapkan)
		}
	}
}

// Benchmark: go test -bench=.
func BenchmarkPalindrom(b *testing.B) {
	for i := 0; i < b.N; i++ {
		Palindrom("Kasur Rusak")
	}
}
```

Jalankan:
```powershell
go test ./contoh-kode/15-testing
go test ./contoh-kode/15-testing -v
go test ./contoh-kode/15-testing -cover
go test ./contoh-kode/15-testing -bench=. -run=^$
```

## Output yang Diharapkan
```
ok  	fundamental/contoh-kode/15-testing	0.4s
```
Dengan `-v` terlihat tiap sub-test:
```
=== RUN   TestPalindrom/kata_palindrom
--- PASS: TestPalindrom/kata_palindrom (0.00s)
...
```

## Percobaan: Merusak Kode
Ubah di `hitung.go` kondisi `nilai >= 85` menjadi `nilai > 85`, jalankan `go test`, dan baca pesan gagalnya:
```
hitung_test.go:..: Grade(85) = B; diharapkan A
```
Kembalikan ke semula.

## Kesalahan Umum
| Masalah | Penyebab |
|---|---|
| `no test files` | Nama file tidak berakhiran `_test.go` |
| Test tidak jalan | Nama fungsi tidak diawali `Test` + huruf besar |
| Test saling bergantung | Setiap test harus mandiri |
| Membandingkan float dengan `==` | Pakai toleransi: `math.Abs(a-b) < 1e-9` |

## Latihan
1. Tulis test untuk fungsi `Faktorial` di `12-package/matematika` (kasus 0, 1, 5, negatif, 21).
2. Tambahkan kasus `"A man, a plan, a canal: Panama"` ke test Palindrom. Test gagal karena tanda baca; perbaiki fungsi `Palindrom` agar hanya memperhitungkan huruf dan angka (`unicode.IsLetter`, `unicode.IsDigit`).
3. Ukur coverage dan buka laporan HTML: `go test -coverprofile=c.out ./... ; go tool cover -html=c.out`.

## Catatan Instruktur
Estimasi 60 menit. Lanjutannya ada di materi 2 (`testify`, `httptest`). Tekankan: test adalah bagian dari pekerjaan, bukan tambahan.
