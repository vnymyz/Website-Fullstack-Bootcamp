# 06 - Response Helper dan Utilitas

## Tujuan Pembelajaran
- Membuat format response JSON yang konsisten.
- Membuat fungsi bantu untuk password (bcrypt) dan token (JWT).

## Langkah

### 1. Response helper
Buat `backend/internal/utils/response.go`:

```go
// File: backend/internal/utils/response.go
package utils

import "github.com/gin-gonic/gin"

// Response adalah format JSON standar untuk semua endpoint.
type Response struct {
	Success bool   `json:"success"`
	Message string `json:"message"`
	Data    any    `json:"data,omitempty"`
}

// OK mengirim response sukses.
func OK(c *gin.Context, status int, message string, data any) {
	c.JSON(status, Response{Success: true, Message: message, Data: data})
}

// Fail mengirim response gagal.
func Fail(c *gin.Context, status int, message string) {
	c.JSON(status, Response{Success: false, Message: message})
}
```

Dengan helper ini semua endpoint membalas dengan bentuk yang sama, sehingga frontend mudah memprosesnya. `omitempty` membuat field `data` hilang jika `nil`.

### 2. Password
Buat `backend/internal/utils/password.go`:

```go
// File: backend/internal/utils/password.go
package utils

import "golang.org/x/crypto/bcrypt"

// HashPassword mengubah password polos menjadi hash bcrypt.
func HashPassword(password string) (string, error) {
	b, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	return string(b), err
}

// CheckPassword membandingkan password polos dengan hash.
func CheckPassword(hash, password string) bool {
	return bcrypt.CompareHashAndPassword([]byte(hash), []byte(password)) == nil
}
```

- **Hash** bukan enkripsi: tidak bisa dikembalikan menjadi password asli.
- `bcrypt` otomatis menambahkan *salt* acak, jadi dua user dengan password sama punya hash berbeda.
- Cek login dilakukan dengan `CompareHashAndPassword`, bukan membandingkan string.

### 3. JWT
Buat `backend/internal/utils/jwt.go`:

```go
// File: backend/internal/utils/jwt.go
package utils

import (
	"errors"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

// Claims adalah isi (payload) token JWT.
type Claims struct {
	UserID int64 `json:"user_id"`
	jwt.RegisteredClaims
}

// GenerateToken membuat token JWT yang ditandatangani dengan secret.
func GenerateToken(userID int64, secret string, expire time.Duration) (string, error) {
	claims := Claims{
		UserID: userID,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(expire)),
			IssuedAt:  jwt.NewNumericDate(time.Now()),
		},
	}
	return jwt.NewWithClaims(jwt.SigningMethodHS256, claims).SignedString([]byte(secret))
}

// ParseToken memverifikasi token dan mengembalikan isinya.
func ParseToken(tokenString, secret string) (*Claims, error) {
	token, err := jwt.ParseWithClaims(tokenString, &Claims{}, func(t *jwt.Token) (any, error) {
		return []byte(secret), nil
	}, jwt.WithValidMethods([]string{"HS256"}))
	if err != nil {
		return nil, err
	}
	claims, ok := token.Claims.(*Claims)
	if !ok || !token.Valid {
		return nil, errors.New("token tidak valid")
	}
	return claims, nil
}
```

**Penjelasan JWT**
Token JWT terdiri dari tiga bagian dipisah titik: `header.payload.signature`.
- **payload** berisi `user_id` dan waktu kedaluwarsa. Isinya hanya di-*encode*, **bukan** dienkripsi. Jangan menaruh data rahasia di dalamnya.
- **signature** dibuat dengan `JWT_SECRET`. Jika ada yang mengubah payload, signature tidak cocok dan token ditolak.
- `jwt.WithValidMethods([]string{"HS256"})` menolak token dengan algoritma lain (serangan *algorithm confusion*).

## Cek Hasil
```powershell
go build ./internal/utils
```

## Latihan
Buat file tes `backend/internal/utils/jwt_test.go` yang membuat token lalu mem-parse-nya, dan memastikan token dengan secret berbeda ditolak. (Contoh tes ada di materi 1, file `15-unit-testing-dasar.md`.)

## Catatan Instruktur
Estimasi 45 menit. Demo: tempel token di https://jwt.io untuk menunjukkan bahwa payload bisa dibaca siapa saja.
