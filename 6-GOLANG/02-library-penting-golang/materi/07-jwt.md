# 07 - JWT (JSON Web Token)

## Tujuan Pembelajaran
- Memahami struktur dan tujuan JWT.
- Membuat dan memverifikasi token dengan `golang-jwt/jwt/v5`.
- Menangani token salah dan kedaluwarsa.

## Konsep
Setelah login, server perlu mengenali user pada request berikutnya. HTTP itu **stateless** (tidak mengingat). Solusi: server memberi **token** yang dibawa client di setiap request.

JWT = `header.payload.signature` (tiga bagian base64url dipisah titik).

```
eyJhbGciOiJIUzI1NiJ9 . eyJ1c2VyX2lkIjo3fQ . SKHEKJZwx...
      header                  payload            signature
  {"alg":"HS256"}      {"user_id":7,"exp":...}   HMAC(header.payload, secret)
```

- **payload** (*claims*): data seperti `user_id`, `exp` (kedaluwarsa), `iat`. **Hanya di-encode, tidak dienkripsi** -> jangan taruh rahasia di dalamnya. Anda bisa melihatnya di https://jwt.io.
- **signature**: dihitung dengan secret. Jika payload diubah, signature tidak cocok -> ditolak.

Alur:
```
Login ok -> server buat JWT -> client simpan -> setiap request: Authorization: Bearer <token>
                                                  server verifikasi signature + exp
```

### Hal penting
| Topik | Penjelasan |
|---|---|
| Secret | Panjang dan acak (>= 32 karakter), simpan di `.env` |
| Masa berlaku | Singkat (mis. 15 menit - 24 jam). Token tidak bisa dicabut sebelum kedaluwarsa kecuali ada daftar blokir |
| Algoritma | Batasi dengan `WithValidMethods` (cegah *algorithm confusion*, mis. `alg: none`) |
| Penyimpanan di browser | `localStorage` mudah tetapi rentan XSS; cookie `httpOnly` lebih aman |

### Pasang
```powershell
go get github.com/golang-jwt/jwt/v5
```

## Langkah
Buat `contoh-kode/07-jwt/main.go`:

```go
// File: contoh-kode/07-jwt/main.go
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
```

Jalankan:
```powershell
go run ./contoh-kode/07-jwt
```

## Output yang Diharapkan
```
Token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjo3LCJyb2xl...
Jumlah bagian (header.payload.signature): 3
Valid -> user_id=7 role=admin err=<nil>
Secret salah -> token signature is invalid: signature is invalid
Kedaluwarsa -> token has invalid claims: token is expired | errors.Is(ErrTokenExpired): true
Dimodifikasi -> token signature is invalid: signature is invalid
```
Token Anda berbeda karena berisi waktu saat dibuat.

## Penjelasan
- `Claims` menanamkan `jwt.RegisteredClaims` (field standar: `exp`, `iat`, `iss`, ...) dan menambah field kita sendiri (`UserID`, `Role`).
- `ParseWithClaims` memverifikasi signature **dan** masa berlaku sekaligus.
- Menyalin payload dari token, mengubah `role` menjadi `admin`, lalu memasangnya lagi akan gagal karena signature tidak cocok. Silakan coba di jwt.io.

## Kesalahan Umum
- Secret lemah/di-hardcode di kode.
- Tidak mengatur `exp` -> token berlaku selamanya.
- Menaruh data sensitif (password, nomor kartu) di payload.

## Latihan
Tambahkan middleware Gin yang membaca header `Authorization: Bearer ...` dan menolak dengan 401 jika token tidak valid (versi lengkapnya ada di project materi 4).

## Catatan Instruktur
Estimasi 60 menit. Demo di jwt.io: tempel token, tunjukkan payload terbaca jelas.
