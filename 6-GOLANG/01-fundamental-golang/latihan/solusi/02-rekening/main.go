package main

import (
	"errors"
	"fmt"
)

var (
	ErrJumlahTidakValid = errors.New("jumlah harus lebih dari 0")
	ErrSaldoKurang      = errors.New("saldo tidak cukup")
)

type Rekening struct {
	Pemilik string
	saldo   int
}

func NewRekening(pemilik string, saldoAwal int) *Rekening {
	return &Rekening{Pemilik: pemilik, saldo: saldoAwal}
}

func (r *Rekening) Saldo() int { return r.saldo }

func (r *Rekening) Setor(jumlah int) error {
	if jumlah <= 0 {
		return ErrJumlahTidakValid
	}
	r.saldo += jumlah
	return nil
}

func (r *Rekening) Tarik(jumlah int) error {
	if jumlah <= 0 {
		return ErrJumlahTidakValid
	}
	if jumlah > r.saldo {
		return fmt.Errorf("tarik %d dari saldo %d: %w", jumlah, r.saldo, ErrSaldoKurang)
	}
	r.saldo -= jumlah
	return nil
}

func main() {
	r := NewRekening("Andi", 100000)
	// [1] Setor
	fmt.Println("========== [1] Setor ==========")
	fmt.Println(r.Setor(50000), r.Saldo())
	// [2] Tarik berhasil
	fmt.Println("========== [2] Tarik berhasil ==========")
	fmt.Println(r.Tarik(30000), r.Saldo())

	// [3] Tarik gagal: saldo kurang
	fmt.Println("========== [3] Tarik gagal: saldo kurang ==========")
	err := r.Tarik(1000000)
	fmt.Println(err)
	fmt.Println("saldo kurang?", errors.Is(err, ErrSaldoKurang))
	// [4] Setor dengan jumlah tidak valid
	fmt.Println("========== [4] Setor dengan jumlah tidak valid ==========")
	fmt.Println(r.Setor(-5))
}
