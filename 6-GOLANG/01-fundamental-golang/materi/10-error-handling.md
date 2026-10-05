# 10 - Error Handling

## Tujuan Pembelajaran
- Memahami filosofi error sebagai **nilai** di Go.
- Membuat, membungkus, dan memeriksa error dengan `errors.New`, `%w`, `errors.Is`, `errors.As`.
- Membuat custom error.

## Konsep
Go tidak memiliki `try/catch`. Fungsi yang mungkin gagal mengembalikan `error` sebagai nilai terakhir:

```go
hasil, err := lakukanSesuatu()
if err != nil {
    return err   // atau tangani
}
```

`error` hanyalah interface:
```go
type error interface { Error() string }
```

| Teknik | Fungsi |
|---|---|
| `errors.New("pesan")` | Membuat error sederhana |
| **Sentinel error** `var ErrX = errors.New(...)` | Error yang bisa dibandingkan |
| `fmt.Errorf("konteks: %w", err)` | **Membungkus** error dengan konteks (`%w`) |
| `errors.Is(err, ErrX)` | Apakah `err` (atau yang dibungkusnya) adalah `ErrX`? |
| `errors.As(err, &target)` | Apakah ada error bertipe tertentu dalam rantai? Ambil datanya |
| `errors.Join(e1, e2)` | Gabungkan beberapa error |
| Custom error | Struct dengan method `Error()` untuk membawa data tambahan |

### Aturan Emas
1. **Jangan abaikan error** (`_`), kecuali Anda yakin dan menuliskan alasannya.
2. **Tambahkan konteks** ketika meneruskan: `fmt.Errorf("simpan user: %w", err)`.
3. **Tangani sekali**: *log* ATAU *return*, bukan keduanya.
4. Pesan error huruf kecil, tanpa titik di akhir, mis. `gagal membuka file`.
5. Gunakan `errors.Is/As`, **jangan** membandingkan teks error.

## Langkah
Buat `contoh-kode/10-error/main.go`:

```go
// File: contoh-kode/10-error/main.go
package main

import (
	"errors"
	"fmt"
	"os"
	"strconv"
)

// 1. Sentinel error: nilai error yang dapat dibandingkan
var ErrSaldoKurang = errors.New("saldo tidak cukup")
var ErrAkunTidakAda = errors.New("akun tidak ditemukan")

// 2. Custom error: tipe sendiri dengan data tambahan
type ErrValidasi struct {
	Field string
	Pesan string
}

func (e *ErrValidasi) Error() string {
	return fmt.Sprintf("validasi gagal pada %s: %s", e.Field, e.Pesan)
}

var akun = map[string]int{"andi": 100000}

func tarik(nama string, jumlah int) error {
	if jumlah <= 0 {
		return &ErrValidasi{Field: "jumlah", Pesan: "harus lebih dari 0"}
	}
	saldo, ada := akun[nama]
	if !ada {
		return ErrAkunTidakAda
	}
	if saldo < jumlah {
		// 3. Membungkus error dengan konteks memakai %w
		return fmt.Errorf("tarik %d dari %s: %w", jumlah, nama, ErrSaldoKurang)
	}
	akun[nama] -= jumlah
	return nil
}

func bacaAngka(s string) (int, error) {
	n, err := strconv.Atoi(s)
	if err != nil {
		return 0, fmt.Errorf("bacaAngka(%q): %w", s, err)
	}
	return n, nil
}

func main() {
	// [1] Periksa error segera
	fmt.Println("========== [1] Periksa error segera ==========")
	// Pola utama Go: periksa error segera
	if _, err := bacaAngka("12x"); err != nil {
		fmt.Println("Error:", err)
	}

	// [2] errors.Is (sentinel error)
	fmt.Println("========== [2] errors.Is (sentinel error) ==========")
	// errors.Is: apakah error ini (atau yang dibungkusnya) sama dengan sentinel?
	err := tarik("andi", 500000)
	fmt.Println(err)
	fmt.Println("saldo kurang?", errors.Is(err, ErrSaldoKurang))

	err = tarik("zaki", 10)
	fmt.Println("akun tidak ada?", errors.Is(err, ErrAkunTidakAda))

	// [3] errors.As (custom error)
	fmt.Println("========== [3] errors.As (custom error) ==========")
	// errors.As: apakah error ini bertipe tertentu? (sekaligus mengambil datanya)
	err = tarik("andi", -5)
	var ev *ErrValidasi
	if errors.As(err, &ev) {
		fmt.Println("field bermasalah:", ev.Field, "|", ev.Pesan)
	}

	// [4] Kasus berhasil
	fmt.Println("========== [4] Kasus berhasil ==========")
	// Berhasil
	if err := tarik("andi", 1000); err == nil {
		fmt.Println("penarikan sukses, sisa:", akun["andi"])
	}

	// [5] Error dari standard library
	fmt.Println("========== [5] Error dari standard library ==========")
	// Error dari standard library: file tidak ada
	_, err = os.ReadFile("tidak-ada.txt")
	fmt.Println("file ada?", !errors.Is(err, os.ErrNotExist))

	// [6] errors.Join
	fmt.Println("========== [6] errors.Join ==========")
	// errors.Join: gabungkan beberapa error (Go 1.20+)
	gabungan := errors.Join(errors.New("error 1"), errors.New("error 2"))
	fmt.Println(gabungan)

	// Aturan emas: jangan abaikan error (jangan lakukan: n, _ := strconv.Atoi(...) untuk input pengguna)
	// Aturan emas 2: tambahkan konteks saat meneruskan error ke atas (fmt.Errorf + %w)
	// Aturan emas 3: tangani error SEKALI saja (log ATAU kembalikan, jangan keduanya)
}
```

Jalankan:
```powershell
go run ./contoh-kode/10-error
```

## Output yang Diharapkan

Setiap baris `========== [N] judul ==========` menandai awal satu kelompok output. Cocokkan nomor `[N]` dengan komentar `// [N]` di kode.

```
========== [1] Periksa error segera ==========
Error: bacaAngka("12x"): strconv.Atoi: parsing "12x": invalid syntax
========== [2] errors.Is (sentinel error) ==========
tarik 500000 dari andi: saldo tidak cukup
saldo kurang? true
akun tidak ada? true
========== [3] errors.As (custom error) ==========
field bermasalah: jumlah | harus lebih dari 0
========== [4] Kasus berhasil ==========
penarikan sukses, sisa: 99000
========== [5] Error dari standard library ==========
file ada? false
========== [6] errors.Join ==========
error 1
error 2
```

## Penjelasan Penting
- `%w` menjaga rantai error sehingga `errors.Is` dan `errors.As` tetap bekerja. `%v` hanya menyalin teksnya.
- Di project materi 3-4, `sql.ErrNoRows` dipetakan ke `ErrNotFound`, dan handler menerjemahkannya menjadi HTTP 404.

## Kesalahan Umum
| Masalah | Solusi |
|---|---|
| `if err.Error() == "..."` | Pakai `errors.Is` |
| Memakai `%v` saat membungkus | Pakai `%w` |
| Error ditulis log lalu di-return lagi | Pilih salah satu |
| `panic` untuk error biasa | Return error |

## Latihan
1. Buat fungsi `Registrasi(nama, email string) error` yang mengembalikan custom error `ErrValidasi` untuk nama kosong atau email tanpa `@`.
2. Buat fungsi yang membaca file lalu mengubah isinya menjadi angka, dengan error yang dibungkus tiap lapisan; di `main` periksa dengan `errors.Is(err, os.ErrNotExist)`.
3. Kumpulkan semua error validasi dengan `errors.Join` dan cetak semuanya sekaligus.

## Catatan Instruktur
Estimasi 75 menit. Siswa dari Java/Python akan mengeluh soal `if err != nil`; jelaskan bahwa alur kegagalan menjadi **eksplisit dan terlihat**.
