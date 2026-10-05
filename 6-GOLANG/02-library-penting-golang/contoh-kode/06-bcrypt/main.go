package main

import (
	"fmt"
	"log"
	"time"

	"golang.org/x/crypto/bcrypt"
)

func HashPassword(password string) (string, error) {
	b, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	return string(b), err
}

func CheckPassword(hash, password string) bool {
	return bcrypt.CompareHashAndPassword([]byte(hash), []byte(password)) == nil
}

func main() {
	password := "rahasia123"

	mulai := time.Now()
	hash1, err := HashPassword(password)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("Hash 1:", hash1)
	fmt.Println("Waktu hashing:", time.Since(mulai).Round(time.Millisecond), "(sengaja lambat)")

	// Password yang sama menghasilkan hash BERBEDA karena salt acak.
	hash2, _ := HashPassword(password)
	fmt.Println("Hash 2:", hash2)
	fmt.Println("Hash sama?", hash1 == hash2)

	// Verifikasi
	fmt.Println("Password benar ->", CheckPassword(hash1, password))
	fmt.Println("Password salah ->", CheckPassword(hash1, "salah"))

	// Cost lebih tinggi = lebih aman tetapi lebih lambat
	cost, _ := bcrypt.Cost([]byte(hash1))
	fmt.Println("Cost:", cost)
}
