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
