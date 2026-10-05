package main

import (
	"fmt"
	"os"
)

// defer: tunda eksekusi sampai fungsi selesai. Beberapa defer berjalan LIFO (terbalik).
func contohDefer() {
	fmt.Println("mulai")
	defer fmt.Println("defer 1")
	defer fmt.Println("defer 2")
	defer fmt.Println("defer 3")
	fmt.Println("selesai")
	// Output: mulai, selesai, defer 3, defer 2, defer 1
}

// Pemakaian utama defer: membersihkan sumber daya (file, koneksi, lock)
func tulisFile(nama, isi string) error {
	f, err := os.Create(nama)
	if err != nil {
		return err
	}
	defer f.Close() // dijamin dijalankan, di jalur manapun fungsi keluar

	_, err = f.WriteString(isi)
	return err
}

// Argumen defer dihitung SAAT defer didaftarkan
func argumenDefer() {
	x := 1
	defer fmt.Println("nilai x saat defer didaftarkan:", x)
	x = 100
}

// panic: kondisi fatal. Menghentikan alur normal, menjalankan semua defer, lalu program crash.
// recover: menangkap panic (hanya bekerja di dalam fungsi yang di-defer).
func amanBagi(a, b int) (hasil int, err error) {
	defer func() {
		if r := recover(); r != nil {
			err = fmt.Errorf("pulih dari panic: %v", r)
		}
	}()
	return a / b, nil // b == 0 -> panic: integer divide by zero
}

func akses(slice []int, i int) (v int, err error) {
	defer func() {
		if r := recover(); r != nil {
			err = fmt.Errorf("index di luar batas: %v", r)
		}
	}()
	return slice[i], nil
}

func main() {
	// [1] defer berjalan terbalik (LIFO)
	fmt.Println("========== [1] defer berjalan terbalik (LIFO) ==========")
	contohDefer()

	// [2] defer untuk menutup file dan argumen defer
	fmt.Println("========== [2] defer untuk menutup file dan argumen defer ==========")

	if err := tulisFile("tmp-defer.txt", "halo"); err != nil {
		fmt.Println("error:", err)
	}
	os.Remove("tmp-defer.txt")

	argumenDefer()

	// [3] recover menangkap panic
	fmt.Println("========== [3] recover menangkap panic ==========")

	fmt.Println(amanBagi(10, 2))
	fmt.Println(amanBagi(10, 0))
	fmt.Println(akses([]int{1, 2, 3}, 5))

	// [4] panic (program sengaja berhenti)
	fmt.Println("========== [4] panic (program sengaja berhenti) ==========")
	// Kapan memakai panic? Hampir tidak pernah untuk error biasa (pakai return error).
	// Hanya untuk kesalahan programmer atau kondisi yang tidak mungkin pulih
	// (mis. konfigurasi wajib hilang saat startup).
	defer fmt.Println("defer di main tetap berjalan sebelum crash")
	panic("sesuatu yang fatal terjadi")
}
