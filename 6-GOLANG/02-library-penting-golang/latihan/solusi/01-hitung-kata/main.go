package main

import (
	"bufio"
	"encoding/json"
	"fmt"
	"os"
	"sort"
	"strings"
	"unicode"
)

type Hasil struct {
	TotalKata int            `json:"total_kata"`
	Frekuensi map[string]int `json:"frekuensi"`
	Terbanyak []string       `json:"terbanyak"`
}

func Hitung(r *bufio.Scanner) Hasil {
	freq := map[string]int{}
	total := 0
	for r.Scan() {
		// Pisahkan berdasarkan karakter yang bukan huruf/angka.
		kata := strings.FieldsFunc(strings.ToLower(r.Text()), func(c rune) bool {
			return !unicode.IsLetter(c) && !unicode.IsNumber(c)
		})
		for _, k := range kata {
			freq[k]++
			total++
		}
	}

	type pasangan struct {
		kata string
		n    int
	}
	var daftar []pasangan
	for k, n := range freq {
		daftar = append(daftar, pasangan{k, n})
	}
	sort.Slice(daftar, func(i, j int) bool {
		if daftar[i].n != daftar[j].n {
			return daftar[i].n > daftar[j].n
		}
		return daftar[i].kata < daftar[j].kata
	})

	var top []string
	for i := 0; i < len(daftar) && i < 3; i++ {
		top = append(top, fmt.Sprintf("%s (%d)", daftar[i].kata, daftar[i].n))
	}
	return Hasil{TotalKata: total, Frekuensi: freq, Terbanyak: top}
}

func main() {
	if len(os.Args) < 2 {
		fmt.Println("pemakaian: go run ./latihan/solusi/01-hitung-kata <file.txt>")
		os.Exit(1)
	}
	f, err := os.Open(os.Args[1])
	if err != nil {
		fmt.Println("gagal membuka file:", err)
		os.Exit(1)
	}
	defer f.Close()

	hasil := Hitung(bufio.NewScanner(f))
	out, _ := json.MarshalIndent(hasil, "", "  ")
	fmt.Println(string(out))
}
