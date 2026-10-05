package main

import (
	"fmt"
	"math"
)

type Bentuk interface {
	Luas() float64
}

type Persegi struct{ Sisi float64 }
type Lingkaran struct{ R float64 }
type Segitiga struct{ Alas, Tinggi float64 }

func (p Persegi) Luas() float64   { return p.Sisi * p.Sisi }
func (l Lingkaran) Luas() float64 { return math.Pi * l.R * l.R }
func (s Segitiga) Luas() float64  { return 0.5 * s.Alas * s.Tinggi }

func TotalLuas(daftar []Bentuk) float64 {
	total := 0.0
	for _, b := range daftar {
		total += b.Luas()
	}
	return total
}

// Suhu memakai String() agar tampil sebagai 30°C
type Suhu float64

func (s Suhu) String() string { return fmt.Sprintf("%.0f°C", float64(s)) }

func main() {
	daftar := []Bentuk{Persegi{2}, Lingkaran{1}, Segitiga{3, 4}}
	fmt.Printf("Total luas: %.2f\n", TotalLuas(daftar))
	fmt.Println(Suhu(30))
}
