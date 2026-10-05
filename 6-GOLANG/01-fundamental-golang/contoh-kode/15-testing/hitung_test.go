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
