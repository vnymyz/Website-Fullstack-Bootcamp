package main

import (
	"errors"
	"fmt"
	"strings"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

type Claims struct {
	UserID int64  `json:"user_id"`
	Role   string `json:"role"`
	jwt.RegisteredClaims
}

func Buat(userID int64, role, secret string, berlaku time.Duration) (string, error) {
	claims := Claims{
		UserID: userID,
		Role:   role,
		RegisteredClaims: jwt.RegisteredClaims{
			Issuer:    "kelas-go",
			IssuedAt:  jwt.NewNumericDate(time.Now()),
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(berlaku)),
		},
	}
	return jwt.NewWithClaims(jwt.SigningMethodHS256, claims).SignedString([]byte(secret))
}

func Verifikasi(tokenStr, secret string) (*Claims, error) {
	token, err := jwt.ParseWithClaims(tokenStr, &Claims{}, func(t *jwt.Token) (any, error) {
		return []byte(secret), nil
	}, jwt.WithValidMethods([]string{"HS256"})) // tolak algoritma lain
	if err != nil {
		return nil, err
	}
	return token.Claims.(*Claims), nil
}

func main() {
	secret := "secret-yang-panjang-dan-acak"

	token, err := Buat(7, "admin", secret, time.Hour)
	if err != nil {
		panic(err)
	}
	fmt.Println("Token:", token)
	fmt.Println("Jumlah bagian (header.payload.signature):", len(strings.Split(token, ".")))

	// 1. Token valid
	c, err := Verifikasi(token, secret)
	fmt.Printf("Valid -> user_id=%d role=%s err=%v\n", c.UserID, c.Role, err)

	// 2. Secret salah
	_, err = Verifikasi(token, "secret-salah")
	fmt.Println("Secret salah ->", err)

	// 3. Token kedaluwarsa
	kadaluwarsa, _ := Buat(7, "admin", secret, -time.Minute)
	_, err = Verifikasi(kadaluwarsa, secret)
	fmt.Println("Kedaluwarsa ->", err, "| errors.Is(ErrTokenExpired):", errors.Is(err, jwt.ErrTokenExpired))

	// 4. Token dimodifikasi
	rusak := token[:len(token)-3] + "abc"
	_, err = Verifikasi(rusak, secret)
	fmt.Println("Dimodifikasi ->", err)
}
