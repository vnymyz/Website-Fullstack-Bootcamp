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
